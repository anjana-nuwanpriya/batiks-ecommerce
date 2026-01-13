@php
    $sortedStocks = $product->stocks->sortBy('variant');
    $stock = $sortedStocks->first();
    $productStock = $product->stocks->sum('qty');
    // Find stock with lowest qty that's greater than 0, sorted by variant ascending
$availableStock = $sortedStocks->where('qty', '>', 0)->sortBy('qty')->first();
    // If no available stock, use first stock for display
    $displayStock = $availableStock ?: $stock;
@endphp
<div class="product-wrapper">
    @if ($product->special_price > 0)
        <div class="product-wrapper__badge">
            <span class="badge badge-success">Sale
                {{ $product->special_price_type == 'fixed' ? round(($product->special_price / $product->cartPrice($stock->id)) * 100) : $product->special_price }}%</span>
        </div>
    @endif
    <a href="{{ route('sf.product.show', $product->slug) }}" class="text-decoration-none">
        <div class="product-wrapper__img">
            @if (!empty($product->thumbnail))
                <img src="{{ $product->thumbnail }}" class="img-fluid" alt="{{ $product->name }}">
            @else
                <img src="{{ asset('assets/default.jpg') }}" class="img-fluid" alt="{{ $product->name }}">
            @endif
        </div>
    </a>
    <div class="product-wrapper__content">
        <div class="product-wrapper__content--title">
            @if (!empty($product->sinhala_name))
                <h5 class="notranslate">{{ $product->sinhala_name }}</h5>
                <h6 class="notranslate">{{ $product->name }}</h6>
            @else
                <h5 class="notranslate">{{ $product->name }}</h5>
            @endif
        </div>
        <div class="rating-st">
            @php
                $rating = $product->reviews->avg('rating');
            @endphp
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $rating)
                    <i class="las la-star active"></i>
                @else
                    <i class="las la-star "></i>
                @endif
            @endfor
            <span>({{ $product->reviews()->where('is_approved', 1)->count() }})</span>
        </div>
        <div class="product-wrapper__action">
            <h6> {{ formatCurrency($product->cartPrice($displayStock->id)) }}</h6>
            @if ($productStock > 0)
                <div class="d-flex gap-2" id="cart-btn" onclick="quickAddToCart(this)"
                    data-product-id="{{ $product->id }}" data-variant="variant-{{ $availableStock->id }}"
                    data-quantity="1">
                    <button class="cart-btn"><i class="las la-shopping-cart"></i></button>
                </div>
            @else
                <div class="d-flex gap-2">
                    <a href="{{ route('sf.product.show', $product->slug) }}" class="cart-btn" disabled><i
                            class="las la-eye"></i></a>
                </div>
            @endif
        </div>
    </div>

    <div class="product-wrapper__action--other">
        <button type="button" class="btn wishlist-btn mb-2 favorite-btn {{ $product->isWishlist() ? 'active' : '' }}"
            onclick="addToWishlist('{{ $product->slug }}')">
            <i class="las la-heart"></i>
        </button>
    </div>
</div>
