<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * Display the shopping cart page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $cartItems = [];

        if (session()->has('cart')) {
            $cart = session()->get('cart');

            foreach ($cart as $productId => $details) {
                $product = Product::find($productId);
                $stock = $product->stocks()->where('id', $details['variant'])->first();

                $cartItems[] = [
                    'product' => $product,
                    'variant' => $stock->variant,
                    'variantId' => $stock->id,
                    'quantity' => $details['quantity'],
                    'price' => $product->cartPrice($stock->id),
                    'stock' => $stock->qty,
                    'subtotal' => $product->cartPrice($stock->id, $details['quantity'])
                ];
            }
        }

        return view('frontend.cart', compact('cartItems'));
    }


    /** Add a product to the cart.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'variant' => 'required|string',
        ]);

        // Extract variant ID
        $variant = explode('-', $request->variant)[1];

        // Get product and stock in one query
        $product = Product::with(['stocks' => fn($query) => $query->where('id', $variant)])
            ->findOrFail($request->product_id);

        $productStock = $product->stocks->first();

        if (!$productStock || $productStock->qty < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Product stock is insufficient'
            ], 400);
        }

        // Use composite key for cart items
        $cartKey = "{$product->id}-{$variant}";
        $cart = session()->get('cart', []);

        // Update or add to session cart
        $cart[$cartKey] = [
            'quantity' => ($cart[$cartKey]['quantity'] ?? 0) + $request->quantity,
            'variant' => $variant,
        ];

        session()->put('cart', $cart);

        // Handle database cart
        $cartModel = Cart::firstOrCreate([
            'session_id' => session()->getId(),
            'user_id' => Auth::id() ?? null,
        ]);

        $cartItem = $cartModel->items()
            ->where('product_id', $product->id)
            ->where('product_variant_id', $variant)
            ->first();

        // Use upsert for efficient DB operation
        if ($cartItem) {
            $cartItem->increment('quantity', $request->quantity);
        } else {
            $cartModel->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variant,
                'quantity' => $request->quantity,
            ]);
        }

        return response()->json([
            'success' => true,
            'itemCount' => count($cart),
            'viewCart' => view('frontend.partials.cart-2')->render(),
        ]);
    }


    public function removeFromCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_stocks,id', // Validate variant_id against stocks table
        ]);

        // Initialize response data
        $itemCount = 0;
        $totalAmount = 0;

        // Use composite key for session cart
        $cartKey = "{$request->product_id}-{$request->variant_id}";
        $cart = session()->get('cart', []);

        // Remove from session cart
        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
            session()->put('cart', $cart);
            $itemCount = count($cart);
            $totalAmount = getCartTotalAmount();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart',
            ], 404);
        }

        // Remove from database cart
        $dbCart = Cart::where('session_id', session()->getId())
            ->when(Auth::check(), fn($query) => $query->where('user_id', Auth::id()))
            ->first();

        if ($dbCart) {
            $deleted = $dbCart->items()
                ->where('product_id', $request->product_id)
                ->where('product_variant_id', $request->variant_id)
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in database cart',
                ], 404);
            }
            $itemCount = $dbCart->items()->count();
            $totalAmount = getCartTotalAmount();
        }

        return response()->json([
            'success' => true,
            'itemCount' => $itemCount,
            'viewCart' => view('frontend.partials.cart-2')->render(),
            'totalAmount' => formatCurrency($totalAmount),
        ]);
    }


    public function variantAvailability(Request $request)
    {
        $productSlug = null;
        $variantId = null;
        $qty = 0;
        $sellingPrice = 0;
        $originalPrice = 0;
        $variantImage = null;

        if (is_array($request->data)) {
            $productSlug = $request->data['productSlug'];
            $parts = explode('-', $request->data['variant']);
            $variantId = $parts[1] ?? null;
        }

        if (!$productSlug || !$variantId) {
            return response()->json([
                'qty' => 0,
                'viewPrice' => '',
            ]);
        }

        // Load product and the specific variant
        $product = Product::with(['stocks' => function ($query) use ($variantId) {
            $query->where('id', $variantId);
        }])
            ->where('slug', $productSlug)
            ->whereHas('stocks', function ($query) use ($variantId) {
                $query->where('id', $variantId);
            })
            ->first();

        if ($product && $product->stocks->isNotEmpty()) {
            $stock = $product->stocks->first();

            $variantImage = $stock->thumbnail;

            if ($stock->qty > 0) {
                $qty = $stock->qty;
                $sellingPrice = $product->cartPrice($variantId, 1);
                $originalPrice = $stock->selling_price;
            }
        }



        $viewPrice = view('frontend.partials.price', compact('sellingPrice', 'originalPrice'))->render();

        return response()->json([
            'qty' => $qty,
            'viewPrice' => $viewPrice,
            'image' => $variantImage,
        ]);
    }


    /**
     * Get the product amount.
     *
     * @param int $productId
     * @param int $variantId
     * @param int $quantity
     * @return int
     */
    public function getProductAmount($productId, $variantId, $quantity)
    {
        $today = Carbon::now()->format('Y-m-d');

        $product = Product::find($productId);
        $variant = $product->stocks()->where('id', $variantId)->first();

        if ($product->special_price > 0 && $product->special_price_start <= $today && $product->special_price_end >= $today) {
            if ($product->special_price_type == 'fixed') {
                $sellingPrice = abs($variant->selling_price - $product->special_price);
            } else {
                $sellingPrice = abs($variant->selling_price - ($variant->selling_price * $product->special_price / 100));
            }
        } else {
            $sellingPrice = $variant->selling_price;
        }

        return $sellingPrice * $quantity;
    }

    /**
     * Update the cart.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get product and stock in one query
        $product = Product::with(['stocks' => fn($query) => $query->where('id', $request->variant_id)])
            ->findOrFail($request->product_id);

        $productStock = $product->stocks->first();

        if (!$productStock || $productStock->qty < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Product stock is insufficient'
            ], 400);
        }

        // Use composite key for cart items
        $cartKey = "{$product->id}-{$request->variant_id}";
        $cart = session()->get('cart', []);


        // Update session cart if item exists
        if (isset($cart[$cartKey])) {
            $cart[$cartKey] = [
                'quantity' => $request->quantity,
                'variant' => $request->variant_id,
            ];
            session()->put('cart', $cart);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in cart'
            ], 404);
        }

        // Update database cart
        $cartModel = Cart::where('session_id', session()->getId())
            ->when(Auth::check(), fn($query) => $query->where('user_id', Auth::id()))
            ->first();

        if ($cartModel) {
            $cartItem = $cartModel->items()
                ->where('product_id', $product->id)
                ->where('product_variant_id', $request->variant_id)
                ->first();

            if ($cartItem) {
                $cartItem->quantity = $request->quantity;
                $cartItem->save();
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found in database cart'
                ], 404);
            }
        }

        $productTotal = $product->cartPrice($request->variant_id, $request->quantity);
        $totalAmount = getCartTotalAmount();

        return response()->json([
            'success' => true,
            'subtotalAmount' => formatCurrency($totalAmount),
            'totalAmount' => formatCurrency($totalAmount),
            'productTotal' => formatCurrency($productTotal),
            'viewCart' => view('frontend.partials.cart-2')->render(),
        ]);
    }

    public function clearCart()
    {
        // Clear database cart for the user or session
        $cart = Cart::where('session_id', session()->getId())
            ->when(Auth::check(), fn($query) => $query->where('user_id', Auth::id()))
            ->first();
        if ($cart) {
            $cart->items()->delete();
        }

        // Clear the session cart
        session()->forget('cart');

        $viewCart = view('frontend.partials.cart-2')->render();
        return response()->json([
            'success' => true,
            'viewCart' => $viewCart,
        ]);
    }
}
