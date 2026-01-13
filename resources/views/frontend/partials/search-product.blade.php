<div class="search-product position-absolute left-0 top-100" style="left: 0; z-index: 1000; background-color: rgba(255, 255, 255); width: 100%; border-radius: 10px; box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.1);">
    @foreach ($products as $product)
    <div class="search-product-item mb-2 border-bottom p-2">
        <a href="{{ route('sf.product.show', $product->slug) }}">
            <div class="d-flex">
                <div class="flex-shrink-0">
                    @if ($product->thumbnail)
                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" height="30" width="30">
                    @else
                    <img src="https://placehold.co/30" alt="{{ $product->name }}" height="30" width="30">
                    @endif
                </div>
                <div class="flex-grow-1 ms-3">
                    {{ $product->name }} <br>
                    <small class="text-muted">{{ formatCurrency($product->cartPrice($product->stocks->first()->id)) }}</small>
                </div>
            </div>
        </a>
    </div>
    @endforeach
</div>
