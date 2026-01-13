<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\City;
use App\Models\FileUpload;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\User;
use App\Models\Weight;
use App\Utilities\HutchSmsUtility;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{

    public function index()
    {
        $orders = Order::with('user')->where('admin_order', false)->orderBy('created_at', 'desc')->get();
        return view('admin.sales.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->new_order_notification = false;
        $order->save();
        return view('admin.sales.orders.info', compact('order'));
    }


    public function updateOrderStatus(Request $request, Order $order)
    {
        try {
            $originalStatus = $order->payment_status;

            // Prevent updating if already paid
            if ($order->payment_status === 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Order already marked as paid.'
                ], 422);
            }

            // Check bank transfer slip for payment confirmation
            // if ($order->payment_method === 'bank_transfer' && $request->status === 'paid' && empty($order->payment_proof)) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Payment slip not attached.'
            //     ], 422);
            // }

            // Handle stock rollback if cancelled/failed/refunded
            $cancelStatuses = ['cancelled', 'failed', 'refunded'];
            if (in_array($request->status, $cancelStatuses) && !in_array($order->payment_status, $cancelStatuses)) {
                $stockChanges = [];
                foreach ($order->items as $item) {
                    // Ensure qty is numeric and positive
                    $qtyToRestore = is_numeric($item->quantity) && $item->quantity > 0 ? (int)$item->quantity : 0;

                    if ($qtyToRestore > 0) {
                        if ($item->variant) {
                            $stock = $item->product->stocks()->where('variant', $item->variant)->first();
                            if ($stock) {
                                $oldQty = $stock->quantity;
                                $stock->increment('qty', $qtyToRestore);
                                $stockChanges[] = [
                                    'product' => $item->product->name,
                                    'variant' => $item->variant,
                                    'old_qty' => $oldQty,
                                    'new_qty' => $stock->qty,
                                    'restored_qty' => $qtyToRestore
                                ];
                            }
                        }
                    }
                }

                // Log stock restoration
                if (!empty($stockChanges)) {
                    activity('order_management')
                        ->causedBy(auth()->user())
                        ->performedOn($order)
                        ->withProperties([
                            'operation_type' => 'stock_restoration',
                            'order_id' => $order->id,
                            'order_code' => $order->code,
                            'status_change' => ['from' => $originalStatus, 'to' => $request->status],
                            'stock_changes' => $stockChanges
                        ])
                        ->log("Restored stock for order #{$order->code} due to status change to {$request->status}");
                }
            }

            // Decode shipping address from JSON string to array
            $address = is_string($order->shipping_address)
                ? json_decode($order->shipping_address, true)
                : $order->shipping_address;

            // Handle bank transfer orders when status is updated to paid
            if ($order->payment_method === 'bank_transfer' && $request->status === 'paid') {

                // Check if PromptXpress API is enabled
                if (!isCourierApiAvailable('promptxpress')) {
                    Log::warning('PromptXpress API is disabled, skipping waybill creation for order: ' . $order->code);
                } else {
                    // Create PromptAPI waybill for paid bank transfer orders
                $total_weight = 0;
                $parcel_description = "";

                foreach ($order->items as $item) {
                    $total_weight += $item->weight;
                    $parcel_description .= "{$item->product->name} ({$item->variant}) x {$item->quantity}, ";
                }

                // Prepare PromptAPI delivery order data
                $orderData = [
                    'orderNo' => $order->code,
                    'customerCode' => $order->user_id ? 'CUST' . $order->user->id : 'GUEST' . $order->id,
                    'consignorName' => env('APP_NAME'),
                    'consignorAddress' => get_setting('address'),
                    'consignorCity' => 'Habaraduwa',
                    'consignorZipCode' => '80240',
                    'consignorCountry' => 'Sri Lanka',
                    'consignorTelephone' => get_setting('phone'),
                    'consignorEmail' => get_setting('email'),
                    'consigneeName' => !empty($order->user_id) ? $order->user->name : $address['name'],
                    'consigneeAddress' => $address['address'],
                    'consigneeCity' => $address['city'],
                    'consigneePostCode' => $address['postal_code'] ?? '00000',
                    'consigneeTelephone' => (string) ($address['phone'] ?? ''),
                    'consigneeEmail' => !empty($order->user_id) ? $order->user->email : $address['email'],
                    'requestDate' => now()->toISOString(),
                    'remarks' => 'Handle with care',
                    'totalWeight' => $total_weight / 1000, // Convert to kg
                    'totalCODAmount' => 0.00, // Bank transfer is prepaid
                    'totalCourierCharge' => $order->shipping_cost,
                    'totalCharge' => $order->grand_total,
                    'area' => 'OS',
                    'eCommPlatform' => 'MSSOLUTIONS',
                    'orderItems' => []
                ];

                // Add order items
                foreach ($order->items as $index => $item) {
                    $orderData['orderItems'][] = [
                        'trackingNo' => '',
                        'waybillNumber' => 'WB' . $order->id . '-' . ($index + 1),
                        'refNo' => 'REF' . $order->id . '-' . ($index + 1),
                        'packageNumber' => 'PKG' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                        'productCategory' => $item->product->category->name ?? 'General',
                        'productName' => $item->product->name,
                        'weight' => $item->weight / 1000, // Convert to kg
                        'numberOfItems' => $item->quantity,
                        'width' => 20, // Default dimensions - can be made dynamic
                        'height' => 15,
                        'length' => 30,
                        'volumeWeight' => ($item->weight / 1000) * 1.2, // Estimated volume weight
                        'codAmount' => 0.00, // Bank transfer is prepaid
                        'courierCharge' => $item->weight_cost ?? 0,
                        'remarks' => 'Variant: ' . $item->variant
                    ];
                }

                try {
                    $promptService = new PromptAPTService();
                    $response = $promptService->createDeliveryOrder($orderData);

                    if ($response['status'] === 201) {
                        // Extract trackingNo from the response result array
                        $waybillNo = null;
                        if (isset($response['response']['result']) && is_array($response['response']['result']) && count($response['response']['result']) > 0) {
                            $waybillNo = $response['response']['result'][0]['trackingNo'] ?? null;
                        }

                        // Fallback if trackingNo not found
                        if (!$waybillNo) {
                            $waybillNo = 'PX' . $order->id;
                        }

                        $order->courier_service = "PromptXpress";
                        $order->waybill_no = $waybillNo;
                        $order->save();

                        Log::info('PromptXpress waybill created successfully for order: ' . $order->code, [
                            'waybill_no' => $waybillNo
                        ]);
                    } else {
                        Log::warning('Failed to create PromptXpress waybill for order: ' . $order->code, $response);
                    }
                } catch (\Exception $e) {
                    Log::error('PromptXpress waybill creation failed for order: ' . $order->code . ' - ' . $e->getMessage());
                }
                }

                // Send SMS for paid order
                if ($address['phone']) {
                    $message = "Your order #{$order->code} payment has been confirmed. Your order is now being processed. Thank you!";
                    // send_sms($order->user->phone, $message);
                }
            } elseif (in_array($request->status, ['cancelled', 'refunded'])) {
                // Send SMS for cancelled/refunded orders
                // if ($order->user->phone) {
                //     $status = $request->status === 'cancelled' ? 'cancelled' : 'refunded';
                //     $message = "Your order #{$order->code} has been {$status}. If you have any questions, please contact us.";
                //     HutchSmsUtility::sendSms($address->phone, $message);
                // }
            }

            // Update order status
            $order->payment_status = $request->status;
            $order->save();

            // Log order status update
            activity('order_management')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties([
                    'operation_type' => 'status_update',
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                    'customer_name' => $order->user->name ?? 'Guest',
                    'payment_method' => $order->payment_method,
                    'status_change' => ['from' => $originalStatus, 'to' => $request->status],
                    'order_total' => $order->grand_total,
                    'waybill_created' => !empty($order->waybill_no)
                ])
                ->log("Updated payment status for order #{$order->code} from {$originalStatus} to {$request->status}");

            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }


    public function updateDeliveryStatus(Request $request, Order $order)
    {
        $originalDeliveryStatus = $order->delivery_status;

        $order->delivery_status = $request->delivery_status;
        $order->save();

        // Log delivery status update
        activity('order_management')
            ->causedBy(auth()->user())
            ->performedOn($order)
            ->withProperties([
                'operation_type' => 'delivery_status_update',
                'order_id' => $order->id,
                'order_code' => $order->code,
                'customer_name' => $order->user->name ?? 'Guest',
                'delivery_status_change' => ['from' => $originalDeliveryStatus, 'to' => $request->delivery_status],
                'waybill_no' => $order->waybill_no
            ])
            ->log("Updated delivery status for order #{$order->code} from {$originalDeliveryStatus} to {$request->delivery_status}");

        return response()->json(['success' => true, 'message' => 'Delivery status updated successfully']);
    }

    public function updateShippingAddress(Request $request, Order $order)
    {
        $request->validate([
            'shipping_address.name' => 'required|string|max:255',
            'shipping_address.phone' => ['required', function ($_attribute, $value, $fail) {
                if (!phoneNumberValidation($value)) {
                    $fail('Please enter a valid Sri Lankan phone number.');
                }
            }],
            'shipping_address.email' => 'nullable|email|max:255',
            'shipping_address.address' => 'required|string|max:500',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'nullable|string|max:100',
            'shipping_address.postal_code' => 'nullable|string|max:20',
            'shipping_address.country' => 'nullable|string|max:100',
        ]);

        try {
            $originalAddress = $order->shipping_address;
            $newAddress = $request->shipping_address;

            // Update the shipping address
            $order->shipping_address = json_encode($newAddress);
            $order->save();

            // Log shipping address update
            activity('order_management')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties([
                    'operation_type' => 'shipping_address_update',
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                    'customer_name' => $order->user->name ?? 'Guest',
                    'address_change' => [
                        'from' => $originalAddress,
                        'to' => $newAddress
                    ]
                ])
                ->log("Updated shipping address for order #{$order->code}");

            return response()->json([
                'success' => true,
                'message' => 'Shipping address updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating shipping address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update shipping address'
            ], 500);
        }
    }


    public function adminOrders()
    {
        $orders = Order::where('admin_order', true)->orderBy('created_at', 'desc')->get();
        return view('admin.sales.admin_orders.index', compact('orders'));
    }

    public function createAdminOrder()
    {
        $users = User::withoutUser()->get();
        $products = Product::with('stocks')->get();

        return view('admin.sales.admin_orders.create', compact('users', 'products'));
    }

    public function getProductVariants(Request $request)
    {
        $product = Product::with('stocks')->find($request->product_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'variants' => $product->stocks,
        ]);
    }

    public function addProductToOrder(Request $request)
    {
        $product = Product::find($request->product_id);
        $variant = ProductStock::find($request->variant_id);

        if (!$product || !$variant) {
            return response()->json([
                'success' => false,
                'message' => 'Product or variant not found'
            ]);
        }

        return response()->json([
            'success' => true,
            'product' => $product,
            'variant' => $variant,
            'price' => $product->cartPrice($variant->id, 1)
        ]);
    }


    public function storeAdminOrder(Request $request)
    {
        // Get available courier services for validation
        $availableCouriers = array_keys(getAvailableCourierServices());

        // Check if any courier services are available
        if (empty($availableCouriers)) {
            return response()->json([
                'success' => false,
                'message' => 'No courier services are currently enabled. Please enable at least one courier service in the settings before creating orders.',
            ], 422);
        }

        $request->validate([
            'customer' => 'required|exists:users,id',
            'product_id' => 'required|array',
            'selected_address' => 'required|exists:addresses,id',
            'payment_method' => 'required|in:COD,CASH,BANK',
            'payment_reference' => 'required_if:payment_method,BANK|nullable|string|max:255',
            'payment_status' => 'required_if:payment_method,CASH,BANK|nullable|in:pending,paid',
            'courier_partner' => 'required|in:' . implode(',', $availableCouriers),
        ], [
            'product_id.required' => 'Please select a product',
            'selected_address.required' => 'Please select a shipping address',
            'payment_method.required' => 'Please select a payment method',
            'payment_reference.required_if' => 'Payment reference is required for bank transfers',
            'payment_status.required_if' => 'Payment status is required for cash and bank payments',
            'courier_partner.required' => 'Please select a courier partner',
        ]);

        try {
            // Perform all operations within a transaction
            $customer = User::findOrFail($request->customer);
            $order = DB::transaction(function () use ($request, $customer) {
                // Fetch address and customer
                $address = Address::findOrFail($request->selected_address);

                // Prepare shipping address
                $shippingAddress = $address->only([
                    'phone',
                    'address',
                    'city',
                    'state',
                    'country',
                    'postal_code'
                ]);
                $shippingAddress['name'] = $customer->name;

                // Validate products and stock
                $subtotal = 0;
                $total_weight = 0;
                $parcel_description = "";
                $items = [];

                foreach ($request->product_id as $key => $id) {
                    $variantId = $request->variant_id[$key];
                    $quantity = $request->quantity[$key];

                    $product = Product::findOrFail($id);
                    $variant = ProductStock::findOrFail($variantId);

                    // Check stock availability
                    $activeFlashDeal = $product->getActiveFlashDealItem();
                    if ($activeFlashDeal) {
                        if ($activeFlashDeal->quantity < $quantity) {
                            throw new \Exception("Insufficient flash deal stock for product: {$product->name}");
                        }
                    } else {
                        if ($variant->qty < $quantity) {
                            throw new \Exception("Insufficient stock for product: {$product->name}");
                        }
                    }

                    // Calculate prices and weight
                    $unit_price = $product->cartPrice($variantId, 1);
                    $total_price = $product->cartPrice($variantId, $quantity);
                    $weight = $variant->weight * $quantity;
                    if (!$product->is_free_shipping) {
                        $total_weight += $weight;
                    }

                    $parcel_description .= "{$product->name} ({$variant->variant}) x {$quantity}, ";

                    // Calculate product-wise shipping cost
                    $weight_cost = $this->calculateProductWiseShippingCost($id, $variantId)->original['shipping_cost'];

                    // Prepare item data
                    $items[] = [
                        'product_id' => $id,
                        'variant' => $variant->variant,
                        'quantity' => $quantity,
                        'cost' => $variant->purchase_price,
                        'unit_price' => $unit_price,
                        'total_price' => $total_price,
                        'weight' => $weight,
                        'weight_cost' => $weight_cost
                    ];

                    $subtotal += $total_price;
                }

                // Calculate total shipping cost
                $shipping_response = $this->calculateShippingCost(new Request([
                    'product_ids' => $request->product_id,
                    'variant_ids' => $request->variant_id,
                    'quantity' => $request->quantity
                ]));
                $shipping_cost = $shipping_response->original['shipping_cost'];

                // Create order
                $order = new Order();
                $order->user_id = $customer->id;
                $order->code = 'ORD-' . rand(100000, 999999);
                $order->shipping_address = json_encode($shippingAddress);
                $order->payment_method = $request->payment_method;

                // Set payment status based on payment method
                if ($request->payment_method === 'COD') {
                    $order->payment_status = 'pending';
                } else {
                    $order->payment_status = $request->payment_status ?? 'pending';
                }

                // Store payment reference in note field if provided
                if ($request->filled('payment_reference')) {
                    $order->note = 'Payment Reference: ' . $request->payment_reference;
                }

                $order->delivery_status = 'Waiting';
                $order->admin_order = true;
                $order->shipping_cost = $shipping_cost;
                $order->grand_total = $subtotal + $shipping_cost;

                // Save order
                $order->save();

                // Create order items and update stock
                foreach ($items as $item) {
                    $order->items()->create($item);

                    // Update stock
                    $product = Product::findOrFail($item['product_id']);
                    $variant = ProductStock::findOrFail($request->variant_id[array_search($item['product_id'], $request->product_id)]);
                    $activeFlashDeal = $product->getActiveFlashDealItem();
                    if ($activeFlashDeal) {
                        $activeFlashDeal->decrement('quantity', $item['quantity']);
                    } else {
                        $variant->decrement('qty', $item['quantity']);
                    }
                }

                // Courier Integration based on selected courier partner
                if ($request->courier_partner === 'PromptXpress') {
                    // Check if PromptXpress API is enabled
                    if (!isCourierApiAvailable('promptxpress')) {
                        throw new \Exception('PromptXpress API is currently disabled. Please enable it in settings or choose a different courier partner.');
                    }
                    // Use PromptXpress service
                    try {
                        $orderData = [
                            'orderNo' => $order->code,
                            'customerCode' => $order->user_id ? 'CUST' . $order->user->id : 'GUEST' . $order->id,
                            'consignorName' => env('APP_NAME'),
                            'consignorAddress' => get_setting('address'),
                            'consignorCity' => 'Habaraduwa',
                            'consignorZipCode' => '80240',
                            'consignorCountry' => 'Sri Lanka',
                            'consignorTelephone' => get_setting('phone'),
                            'consignorEmail' => get_setting('email'),
                            'consigneeName' => $customer->name,
                            'consigneeAddress' => $address->address,
                            'consigneeCity' => $address->city,
                            'consigneePostCode' => $address->postal_code ?? '00000',
                            'consigneeTelephone' => $address->phone,
                            'consigneeEmail' => $customer->email,
                            'requestDate' => now()->toISOString(),
                            'remarks' => 'Handle with care - Prepaid Order',
                            'totalWeight' => $total_weight / 1000,
                            'totalCODAmount' => $order->payment_method === 'COD' ? $order->grand_total : 0.00,
                            'totalCourierCharge' => $order->shipping_cost,
                            'totalCharge' => $order->grand_total,
                            'area' => 'OS',
                            'eCommPlatform' => 'MSSOLUTIONS',
                            'orderItems' => []
                        ];

                        // Add order items
                        foreach ($order->items as $index => $item) {
                            $orderData['orderItems'][] = [
                                'trackingNo' => '',
                                'waybillNumber' => 'WB' . $order->id . '-' . ($index + 1),
                                'refNo' => 'REF' . $order->id . '-' . ($index + 1),
                                'packageNumber' => 'PKG' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                                'productCategory' => 'General',
                                'productName' => $item->product->name,
                                'weight' => $item->weight / 1000,
                                'numberOfItems' => $item->quantity,
                                'width' => 20,
                                'height' => 15,
                                'length' => 30,
                                'volumeWeight' => ($item->weight / 1000) * 1.2,
                                'codAmount' => $order->payment_method === 'COD' ? ($item->total_price + ($item->weight_cost ?? 0)) : 0.00,
                                'courierCharge' => $item->weight_cost ?? 0,
                                'remarks' => 'Variant: ' . $item->variant
                            ];
                        }

                        $promptService = new PromptAPTService();
                        $response = $promptService->createDeliveryOrder($orderData);


                        if ($response['status'] === 201) {
                            $waybillNo = null;
                            if (isset($response['response']['result']) && is_array($response['response']['result']) && count($response['response']['result']) > 0) {
                                $waybillNo = $response['response']['result'][0]['trackingNo'] ?? null;
                            }

                            $order->courier_service = "PromptXpress";
                            $order->waybill_no = $waybillNo;
                            $order->save();
                        }
                    } catch (\Exception $e) {
                        Log::error('PromptXpress waybill creation failed for prepaid order: ' . $order->code . ' - ' . $e->getMessage());
                        // Don't throw exception here, order should still be created
                    }
                }

                return $order;
            });

            // SMS Notification (outside transaction)
            if ($order->user->phone) {
                $checkoutController = new CheckoutController();
                $checkoutController->orderNotify($order->id);
            }

            // Log admin order creation
            activity('order_management')
                ->causedBy(auth()->user())
                ->performedOn($order)
                ->withProperties([
                    'operation_type' => 'admin_create',
                    'order_id' => $order->id,
                    'order_code' => $order->code,
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status,
                    'payment_reference' => $request->payment_reference,
                    'total_amount' => $order->grand_total,
                    'shipping_cost' => $order->shipping_cost,
                    'items_count' => count($request->product_id),
                    'waybill_created' => !empty($order->waybill_no),
                    'courier_service' => $order->courier_service ?? $request->courier_partner,
                    'selected_courier_partner' => $request->courier_partner,
                    'items' => collect($request->product_id)->map(function ($productId, $key) use ($request) {
                        $product = Product::find($productId);
                        $variant = ProductStock::find($request->variant_id[$key]);
                        return [
                            'product_name' => $product->name,
                            'variant' => $variant->variant,
                            'quantity' => $request->quantity[$key]
                        ];
                    })->toArray()
                ])
                ->log("Created admin order #{$order->code} for customer {$customer->name} with {$order->payment_method} payment");

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'redirect' => route('admin.order.show', $order->id)
            ]);
        } catch (\Exception $e) {
            // Log detailed error
            Log::error('Admin Order Creation Failed: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'order_id' => isset($order) ? $order->id : null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Calculate shipping cost
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculateShippingCost(Request $request)
    {
        $shippingCost = 0;
        $shippingWeight = 0;
        $quantity = 0;
        if ($request->has('product_ids')) {
            foreach ($request->product_ids as $key => $product_id) {
                $product = Product::find($product_id);
                $productStock = ProductStock::find($request->variant_ids[$key]);
                if (!$product->is_free_shipping) {
                    $shippingWeight += $productStock->weight * $request->quantity[$key];
                }
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

        return response()->json([
            'success' => true,
            'shipping_cost' => $shippingCost,
        ]);
    }

    public function calculateProductWiseShippingCost($product_id, $variant_id)
    {
        $shippingCost = 0;
        $shippingWeight = 0;

        $product = Product::find($product_id);
        $productStock = ProductStock::find($variant_id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found.',
            ], 404);
        }

        if (!$product->is_free_shipping) {
            $shippingWeight = $productStock->weight; // in grams
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

        return response()->json([
            'success' => true,
            'shipping_cost' => $shippingCost,
        ]);
    }

    public function receipt($orderId)
    {
        $selectedOrder = Order::find($orderId);

        $orders = Order::with('items')
            ->whereDate('created_at', $selectedOrder->created_at->toDateString())
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('payment_method', 'COD')
                    ->where('delivery_status', 'Waiting');
                })
                ->orWhere(function ($q) {
                    $q->where('payment_status', 'paid')
                    ->where('delivery_status', 'Waiting');
                })
                ->orWhere(function ($q) {
                    $q->where('delivery_status', 'Waiting')
                    ->whereNotNull('waybill_no');
                });
            })
            ->whereBetween('id', [$selectedOrder->id - 5, $selectedOrder->id + 5])
            ->orderBy('id')
            ->get();

        $perPage = 4;
        return view('admin.recipt', compact('orders', 'perPage'));
        // $pdf = Pdf::loadView('admin.recipt', compact('orders'));
        // // $pdf->setPaper([0, 0, 283.465, 425.1975]); // 100mm x 150mm in points
        // $pdf->setPaper('a4');
        // return $pdf->download("receipt.pdf");
    }

    public function invoice(Order $order)
    {
        $pdf = Pdf::loadView('admin.invoice', compact('order'));
        // $pdf->setPaper('a4'); // A4 size paper
        return $pdf->stream("invoice-{$order->code}.pdf");
    }

    /**
     * Create waybill for orders that don't have one
     */
    public function createWaybill(Request $request, Order $order)
    {

        // Check if order already has a waybill
        if (!empty($order->waybill_no)) {
            return response()->json([
                'success' => false,
                'message' => 'Order already has a waybill: ' . $order->waybill_no
            ]);
        }

        // Decode shipping address from JSON string to array
        $address = is_string($order->shipping_address)
            ? json_decode($order->shipping_address, true)
            : $order->shipping_address;

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid shipping address data'
            ]);
        }

        // Get selected courier service from request, fallback to payment method logic
        $courierService = $request->input('courier_service');


        if (!$courierService) {
            // Fallback to original logic based on payment method
            $courierService = 'PromptXpress';
        }

        // Create waybill based on selected service
        if ($courierService === 'PromptXpress') {
            // Check if PromptXpress API is enabled
            if (!isCourierApiAvailable('promptxpress')) {
                return response()->json([
                    'success' => false,
                    'message' => 'PromptXpress API is currently disabled. Please enable it in settings.'
                ]);
            }
            return $this->createPromptXpressWaybill($order, $address);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid courier service: ' . $courierService
            ]);
        }
    }

    /**
     * Create PromptXpress waybill for prepaid orders
     */
    private function createPromptXpressWaybill(Order $order, array $address)
    {
        $total_weight = 0;
        $parcel_description = "";

        foreach ($order->items as $item) {
            $total_weight += $item->weight;
            $parcel_description .= "{$item->product->name} ({$item->variant}) x {$item->quantity}, ";
        }

        // Prepare PromptXpress delivery order data
        $orderData = [
            'orderNo' => $order->code,
            'customerCode' => $order->user_id ? 'CUST' . $order->user->id : 'GUEST' . $order->id,
            'consignorName' => env('APP_NAME'),
            'consignorAddress' => get_setting('address'),
            'consignorCity' => 'Galle',
            'consignorZipCode' => '80240',
            'consignorCountry' => 'Sri Lanka',
            'consignorTelephone' => get_setting('phone'),
            'consignorEmail' => get_setting('email'),
            'consigneeName' => !empty($order->user_id) ? $order->user->name : $address['name'],
            'consigneeAddress' => $address['address'],
            'consigneeCity' => $address['city'],
            'consigneePostCode' => $address['postal_code'] ?? '0000',
            'consigneeTelephone' => (string) ($address['phone'] ?? ''),
            'consigneeEmail' => !empty($order->user_id) ? $order->user->email : $address['email'],
            'requestDate' => now()->toISOString(),
            'remarks' => 'Handle with care',
            'totalWeight' => $total_weight / 1000, // Convert to kg
            'totalCODAmount' => 0.00, // Prepaid orders have no COD
            'totalCourierCharge' => $order->shipping_cost,
            'totalCharge' => $order->grand_total,
            'area' => 'OS',
            'eCommPlatform' => 'MSSOLUTIONS',
            'orderItems' => []
        ];

        // Add order items
        foreach ($order->items as $index => $item) {
            $orderData['orderItems'][] = [
                'trackingNo' => '',
                'waybillNumber' => 'WB' . $order->id . '-' . ($index + 1),
                'refNo' => 'REF' . $order->id . '-' . ($index + 1),
                'packageNumber' => 'PKG' . str_pad($index + 1, 3, '0', STR_PAD_LEFT),
                'productCategory' => $item->product->categories()->first()->name ?? 'General',
                'productName' => $item->product->name,
                'weight' => $item->weight / 1000, // Convert to kg
                'numberOfItems' => $item->quantity,
                'width' => 20,
                'height' => 15,
                'length' => 30,
                'volumeWeight' => ($item->weight / 1000) * 1.2,
                'codAmount' => $order->payment_method === 'COD' ? ($item->total_price + ($item->weight_cost ?? 0)) : 0.00,
                'courierCharge' => $item->weight_cost ?? 0,
                'remarks' => $item->variant
            ];
        }

        $promptService = new PromptAPTService();
        $response = $promptService->createDeliveryOrder($orderData);

        if ($response['status'] === 201) {
            // Extract trackingNo from the response result array
            $waybillNo = null;
            if (isset($response['response']['result']) && is_array($response['response']['result']) && count($response['response']['result']) > 0) {
                $waybillNo = $response['response']['result'][0]['trackingNo'] ?? null;
            }


            $order->courier_service = "PromptXpress";
            $order->waybill_no = $waybillNo;

            // Log before saving
            Log::info('About to save PromptXpress waybill for order: ' . $order->code, [
                'waybill_no' => $waybillNo,
                'courier_service' => $order->courier_service,
                'order_id' => $order->id
            ]);

            $saved = $order->save();

            // Log after saving
            Log::info('PromptXpress waybill save result for order: ' . $order->code, [
                'saved' => $saved,
                'waybill_no' => $order->waybill_no,
                'courier_service' => $order->courier_service
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PromptXpress waybill created successfully',
                'waybill_no' => $waybillNo
            ]);
        }

        // Log the failure for debugging
        Log::error('PromptXpress waybill creation failed for order: ' . $order->code, [
            'response' => $response,
            'order_data' => $orderData
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to create PromptXpress waybill: ' . ($response['error'] ?? $response['message'] ?? 'Unknown error')
        ]);
    }


    /*
    * PromptX Waybill Download
    */
    public function promptXWaybillDownload(Request $request, Order $order)
    {
        try {

            $media = $order->getFirstMedia('waybill');
            if (!empty($media)) {
                return response()->download($media->getPath(), $media->file_name);
            }

            $waybill_no = $order->waybill_no;

            if (!empty($waybill_no)) {
                $promptService = new PromptAPTService();
                $response = $promptService->getTrackingNumberLabel($waybill_no);

                $decodedImage = base64_decode($response['labelImage']);

                // Collection name
                $collection = 'waybill';

                // File info
                $fileName = "waybill-{$order->id}.png";
                $folder   = uniqid() . '-' . now()->timestamp;

                // Storage path (storage/app/tmp/waybills/{folder}/file.png)
                $path = "tmp/{$collection}/{$folder}/{$fileName}";

                // Save to tmp folder
                Storage::put($path, $decodedImage);

                // Save record in FileUpload (optional, if you use this table)
                FileUpload::create([
                    'file_name' => $fileName,
                    'folder'    => $folder,
                ]);

                // Attach to media library (Spatie will copy the file)
                $media = $order
                    ->addMedia(storage_path("app/{$path}"))
                    ->usingName("waybill-{$order->id}")
                    ->usingFileName($fileName)
                    ->toMediaCollection($collection);

                // 👉 Delete tmp folder after attaching
                Storage::deleteDirectory("tmp/{$collection}/{$folder}");

                return response()->download($media->getPath(), $media->file_name);
            }
        } catch (Exception $e) {
            Log::error('PromptX Waybill Download Failed: ' . $e->getMessage());
            toast('Failed to download waybill', 'error');
            return redirect()->back();
        }
    }

    /**
     * Search cities for Select2
     */
    public function searchCities(Request $request)
    {
        // Log::info('City search called with query: ' . $request->get('q'));

        try {
            $query = $request->get('q');

            if (empty($query)) {
                return response()->json([]);
            }

            $cities = City::search($query);
            // Log::info('Found ' . $cities->count() . ' cities');

            $results = $cities->map(function ($city) {
                $displayName = $city->name_en ?: $city->name_si ?: $city->name_ta ?: 'Unknown City';
                $subName = $city->sub_name_en ?: $city->sub_name_si ?: $city->sub_name_ta;

                if ($subName && $subName !== $displayName) {
                    $displayName .= ' - ' . $subName;
                }

                return [
                    'id' => $city->name_en ?: $city->name_si ?: $city->name_ta,
                    'text' => $displayName . ($city->postcode ? ' (' . $city->postcode . ')' : '')
                ];
            });

            Log::info('Returning results: ' . json_encode($results->take(3)->toArray()));
            return response()->json($results);
        } catch (\Exception $e) {
            Log::error('City search error: ' . $e->getMessage());
            return response()->json([]);
        }
    }
}
