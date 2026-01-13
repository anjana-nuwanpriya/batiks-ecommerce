@extends('frontend.layouts.app')

@section('content')

@php
    $breadcrumbs = [
        ['url' => route('home'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Shopping Cart'],
    ];
@endphp

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="$breadcrumbs"
/>

<div class="shopping-cart-section">
    <div class="container">
        <h3 class="section-title">My Shopping Cart</h3>

        @if(empty($cartItems))
            <div class="alert alert-warning">
                Your cart is empty.
            </div>
        @else
        <form action="{{ route('cart.checkout') }}" method="GET">
            <div class="cart-content">
                <div class="cart-items">
                    <div class="cart-header">
                        <div class="product-col">PRODUCT</div>
                        <div class="price-col">PRICE</div>
                        <div class="quantity-col">QUANTITY</div>
                        <div class="subtotal-col">SUBTOTAL</div>
                        <div class="remove-col"></div>
                    </div>
                    @if(!empty($cartItems))
                    @foreach($cartItems as $item)
                    <div class="cart-item" data-product-id="{{ $item['product']->id }}" data-variant-id="{{ $item['variantId'] }}">
                        <div class="product-col">
                            <div class="product-info">
                                @if($item['product']->thumbnail)
                                    <img src="{{ $item['product']->thumbnail }}" alt="{{ $item['product']->name }}">
                                @else
                                    <img src="{{ asset('assets/default.jpg') }}" alt="{{ $item['product']->name }}">
                                @endif
                                <div class="product-name-container">
                                    <div class="product-name">{{ $item['product']->name }}</div>
                                    @if ($item['variant'] != "Standard")
                                        <span class="product-variant text-muted fs-12">{{ $item['variant'] }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="price-col">{{ formatCurrency($item['price']) }}</div>
                        <div class="quantity-col">
                            <div class="quantity-control" data-productId="{{ $item['product']->id }}" data-variantId="{{ $item['variantId'] }}">
                                <button type="button" class="quantity-btn minus">-</button>
                                <input type="number" value="{{ $item['quantity'] }}" min="1" max="{{ $item['stock'] }}" class="quantity-input"  value="{{ $item['quantity'] }}">
                                <button type="button" class="quantity-btn plus">+</button>
                            </div>
                        </div>
                        <div class="subtotal-col" id="subtotal-{{ $item['product']->id }}-{{ $item['variantId'] }}">{{ formatCurrency($item['subtotal']) }}</div>
                        <div class="remove-col">
                            <button class="remove-item" type="button" onclick="removeFromCart('{{ $item['product']->id }}', '{{ $item['variantId'] }}')"> <i class="las la-times-circle"></i> </button>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    <!-- Additional cart items can be added here -->
                </div>

                <div class="cart-summary">
                    <h2>Cart Total</h2>
                    <div class="summary-row">
                        <span>Subtotal:</span>
                        <span class="amount" id="subtotal-amount">{{ formatCurrency(getCartTotalAmount()) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping:</span>
                        <span class="amount">Free</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total:</span>
                        <span class="amount" id="total-amount">{{ formatCurrency(getCartTotalAmount()) }}</span>
                    </div>
                    <button class="checkout-btn" type="submit"> {{ __('Proceed to checkout')}}</button>
                </div>
            </div>
        </form>
        {{-- <div class="cart-actions">
            <div class="coupon-section">
                <h3>Coupon Code</h3>
                <div class="coupon-form">
                    <input type="text" placeholder="Enter code">
                    <button class="apply-coupon">Apply Coupon</button>
                </div>
            </div>
            <div class="cart-buttons">
                <a href="{{ route('home') }}" class="return-shop">Return to shop</a>
                <button class="update-cart">Update Cart</button>
            </div>
        </div> --}}
    </div>
</div>
@endif
@endsection