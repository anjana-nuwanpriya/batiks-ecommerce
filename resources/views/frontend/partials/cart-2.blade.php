<!-- Cart Sidebar -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="cartSidebar" aria-labelledby="cartSidebarLabel">
    <div class="offcanvas-header">
        <h5 id="cartSidebarLabel" class="offcanvas-title fw-bold">My Cart</h5>
        <button type="button" class="btn-close font-12" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">

        @if(!empty(session()->get('cart')))
        <div class="cart-items">
            @foreach(session()->get('cart') as $productId => $item)
            @php
                $product = \App\Models\Product::find($productId);
                $variant = $product->stocks()->where('id', $item['variant'])->first();
            @endphp
            <div class="d-flex position-relative mb-3 border-bottom pb-2" id="cart-item-{{ $product->id }}">
                <div class="flex-shrink-0">
                    @if($product->thumbnail)
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="" height="40">
                    @else
                    <img src="{{ asset('assets/default.jpg') }}" alt="{{ $product->name }}" class="" height="40">
                    @endif
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6>{{ $product->name }}</h6>
                    @if ($variant->variant != "Standard")
                    <p>{{ $variant->name }}</p>
                    @endif
                    <p> {{ $item['quantity'] }} x <span class="text-dark">{{ formatCurrency($product->cartPrice($variant->id, $item['quantity'])) }} </span></p>
                </div>
                <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2 font-12" onclick="removeFromCart('{{ $product->id }}', '{{ $variant->id }}')"></button>
            </div>
            @endforeach
        </div>
        <!-- Footer Section -->
        <div class="mt-auto border-top pt-3">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-bold">Total</span>
                <span class="fw-bold text-success" id="cart-total-amount">{{ formatCurrency(getCartTotalAmount()) }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center gap-3 mt-3">
                <button onclick="clearCart()" class="btn bg-white btn-outline-dark flex-1" @if(empty(session()->get('cart'))) disabled @endif>Clear Cart</button>

                <div class="d-flex gap-2">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-success flex-1">{{ __('View Cart') }}</a>
                    <a href="{{ route('cart.checkout') }}" class="btn btn-success flex-1" @if(empty(session()->get('cart'))) disabled @endif>Checkout</a>
                </div>
            </div>
        </div>
        @else
        <!-- Empty Cart State -->
        <div class="text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
            <i class="las la-box-open display-4 text-muted"></i>
            <p class="text-muted mt-3">Your cart is empty</p>
        </div>
        @endif
    </div>
</div>
