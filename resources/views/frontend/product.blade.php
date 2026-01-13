@extends('frontend.layouts.app')
@section('title', $product->name)
@section('content')

    @php
        $breadcrumbs = [
            ['url' => route('home'), 'label' => 'Home'],
            ['url' => route('sf.products.list'), 'label' => 'Products'],
            ['url' => '', 'label' => $product->name],
        ];

        $productStock = $product->stocks;

    @endphp

    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="$breadcrumbs" />

    <div class="product-detail py-5">
        <div class="container">
            <!-- Product Main Content -->
            <div class="product-main mb-5">
                <div class="row">
                    <!-- Product Gallery -->
                    <div class="col-md-6">
                        <div class="product-gallery">
                            <div class="main-image mb-3 position-relative" id="productImage">
                                <div class="image-preloader d-none position-absolute top-50 start-50 translate-middle"
                                    id="imagePreloader">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                                @if (!empty($product->thumbnail))
                                    <img src="{{ $product->thumbnail }}" alt="{{ $product->name }}" class="img-fluid">
                                @else
                                    <img src="{{ asset('assets/default.jpg') }}" alt="{{ $product->name }}"
                                        class="img-fluid">
                                @endif
                            </div>
                            @if ($product->gallery)
                                <div class="thumbnail-list d-flex gap-2">
                                    @foreach ($product->gallery as $image)
                                        <div class="thumb-item">
                                            <img src="{{ asset($image) }}" alt="{{ $product->name }}" class="img-fluid">
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="col-md-6">
                        <div class="product-info">

                            @if (!empty($product->sinhala_name))
                                <h1 class="product-title mb-2 notranslate">{{ $product->sinhala_name }}</h1>
                                <h3 class="h5 notranslate">{{ $product->name }}</h3>
                            @else
                                <h1 class="product-title mb-2 notranslate">{{ $product->name }}</h1>
                            @endif


                            <div class="mb-2" id="product-stock-status">
                                @php
                                    $stockStatus = $productStock->sum('qty');
                                @endphp
                                @if ($stockStatus > 0)
                                    <span class="badge bg-success">In Stock</span>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </div>

                            <div class="product-rating mb-3">
                                <div class="stars">
                                    @php
                                        $rating = $product->reviews->avg('rating');
                                    @endphp
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $rating)
                                            <i class="las la-star filled"></i>
                                        @else
                                            <i class="las la-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="rating-text">{{ $product->reviews->count() }}
                                    {{ Str::plural('Review', $product->reviews) }}</span>
                                {{-- <span class="sku ms-3">SKU: {{ $productStock->first()->sku }}</span> --}}
                            </div>

                            @php
                                $standardStock = $productStock->where('is_standard', 1)->first();
                            @endphp

                            <div class="product-price mb-4" id="product-price">
                                @if ($stockStatus <= 0)
                                    <div class="d-flex align-items-baseline gap-2">
                                        <span
                                            class="current-price fs-3 fw-bold">{{ formatCurrency($product->cartPrice($standardStock->id)) }}</span>
                                    </div>
                                @endif
                            </div>

                            <div class="product-description mb-4">
                                {!! $product->short_description !!}
                            </div>

                            {{-- @php
                                $whatsapp = get_setting('whatsapp');
                            @endphp
                            <div class="alert alert-warning mb-4">
                                <i class="las la-exclamation-triangle me-2"></i>
                                <strong>Special Note:</strong> Our online ordering system is temporarily offline. For now, please place your order through WhatsApp: <a href="https://wa.me/{{ get_setting('whatsapp') }}" target="_blank" class="text-decoration-none fw-bold">{{ get_setting('whatsapp') }}</a>
                            </div> --}}

                            <form action="{{ route('cart.add') }}" method="POST" id="add-to-cart">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">

                                <div class="product-variants-wrapper">

                                    <div class="d-flex align-items-center gap-2">
                                        @php
                                            $checkedSet = false;
                                        @endphp

                                        @foreach ($productStock as $key => $stock)
                                            <div
                                                class="product-variant-item {{ $stock->variant == 'Standard' ? 'd-none' : '' }}">
                                                @if ($stock->qty > 0)
                                                    <label
                                                        for="variant-{{ $stock->variant }}">{{ $stock->variant }}</label>
                                                    <input type="radio" name="variant" id="variant-{{ $stock->variant }}"
                                                        value="variant-{{ $stock->id }}"
                                                        {{ !$checkedSet ? 'checked' : '' }}>
                                                    @php $checkedSet = true; @endphp
                                                @else
                                                    <label for="variant-{{ $stock->variant }}" name="variant"
                                                        id="variant-{{ $stock->variant }}"
                                                        value="variant-{{ $stock->id }}"
                                                        class="product-variant-item-disabled">{{ $stock->variant }}</label>
                                                    <input type="radio" disabled>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="product-actions d-flex gap-3 mb-4 align-items-center">
                                    <div class="quantity-controls d-flex align-items-center">
                                        <button type="button" class="btn btn-link text-dark px-3 quantity-decrease"
                                            data-price="{{ $product->price }}"><i class="las la-minus"></i></button>
                                        <input type="number" name="quantity" value="1" min="1"
                                            class="form-control quantity-input">
                                        <button type="button" class="btn btn-link text-dark px-3 quantity-increase"
                                            data-price="{{ $product->price }}"><i class="las la-plus"></i></button>
                                    </div>

                                    <button type="button"
                                        class="btn btn-outline-secondary add-to-wishlist {{ auth()->check() && auth()->user()->wishlist()->where('product_id', $product->id)->exists() ? 'active' : '' }}"
                                        data-product-id="{{ $product->id }}"
                                        onclick="addToWishlist('{{ $product->slug }}')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-heart" viewBox="0 0 16 16">
                                            <path
                                                d="m8 2.748-.717-.737C5.6.281 2.514.878 1.4 3.053c-.523 1.023-.641 2.5.314 4.385.92 1.815 2.834 3.989 6.286 6.357 3.452-2.368 5.365-4.542 6.286-6.357.955-1.886.838-3.362.314-4.385C13.486.878 10.4.28 8.717 2.01L8 2.748zM8 15C-7.333 4.868 3.279-3.04 7.824 1.143c.06.055.119.112.176.171a3.12 3.12 0 0 1 .176-.17C12.72-3.042 23.333 4.867 8 15z" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="product-actions d-block d-md-flex gap-3 mb-4">
                                    @if ($stockStatus)
                                        <button type="submit"
                                            class="btn btn-style-1 d-block d-md-flex align-items-center mb-3 mb-md-0">
                                            Add to Cart
                                            <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="20"
                                                height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6">
                                                </path>
                                            </svg>

                                        </button>
                                    @endif
                                    <button type="button" class="btn btn-request" data-bs-toggle="modal"
                                        data-bs-target="#reqQuoteModal" data-product-id="{{ $product->id }}"
                                        data-product-name="{{ $product->name }}"
                                        onclick="prePopulateQuoteModal({{ $product->id }}, '{{ $product->name }}')">
                                        Request Quote
                                        <svg xmlns="http://www.w3.org/2000/svg" class="ms-2" width="20"
                                            height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </form>

                            <div class="product-meta mb-4">
                                <div class="meta-item mb-2">
                                    <span class="text-muted">Category:</span>
                                    <a href="" class="text-decoration-none">
                                        @foreach ($product->categories as $category)
                                            {{ $category->name }}@if (!$loop->last)
                                                ,
                                            @endif
                                        @endforeach
                                    </a>
                                </div>
                                {{-- <div class="meta-item">
                                <span class="text-muted">Tag:</span>
                                <div class="tag-list d-inline-block">
                                    @php
                                        $dummyTags = ['Vegetables', 'Healthy', 'Fresh', 'Organic', 'Green'];
                                    @endphp
                                    @foreach ($dummyTags as $index => $tag)
                                        <a href="#" class="text-decoration-none">{{ $tag }}</a>@if ($index < count($dummyTags) - 1),@endif
                                    @endforeach
                                </div>
                            </div> --}}
                            </div>

                            <div class="product-share">
                                <span class="text-muted">Share Item:</span>
                                <div class="social-links d-inline-block ms-2">
                                    <a href="https://facebook.com/share?url={{ url()->current() }}" target="_blank"
                                        class="text-dark me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 320 512">
                                            <path
                                                d="M279.14 288l14.22-92.66h-88.91v-60.13c0-25.35 12.42-50.06 52.24-50.06h40.42V6.26S260.43 0 225.36 0c-73.22 0-121.08 44.38-121.08 124.72v70.62H22.89V288h81.39v224h100.17V288z" />
                                        </svg>
                                    </a>
                                    <a href="https://www.tiktok.com/share?url={{ url()->current() }}" target="_blank"
                                        class="text-dark me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 448 512">
                                            <path
                                                d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z" />
                                        </svg>
                                    </a>
                                    <a href="https://wa.me/?text={{ url()->current() }}" target="_blank"
                                        class="text-dark me-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 448 512">
                                            <path
                                                d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z" />
                                        </svg>
                                    </a>

                                    <a href="https://www.instagram.com/share?url={{ url()->current() }}" target="_blank"
                                        class="text-dark">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 448 512">
                                            <path
                                                d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Tabs -->
            <div class="product-tabs mb-5">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#description">Descriptions</a>
                    </li>
                    @if (!empty($product->how_to_use))
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#how_use">How to Use</a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#feedback">Customer Feedback
                            ({{ $product->reviews->count() }})</a>
                    </li>
                </ul>

                <div class="tab-content p-3 p-md-4 border border-top-0">
                    <div class="tab-pane fade show active" id="description">
                        {!! $product->description !!}
                    </div>
                    @if (!empty($product->how_to_use))
                        <div class="tab-pane fade" id="how_use">
                            {!! $product->how_to_use !!}
                        </div>
                    @endif
                    <div class="tab-pane fade" id="feedback">
                        <!-- Add customer feedback/reviews content -->
                        <div class="review-form customer-feedback">
                            @auth
                                @if (!auth()->user()->reviews()->where('product_id', $product->id)->exists())
                                    <h5 class="mb-4">Write a Review</h5>
                                    <form action="{{ route('sf.product.review.store', ['product' => $product->id]) }}"
                                        method="POST" class="ajax-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="{{ auth()->user()->name }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contact" class="form-label">Email/Phone</label>
                                            <input type="text" class="form-control" id="contact" name="contact"
                                                value="{{ auth()->user()->email ?? auth()->user()->phone }}" readonly>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Rating</label>
                                            <div class="rating-input">
                                                @for ($i = 5; $i >= 1; $i--)
                                                    <input type="radio" id="star{{ $i }}" name="rating"
                                                        value="{{ $i }}">
                                                    <label for="star{{ $i }}"><i class="las la-star"></i></label>
                                                @endfor
                                            </div>
                                            <div class="field-notice text-danger" rel="rating"></div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comment" class="form-label">Your Review</label>
                                            <textarea class="form-control" id="comment" name="comment" rows="4"></textarea>
                                            <div class="field-notice text-danger" rel="comment"></div>
                                        </div>

                                        <button type="submit" class="btn btn-style-1">Submit Review</button>
                                    </form>
                                @endif
                            @else
                                <div class="alert alert-info">
                                    Please <a href="{{ route('user.login') }}">login</a> to write a review.
                                </div>
                            @endauth

                            @if ($product->reviews->isNotEmpty())
                                <div class="mt-4 review-summary">
                                    <h5>All Reviews</h5>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-4 mb-3 mb-md-0">
                                            <div class="rating-summary">
                                                <div class="average-rating">
                                                    <h2>{{ number_format($product->reviews->avg('rating'), 1) }}<span
                                                            class="out-of">/5</span></h2>
                                                    <div class="stars">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= round($product->reviews->avg('rating')))
                                                                <i class="las la-star"></i>
                                                            @else
                                                                <i class="lar la-star"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <p>Based on {{ $product->reviews->count() }} reviews</p>
                                                </div>
                                                <div class="rating-breakdown">
                                                    @for ($star = 5; $star >= 1; $star--)
                                                        @php
                                                            $count = $product->reviews->where('rating', $star)->count();
                                                            $percentage =
                                                                $product->reviews->count() > 0
                                                                    ? ($count / $product->reviews->count()) * 100
                                                                    : 0;
                                                        @endphp
                                                        <div class="rating-bar">
                                                            <span class="rating-label">{{ $star }} star</span>
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $percentage }}%"
                                                                    aria-valuenow="{{ $percentage }}" aria-valuemin="0"
                                                                    aria-valuemax="100"></div>
                                                            </div>
                                                            <span class="rating-count">({{ $count }})</span>
                                                        </div>
                                                    @endfor
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-lg-8">
                                            <div class="review-list">
                                                @foreach ($product->reviews()->where('is_approved', 1)->get() as $review)
                                                    <div class="review-item mb-4 pb-4 border-bottom">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <div>
                                                                <h6 class="mb-1">{{ $review->user->name }}</h6>
                                                                <div class="stars">
                                                                    @for ($i = 1; $i <= 5; $i++)
                                                                        <i
                                                                            class="las la-star {{ $i <= $review->rating ? 'text-warning' : '' }}"></i>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                            <small
                                                                class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                                        </div>
                                                        <p class="mb-0">{{ $review->comment }}</p>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if ($product->relatedProducts->count() > 0)
                <div class="related-products">
                    <h3 class="section-title mb-4">{{ __('Related Products') }}</h3>
                    <div class="row g-4">
                        @foreach ($product->relatedProducts as $relatedProduct)
                            @php
                                $relatedProduct = $relatedProduct->relatedProduct;
                            @endphp
                            @if ($relatedProduct)
                                <div class="col-md-3">
                                    @include('frontend.partials.product-card', [
                                        'product' => $relatedProduct,
                                    ])
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            const productSlug = '{{ $product->slug }}';

            function checkVariantAvailability(variantId) {
                if (!variantId) return;

                // Show preloader when image is about to change
                $('#imagePreloader').removeClass('d-none');
                $('#productImage img').addClass('opacity-50');

                $.post('{{ route('cart.variant.availability') }}', {
                    data: {
                        variant: variantId,
                        productSlug: productSlug,
                    },
                    _token: csrfToken
                }, function(response) {
                    $('#product-price').html(response.viewPrice);
                    $('.quantity-input').attr('max', response.qty);

                    if (response.image) {
                        // Create new image element to preload
                        const newImg = new Image();
                        newImg.onload = function() {
                            // Hide preloader and update image once loaded
                            $('#imagePreloader').addClass('d-none');
                            $('#productImage').html('<img src="' + response.image +
                                '" alt="{{ $product->name }}" class="img-fluid">');
                        };
                        newImg.onerror = function() {
                            // Hide preloader even if image fails to load
                            $('#imagePreloader').addClass('d-none');
                            $('#productImage img').removeClass('opacity-50');
                        };
                        newImg.src = response.image;
                    } else {
                        // Hide preloader if no image in response
                        $('#imagePreloader').addClass('d-none');
                        $('#productImage img').removeClass('opacity-50');
                    }
                }).fail(function() {
                    // Hide preloader on AJAX failure
                    $('#imagePreloader').addClass('d-none');
                    $('#productImage img').removeClass('opacity-50');
                });
            }

            // Trigger on page load
            checkVariantAvailability($('input[name="variant"]:checked').val());
            // Trigger on variant change
            $('input[name="variant"]').on('change', function() {
                checkVariantAvailability($(this).val());
            });
        });


        // Increase quantity
        $('.quantity-increase').click(function() {
            var input = $(this).siblings('.quantity-input');
            var currentValue = parseInt(input.val());
            var maxValue = parseInt(input.attr('max'));

            if (currentValue < maxValue) {
                input.val(currentValue + 1);
                $('.quantity-decrease').prop('disabled', false);
            }

            if (currentValue + 1 >= maxValue) {
                $(this).prop('disabled', true);
            }
        });

        // Decrease quantity
        $('.quantity-decrease').click(function() {
            var input = $(this).siblings('.quantity-input');
            var currentValue = parseInt(input.val());

            if (currentValue > 1) {
                input.val(currentValue - 1);
                $('.quantity-increase').prop('disabled', false);
            }

            if (currentValue - 1 <= 1) {
                $(this).prop('disabled', true);
            }
        });

        // Prevent manual input of negative numbers and respect max/min
        $('.quantity-input').on('change', function() {
            var value = parseInt($(this).val());
            var max = parseInt($(this).attr('max'));

            if (value < 1) {
                $(this).val(1);
                $('.quantity-decrease').prop('disabled', true);
                $('.quantity-increase').prop('disabled', false);
            } else if (value > max) {
                $(this).val(max);
                $('.quantity-increase').prop('disabled', true);
                $('.quantity-decrease').prop('disabled', false);
            } else {
                $('.quantity-decrease').prop('disabled', value <= 1);
                $('.quantity-increase').prop('disabled', value >= max);
            }
        });

        // Function to pre-populate quote modal with current product
        function prePopulateQuoteModal(productId, productName) {
            setTimeout(function() {
                const firstProductSelect = document.querySelector('#productsList .product-select');
                if (firstProductSelect) {
                    firstProductSelect.value = productId;

                    // Trigger change event to load variants
                    const changeEvent = new Event('change', {
                        bubbles: true
                    });
                    firstProductSelect.dispatchEvent(changeEvent);

                    // Set quantity to 1
                    const quantityInput = firstProductSelect.closest('.product-item').querySelector(
                        '.product-quantity');
                    if (quantityInput) {
                        quantityInput.value = 1;
                    }
                }
            }, 100);
        }
    </script>
@endsection
