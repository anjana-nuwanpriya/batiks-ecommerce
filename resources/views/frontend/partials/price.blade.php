<div class="d-flex align-items-baseline gap-2">
    <span class="current-price fs-3 fw-bold">{{ formatCurrency($sellingPrice) }}</span>
    @if ($originalPrice > 0 && $originalPrice > $sellingPrice)
        <span class="regular-price text-muted text-decoration-line-through">{{ formatCurrency($originalPrice) }}</span>
        <span class="discount bg-light text-dark">
            {{ round((($originalPrice - $sellingPrice) / $originalPrice) * 100) }}% Off
        </span>
    @endif
</div>
