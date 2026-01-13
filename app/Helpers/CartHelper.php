<?php

namespace App\Helpers;

use App\Models\Product;

class CartHelper
{
    /**
     * Clean cart session by removing deleted products and invalid variants
     * 
     * @return array Clean cart items
     */
    public static function cleanCart()
    {
        $cart = session()->get('cart', []);
        $cleanCart = [];

        foreach ($cart as $productId => $item) {
            // Check if product still exists in database
            $product = Product::find($productId);
            
            if ($product) {
                // Check if variant still exists
                $variant = $product->stocks()->find($item['variant'] ?? null);
                
                if ($variant) {
                    // Product and variant both exist, keep in cart
                    $cleanCart[$productId] = $item;
                }
            }
            // If product or variant deleted, skip this item (remove from cart)
        }

        // Update session with clean cart
        session()->put('cart', $cleanCart);
        
        return $cleanCart;
    }

    /**
     * Get cart total items count
     * 
     * @return int Total quantity of items in cart
     */
    public static function getCartItemCount()
    {
        $cart = session()->get('cart', []);
        $count = 0;

        foreach ($cart as $item) {
            $count += $item['quantity'] ?? 0;
        }

        return $count;
    }

    /**
     * Get cart total amount (same as getCartTotalAmount)
     * 
     * @return float Total price of all items in cart
     */
    public static function getCartTotal()
    {
        return self::getCartTotalAmount();
    }

    /**
     * Get cart total amount
     * 
     * @return float Total price of all items in cart
     */
    public static function getCartTotalAmount()
    {
        $cart = session()->get('cart', []);
        $totalAmount = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            
            if ($product) {
                $variant = $product->stocks()->find($item['variant'] ?? null);
                
                if ($variant) {
                    $totalAmount += $product->cartPrice($variant->id, $item['quantity'] ?? 0);
                }
            }
        }

        return $totalAmount;
    }

    /**
     * Remove item from cart
     * 
     * @param int $productId Product ID to remove
     * @param int|null $variantId Variant ID (optional)
     * @return array Updated cart
     */
    public static function removeFromCart($productId, $variantId = null)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
        }

        session()->put('cart', $cart);
        
        return $cart;
    }

    /**
     * Clear entire cart
     * 
     * @return void
     */
    public static function clearCart()
    {
        session()->put('cart', []);
    }

    /**
     * Add item to cart
     * 
     * @param int $productId Product ID
     * @param int $variantId Stock/Variant ID
     * @param int $quantity Quantity to add
     * @return array Updated cart
     */
    public static function addToCart($productId, $variantId, $quantity = 1)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            // Item already in cart, increase quantity
            $cart[$productId]['quantity'] += $quantity;
        } else {
            // New item, add to cart
            $cart[$productId] = [
                'variant' => $variantId,
                'quantity' => $quantity,
            ];
        }

        session()->put('cart', $cart);
        
        return $cart;
    }

    /**
     * Update item quantity in cart
     * 
     * @param int $productId Product ID
     * @param int $quantity New quantity
     * @return array Updated cart
     */
    public static function updateCartQuantity($productId, $quantity)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity <= 0) {
                // Remove if quantity is 0 or less
                unset($cart[$productId]);
            } else {
                $cart[$productId]['quantity'] = $quantity;
            }
        }

        session()->put('cart', $cart);
        
        return $cart;
    }

    /**
     * Get all cart items
     * 
     * @return array Cart items
     */
    public static function getCart()
    {
        return session()->get('cart', []);
    }

    /**
     * Check if cart is empty
     * 
     * @return bool
     */
    public static function isCartEmpty()
    {
        $cart = session()->get('cart', []);
        return empty($cart);
    }
}