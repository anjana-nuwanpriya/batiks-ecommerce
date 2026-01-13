<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Models\OrderInfo;
use App\Models\Product;
use App\Models\Weight;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\AuthWeb\RegisterController;
use App\Mail\OrderInvoiceMail;
use App\Models\PhoneVerification;
use App\Models\User;
use App\Services\Payhere;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use stdClass;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     *
     * @return \Illuminate\View\View
     */
    public function checkout()
    {

        if (empty(session()->get('cart'))) {
            return redirect()->route('home');
        }

        $addresses = (object)array();
        if (Auth::check()) {
            $user = Auth::user();
            $addresses = $user->addresses;
        }

        $cartItems = [];
        $shippingCost = 0;
        $shippingWeight = 0;
        $cartTotal = 0;

        if (session()->has('cart')) {
            $cart = session()->get('cart');

            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                $stock = $product->stocks()->where('id', $details['variant'])->first();
                if ($product->is_free_shipping == false) {
                    $shippingWeight += $stock->weight * $details['quantity'];
                }

                $subtotal = $product->cartPrice($stock->id, $details['quantity']);
                $cartTotal += $subtotal;

                $cartItems[] = [
                    'product' => $product,
                    'variant' => $stock->variant,
                    'quantity' => $details['quantity'],
                    'price' => $product->cartPrice($stock->id),
                    'stock' => $stock->qty,
                    'subtotal' => $subtotal
                ];
            }
        }

        if ($shippingWeight > 0) {
            $shippingCost = $this->calculateShippingCost();
        }

        // Check COD availability
        $codAvailable = $this->isCodAvailable($cartTotal, $shippingWeight);

        // Convert shipping weight from grams to kg for display
        $shippingWeightKg = $shippingWeight / 1000;

        return view('frontend.checkout', compact('addresses', 'cartItems', 'shippingCost', 'codAvailable', 'shippingWeightKg'));
    }

    public function process(Request $request)
    {
        $cart = session()->get('cart');

        if (empty($cart)) {
            toast()->error('Your cart is empty');
            return redirect()->route('home');
        }

        // ✅ Validate stock before proceeding
        foreach ($cart as $productId => $details) {
            $product = Product::find($productId);
            $stock = $product?->stocks()->where('id', $details['variant'])->first();

            if (!$product || !$stock || $stock->qty < $details['quantity']) {
                toast()->error("Sorry, {$product->name} is out of stock or has insufficient quantity.");
                return redirect()->back();
            }
        }

        // ✅ Validate COD availability if COD is selected
        if ($request->payment_method === 'COD') {
            $cartTotal = getCartTotalAmount();
            $shippingWeight = 0;

            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                $stock = $product->stocks()->where('id', $details['variant'])->first();
                if ($product->is_free_shipping == false) {
                    $shippingWeight += $stock->weight * $details['quantity'];
                }
            }

            if (!$this->isCodAvailable($cartTotal, $shippingWeight)) {
                toast()->error('Cash on Delivery is not available for this order due to amount or weight restrictions.');
                return redirect()->back();
            }
        }
        $shippingAddress = $this->buildShippingAddress($request);

        if ($shippingAddress instanceof RedirectResponse) {
            return $shippingAddress;
        }

        $parcelDescription = '';
        $order = DB::transaction(function () use ($request, $shippingAddress, $cart, &$parcelDescription) {
            $order = new Order();
            $order->user_id = Auth::id();
            $order->shipping_address = json_encode($shippingAddress);
            $order->code = 'ORD-' . rand(100000, 999999);
            $order->payment_method = $request->payment_method;
            $order->shipping_cost = $this->calculateShippingCost();

            $subtotal = getCartTotalAmount();
            $total = $subtotal + $order->shipping_cost;

            if ($request->payment_method === 'payhere') {
                $fee = env('PAYHERE_CONVENIENCE_FEE', 0);
                $handling_fee = ($total * $fee / 100);
                $order->handling_fee = $handling_fee;
                $total += $handling_fee;
            }

            $order->grand_total = $total;
            $order->note = $request->order_notes;
            $order->new_order_notification = true;
            $order->save();

            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                $stock = $product->stocks()->where('id', $details['variant'])->first();

                $parcelDescription .= "{$product->name} ({$stock->variant}) x {$details['quantity']}, ";

                $shippingCost = $this->calculateProductWiseShippingCost($product->id);

                $orderItem = new OrderInfo();
                $orderItem->fill([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant' => $stock->variant,
                    'quantity' => $details['quantity'],
                    'cost' => $stock->purchase_price,
                    'unit_price' => $product->cartPrice($stock->id),
                    'total_price' => $product->cartPrice($stock->id, $details['quantity']),
                    'weight' => $stock->weight * $details['quantity'],
                    'weight_cost' => $shippingCost,
                ])->save();


                $activeFlashDeal = $product->getActiveFlashDealItem();
                if ($activeFlashDeal) {
                    $activeFlashDeal->decrement('quantity', $details['quantity']);
                } else {
                    $stock->decrement('qty', $details['quantity']);
                }
            }

            return $order;
        });

        // ✅ Payment Method Handling
        if ($request->payment_method === 'payhere') {
            return $this->processPayHere($order, $shippingAddress, $parcelDescription);
        }

        if ($request->payment_method == 'COD') {
            $this->orderNotify($order->id);
        }

        session()->forget('cart');

        return redirect()->route('cart.order.complete', $order->id);
    }

    private function buildShippingAddress(Request $request)
    {

        if (Auth::check()) {
            $request->validate([
                'terms_accepted' => 'required|accepted',
                'address_id' => 'required',
                'payment_method' => 'required',
                'user_phone' => ['required', function ($attr, $value, $fail) {
                    if (!phoneNumberValidation($value)) {
                        $fail('Please enter a valid Sri Lankan phone number.');
                    }

                    // Check if user has verified their phone number
                    $phone = phoneNumberValidation($value);
                    if (Auth::check() && empty(Auth::user()->phone_verified_at)) {
                        if (!PhoneVerification::isPhoneVerified($phone)) {
                            $fail('Please verify your phone number before proceeding with checkout.');
                        }
                    }
                }],
            ]);

            $address = Address::findOrFail($request->address_id);

            // Use the verified phone number from the request or user's verified phone
            $phone = phoneNumberValidation($request->user_phone);
            if (empty(Auth::user()->phone_verified_at)) {
                // Phone verification required, use the phone from request
                $phone = phoneNumberValidation($request->user_phone);
            } else {
                // User already has verified phone, use it
                $phone = Auth::user()->phone;
            }

            return [
                'name' => Auth::user()->name,
                'phone' => $phone,
                'address' => $address->address,
                'city' => $address->city,
                'state' => $address->state,
                'country' => $address->country,
                'postal_code' => $address->postal_code,
            ];
        }


        $request->validate([
            'terms_accepted' => 'required|accepted',
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => ['required', function ($attr, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }

                // Check if phone number is verified
                $phone = phoneNumberValidation($value);
                if (!PhoneVerification::isPhoneVerified($phone)) {
                    $fail('Please verify your phone number before proceeding with checkout.');
                }
            }],
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country' => 'required',
            'zip_code' => 'required',
            'payment_method' => 'required',
        ]);



        $request->merge(['phone' => phoneNumberValidation($request->phone)]);

        if ($request->create_account === 'on') {
            $request->validate([
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:6|confirmed',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'created_by' => 'self',
            ]);

            Address::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'country' => $request->country,
                'zip_code' => $request->zip_code,
            ]);

            Auth::login($user);
        }

        return [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'country' => $request->country,
            'postal_code' => $request->zip_code,
            'guest' => !Auth::check(),
        ];
    }

    // private function handleBankPayment($order, $shippingAddress, $parcelDescription)
    // {
    //     try {
    //         $data = [
    //             'order_id' => $order->id,
    //             'parcel_weight' => $order->items()->sum('weight') / 1000,
    //             'parcel_description' => trim($parcelDescription, ', '),
    //             'recipient_name' => Auth::check() ? Auth::user()->name : $shippingAddress['name'],
    //             'recipient_contact_1' => $shippingAddress['phone'],
    //             'recipient_contact_2' => '',
    //             'recipient_address' => $shippingAddress['address'],
    //             'recipient_city' => $shippingAddress['city'],
    //             'amount' => $order->grand_total,
    //             'exchange' => 0,
    //         ];

    //         $promptAPTService = new PromptAPTService();
    //         $response = $promptAPTService->createDeliveryOrder($data);

    //         if (isset($response['status']) && $response['status'] == 200 && isset($response['waybill_no'])) {
    //             $order->waybill_no = $response['waybill_no'];
    //             $order->payment_status = 'pending'; // Bank payment requires manual verification
    //             $order->save();
    //             return true;
    //         }

    //         return false;
    //     } catch (\Exception $e) {
    //         Log::error('Bank payment waybill creation failed', [
    //             'order_id' => $order->id,
    //             'error' => $e->getMessage()
    //         ]);
    //         return false;
    //     }
    // }

    private function processPayHere($order, $shippingAddress, $parcelDescription)
    {
        try {
            $orderData = $this->preparePayHereData($order, $shippingAddress);
            $payhere = new Payhere();
            $payment = $payhere->createPayment($orderData);

            session()->forget('cart');
            return view('frontend.payments.payhere-form', ['payment' => $payment]);
        } catch (\Exception $e) {
            Log::error('PayHere payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            // Retry payment creation even if something failed
            try {
                $orderData = $this->preparePayHereData($order, $shippingAddress);
                $payhere = new Payhere();
                $payment = $payhere->createPayment($orderData);

                session()->forget('cart');
                return view('frontend.payments.payhere-form', ['payment' => $payment]);
            } catch (\Exception $ex) {
                Log::critical('Second PayHere attempt failed', [
                    'order_id' => $order->id,
                    'error' => $ex->getMessage()
                ]);
                abort(500, 'Payment could not be processed at this time.');
            }
        }
    }

    /**
     * Prepare data for PayHere payment.
     *
     * @param $order
     * @param $shippingAddress
     * @return array
     */

    private function preparePayHereData($order, $shippingAddress)
    {
        $items = $order->items->map(function ($item) {
            return $item->product->name . ' - ' . $item->variant;
        })->implode(', ');

        return [
            'first_name' => Auth::check() ? Auth::user()->name : $shippingAddress['name'],
            'last_name' => 'X',
            'email' => Auth::check() ? Auth::user()->email : $shippingAddress['email'],
            'phone' => $shippingAddress['phone'],
            'address' => $shippingAddress['address'],
            'city' => $shippingAddress['city'],
            'country' => $shippingAddress['country'],
            'order_id' => $order->id,
            'items' => $items,
            'amount' => $order->grand_total,
        ];
    }


    /**
     * Calculate the shipping cost.
     *
     * @return float
     */
    public function calculateShippingCost()
    {
        $cart = session()->get('cart');
        $shippingCost = 0;
        $shippingWeight = 0;

        //check if first order
        // if (Auth::check()) {
        //     $user = Auth::user();
        //     $orderCount = $user->orders()->where('payment_status', 'paid')->count();

        //     if ($orderCount == 0) {
        //         // First order, set shipping cost to 0
        //         return $shippingCost;
        //     }
        // }

        // Calculate total shipping weight
        foreach ($cart as $productId => $details) {
            $product = Product::find($productId);
            $stock = $product->stocks()->where('id', $details['variant'])->first();
            if ($product->is_free_shipping == false) {
                $shippingWeight += $stock->weight * $details['quantity'];
            }
        }

        if (get_setting('shipping_type') == 'flat_rate') {
            $shippingCost += get_setting('shipping_flat_rate');
        } else if (get_setting('shipping_type') == 'free_shipping') {
            $shippingCost = 0;
        } else if (get_setting('shipping_type') == 'product_wise') {
            $weightRanges = Weight::get();
            $shippingCost = 0;
            $foundMatchingRange = false;

            foreach ($weightRanges as $weightRange) {
                // Check if this is a range entry (e.g. "0-1000")
                if (strpos($weightRange->weight, '-') !== false) {
                    $range = explode('-', $weightRange->weight);
                    $min = (float)$range[0];
                    $max = (float)$range[1];

                    // If weight is within this range, use its price
                    if ($shippingWeight >= $min && $shippingWeight <= $max) {
                        $shippingCost = $weightRange->price;
                        $foundMatchingRange = true;
                        break;
                    }
                }
            }

            // If no matching range found, use the last entry's price plus per kg cost
            if (!$foundMatchingRange && count($weightRanges) > 0) {
                // Find the highest range
                $highestMax = 0;
                $basePrice = 0;

                foreach ($weightRanges as $weightRange) {
                    if (strpos($weightRange->weight, '-') !== false) {
                        $range = explode('-', $weightRange->weight);
                        $max = (float)$range[1];

                        if ($max > $highestMax) {
                            $highestMax = $max;
                            $basePrice = $weightRange->price;
                        }
                    }
                }

                // Set base shipping cost based on the highest range
                $shippingCost = $basePrice;

                $excessWeight = $shippingWeight - $highestMax;
                if ($excessWeight > 0) {
                    $excessWeightInKg = ceil($excessWeight / 1000);
                    $shippingCost += ($excessWeightInKg * get_setting('shipping_additional_cost'));
                }
            }
        }

        return $shippingCost;
    }

    public function calculateProductWiseShippingCost($product_id)
    {
        $shippingCost = 0;
        $shippingWeight = 0;

        $product = Product::find($product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        if (!$product->is_free_shipping) {
            $shippingWeight = $product->weight; // in grams
        }

        $shippingType = get_setting('shipping_type');

        if ($shippingType === 'flat_rate') {
            $shippingCost = (float) get_setting('shipping_flat_rate');
        } elseif ($shippingType === 'free_shipping') {
            $shippingCost = 0;
        } elseif ($shippingType === 'product_wise') {
            $weightRanges = Weight::all();
            $foundMatchingRange = false;

            foreach ($weightRanges as $range) {
                if (strpos($range->weight, '-') !== false) {
                    [$min, $max] = explode('-', $range->weight);
                    $min = (float) $min;
                    $max = (float) $max;

                    if ($shippingWeight >= $min && $shippingWeight <= $max) {
                        $shippingCost = $range->price;
                        $foundMatchingRange = true;
                        break;
                    }
                }
            }

            if (!$foundMatchingRange && $weightRanges->count() > 0) {
                // Find highest range
                $highestMax = 0;
                $basePrice = 0;

                foreach ($weightRanges as $range) {
                    if (strpos($range->weight, '-') !== false) {
                        [$_, $max] = explode('-', $range->weight);
                        $max = (float) $max;

                        if ($max > $highestMax) {
                            $highestMax = $max;
                            $basePrice = $range->price;
                        }
                    }
                }

                $shippingCost = $basePrice;

                $excessWeight = $shippingWeight - $highestMax;
                if ($excessWeight > 0) {
                    $excessWeightInKg = ceil($excessWeight / 1000);
                    $shippingCost += ($excessWeightInKg * get_setting('shipping_additional_cost')); // 60 per kg
                }
            }
        }

        return $shippingCost;
    }

    /**
     * Check if COD payment method is available based on amount and weight limits
     *
     * @param float $cartTotal
     * @param float $shippingWeight (in grams)
     * @return bool
     */
    private function isCodAvailable($cartTotal, $shippingWeight)
    {
        // Get COD settings from database
        $codMaxAmount = get_setting('cod_max_amount');
        $maxKgCodValue = get_setting('max_kg_cod_value'); // This is in grams as per your comment

        // Check if COD is disabled due to amount limit
        if ($codMaxAmount && $cartTotal > $codMaxAmount) {
            return false;
        }

        // Check if COD is disabled due to weight limit
        if ($maxKgCodValue && $shippingWeight > $maxKgCodValue) {
            return false;
        }

        return true;
    }

    public function orderComplete($orderId)
    {
        $order = Order::find($orderId);
        return view('frontend.order-complete', compact('order'));
    }

    public function orderCancel($orderId)
    {
        $order = Order::find($orderId);
        $order->payment_status = 'failed';
        $order->save();
        $this->reverseStock($orderId);
        toast()->error('Order cancelled');
        return redirect()->route('home');
    }

    /**
     * Send order notifications to customer via SMS and email
     *
     * @param int $orderId
     * @return void
     */
    public function orderNotify($orderId)
    {
        $order = Order::find($orderId);

        $notificationService = new NotificationService();
        $results = $notificationService->sendOrderNotification($order);

        // Log notification results
        Log::info('Order notifications sent', [
            'order_id' => $orderId,
            'email_sent' => $results['email'],
            'sms_sent' => $results['sms'],
            'user_data' => $results['user_data']
        ]);
    }


    public function getShippingCostForSummary()
    {
        $paymentMethod = request()->payment_method;

        // Calculate shipping cost and weight
        $shippingCost = $this->calculateShippingCost();
        $total = getCartTotalAmount() + $shippingCost;

        // Calculate shipping weight
        $shippingWeight = 0;
        if (session()->has('cart')) {
            $cart = session()->get('cart');
            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                $stock = $product->stocks()->where('id', $details['variant'])->first();
                if ($product->is_free_shipping == false) {
                    $shippingWeight += $stock->weight * $details['quantity'];
                }
            }
        }

        $shippingWeightKg = $shippingWeight / 1000;

        return response()->json([
            'raw_shipping_cost' => $shippingCost,
            'shipping_cost' => $shippingCost == 0 ? 'Free' : formatCurrency($shippingCost),
            'shipping_cost_formatted' => $shippingCost == 0 ? '<span class="badge bg-success">Free</span>' : formatCurrency($shippingCost),
            'shipping_weight_kg' => number_format($shippingWeightKg, 2),
            'has_shipping_cost' => $shippingCost > 0,
            'has_shipping_weight' => $shippingWeightKg > 0,
            'total' => formatCurrency($total),
        ]);
    }



    /**
     * Reverse stock quantities for an order
     * This function restores stock quantities when an order is cancelled or refunded
     *
     * @param int $orderId
     * @return bool
     */
    public function reverseStock($orderId)
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                Log::error('Order not found for stock reversal', ['order_id' => $orderId]);
                return false;
            }

            DB::transaction(function () use ($order) {
                foreach ($order->items as $orderItem) {
                    $product = Product::find($orderItem->product_id);

                    if (!$product) {
                        Log::warning('Product not found during stock reversal', [
                            'product_id' => $orderItem->product_id,
                            'order_id' => $order->id
                        ]);
                        continue;
                    }

                    // Find the stock variant
                    $stock = $product->stocks()->where('variant', $orderItem->variant)->first();

                    if (!$stock) {
                        Log::warning('Stock variant not found during stock reversal', [
                            'product_id' => $orderItem->product_id,
                            'variant' => $orderItem->variant,
                            'order_id' => $order->id
                        ]);
                        continue;
                    }

                    // Check if this was a flash deal order
                    $activeFlashDeal = $product->getActiveFlashDealItem();

                    if ($activeFlashDeal) {
                        // Restore flash deal quantity
                        $activeFlashDeal->increment('quantity', $orderItem->quantity);

                        Log::info('Flash deal stock reversed', [
                            'product_id' => $product->id,
                            'variant' => $orderItem->variant,
                            'quantity_restored' => $orderItem->quantity,
                            'order_id' => $order->id
                        ]);
                    } else {
                        // Restore regular stock
                        $stock->increment('qty', $orderItem->quantity);

                        Log::info('Regular stock reversed', [
                            'product_id' => $product->id,
                            'variant' => $orderItem->variant,
                            'quantity_restored' => $orderItem->quantity,
                            'new_stock_qty' => $stock->qty,
                            'order_id' => $order->id
                        ]);
                    }
                }
            });

            Log::info('Stock reversal completed successfully', ['order_id' => $orderId]);
            return true;
        } catch (\Exception $e) {
            Log::error('Stock reversal failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Search cities for autocomplete
     */
    public function searchCities(Request $request)
    {
        $query = $request->get('q', '');
        $results = \App\Services\CityService::searchCities($query, 10);

        return response()->json($results);
    }
}
