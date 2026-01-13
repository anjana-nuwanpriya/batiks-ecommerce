@extends('frontend.layouts.app')
@section('title', $title)
@section('description', $description)

@section('content')
    <!-- Page Banner -->
    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="$breadcrumbs" />


    <div class="container py-5">
        <!-- Mobile Filter Toggle Button -->
        <div class="d-md-none mb-3">
            <button class="btn" type="button" data-bs-toggle="offcanvas" data-bs-target="#filterOffcanvas"
                aria-controls="filterOffcanvas">
                <i class="las la-filter me-2"></i> Filter
            </button>
        </div>
        <div class="row">

            <!-- Offcanvas for Mobile -->
            <div class="offcanvas offcanvas-start" tabindex="-1" id="filterOffcanvas"
                aria-labelledby="filterOffcanvasLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="filterOffcanvasLabel">Filter Products</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="filter-sidebar mb-3">
                        <div class="filter-header">
                            <h5 class="filter-title">{{ __('Availability') }}</h5>
                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="inStock"
                                        onchange="filterStock(this.checked ? 'in_stock' : '');"
                                        {{ request('stock') == 'in_stock' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="inStock">
                                        In Stock
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-sidebar mb-3">
                        <div class="filter-header">
                            <h5 class="filter-title">{{ __('Featured') }}</h5>
                            <div class="mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="featuredProducts"
                                        onchange="filterFeatured(this.checked ? 'featured' : '');"
                                        {{ request('filter') == 'featured' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="featuredProducts">
                                        Featured Products
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="filter-sidebar">
                        <!-- Categories Section -->
                        <div class="filter-section">
                            <div class="filter-header">
                                <h5 class="filter-title"><a href="{{ route('sf.products.list') }}">{{ __('All Categories') }}</a></h5>
                            </div>
                            <div class="mt-3">
                                @if (isset($categoryTree) && !empty($categoryTree))
                                    @if ($categories)
                                        <div class="mb-3 border-bottom pb-3">
                                            @foreach ($categories as $category)
                                                <label class="form-check-label d-flex justify-content-between fs-13 gap-2"
                                                    for="vegetables">
                                                    <a href="{{ route('sf.products.list', ['category' => $category->slug, 'q' => $queryTerm]) }}"
                                                        class="text-dark d-flex align-items-center gap-2">
                                                        <svg width="12" height="12" viewBox="0 0 24 24"
                                                            fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round">
                                                            <path d="M9 18l6-6-6-6" />
                                                        </svg> {!! $category->name !!}
                                                    </a>
                                                </label>
                                            @endforeach
                                            <div class="ms-4 mt-2">
                                                @foreach ($categoryTree as $subCategory)
                                                    <div class="mb-2">
                                                        <a href="{{ route('sf.products.list', ['category' => $subCategory['slug'], 'q' => $queryTerm]) }}"
                                                            class="text-dark d-flex align-items-center gap-2">
                                                            <svg width="10" height="10" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path d="M9 18l6-6-6-6" />
                                                            </svg> {!! $subCategory['name'] !!}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    @foreach ($categories as $category)
                                        <div class="mb-3 border-bottom pb-3">
                                            <label class="form-check-label d-flex justify-content-between fs-13 gap-2"
                                                for="vegetables">
                                                <a href="{{ route('sf.products.list', ['category' => $category->slug, 'q' => $queryTerm]) }}"
                                                    class="text-dark d-flex align-items-center gap-2">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round">
                                                        <path d="M9 18l6-6-6-6" />
                                                    </svg> {!! $category->name !!}
                                                </a>
                                            </label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Desktop Sidebar -->
            <div class="col-12 col-md-4 col-lg-3 d-none d-md-block">
                <div class="filter-sidebar mb-3">
                    <div class="filter-header">
                        <h5 class="filter-title">{{ __('Availability') }}</h5>
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="inStock"
                                    onchange="filterStock(this.checked ? 'in_stock' : '');"
                                    {{ request('stock') == 'in_stock' ? 'checked' : '' }}>
                                <label class="form-check-label" for="inStock">
                                    In Stock
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="filter-sidebar mb-3">
                    <div class="filter-header">
                        <h5 class="filter-title">{{ __('Featured') }}</h5>
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="featuredProducts"
                                    onchange="filterFeatured(this.checked ? 'featured' : '');"
                                    {{ request('filter') == 'featured' ? 'checked' : '' }}>
                                <label class="form-check-label" for="featuredProducts">
                                    Featured Products
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter-sidebar">
                    <!-- Categories Section -->
                    <div class="filter-section">
                        <div class="filter-header">
                            <h5 class="filter-title"><a href="{{ route('sf.products.list') }}">{{ __('Categories') }}</a>
                            </h5>
                        </div>
                        <div class="mt-3">
                            <!-- Breadcrumb for Category Navigation -->
                            @if (isset($selectedCategory) && $selectedCategory)
                                <div class="mb-3 border-bottom pb-3">
                                    <a href="{{ route('sf.products.list', ['q' => $queryTerm]) }}"
                                        class="text-dark d-flex align-items-center gap-2">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <path d="M15 18l-6-6 6-6" />
                                        </svg>
                                        Back to All Categories
                                    </a>
                                </div>
                                @if ($selectedCategory->parent_id)
                                    <div class="mb-3 border-bottom pb-3">
                                        <a href="{{ route('sf.products.list', ['category' => $parentCategory->slug, 'q' => $queryTerm]) }}"
                                            class="text-dark d-flex align-items-center gap-2">
                                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <path d="M15 18l-6-6 6-6" />
                                            </svg>
                                            Back to {!! $parentCategory->name !!}
                                        </a>
                                    </div>
                                @endif
                            @endif

                            <!-- Current Level Categories -->
                            @if ($categories)
                                @foreach ($categories as $category)
                                    <div class="mb-3 border-bottom pb-3">
                                        <label class="form-check-label d-flex justify-content-between fs-13 gap-2"
                                            for="category-{{ $category->id }}">
                                            <a href="{{ route('sf.products.list', ['category' => $category->slug, 'q' => $queryTerm]) }}"
                                                class="text-dark d-flex align-items-center gap-2">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M9 18l6-6-6-6" />
                                                </svg>
                                                {!! $category->name !!}
                                            </a>
                                        </label>
                                    </div>
                                @endforeach
                            @endif

                            <!-- Subcategories (if a category is selected) -->
                            @if (isset($categoryTree) && !empty($categoryTree))
                                <div class="ms-4 mt-2">
                                    @foreach ($categoryTree as $subCategory)
                                        <div class="mb-3 border-bottom pb-3">
                                            <a href="{{ route('sf.products.list', ['category' => $subCategory['slug'], 'q' => $queryTerm]) }}"
                                                class="text-dark d-flex align-items-center gap-2">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none"
                                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path d="M9 18l6-6-6-6" />
                                                </svg>
                                                {!! $subCategory['name'] !!}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-8 col-lg-9">
                <div class="d-md-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="search-heading mb-2">{!! $heading !!}</h4>
                        <p class="text-muted mb-0">{{ $products->total() }} items found</p>
                    </div>
                    <div class="d-flex align-items-center sort-by">
                        <span class="me-3" style="color: #666;text-wrap-mode: nowrap;">Sort by:</span>
                        <select class="form-select" onchange="filterSort(this.value);">
                            <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_asc" {{ $sort == 'price_asc' ? 'selected' : '' }}>Price (low to high)
                            </option>
                            <option value="price_desc" {{ $sort == 'price_desc' ? 'selected' : '' }}>Price (high to low)
                            </option>
                        </select>
                    </div>
                </div>


                <div class="row">
                    @if ($products->count() > 0)
                        @foreach ($products as $product)
                            <div class="col-12 col-md-4 col-lg-3 mb-3">
                                @include('frontend.partials.product-card', ['product' => $product])
                            </div>
                        @endforeach
                        <div class="col-12 mt-4">
                            {{ $products->links('pagination::bootstrap-5') }}
                        </div>
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="120" height="120"
                                    viewBox="0 0 24 24" class="mb-4">
                                    <path fill="#ccc"
                                        d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z"
                                        style="animation: searchAnimation 2s infinite" />
                                </svg>
                                <p class="h5 text-muted">No products found</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </div>

@endsection

@section('scripts')
    <script>
        function filterSort(value) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('sort', value);
            window.location.href = currentUrl.toString();
        }

        function filterStock(value) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('stock', value);
            window.location.href = currentUrl.toString();
        }

        function filterFeatured(value) {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('filter', value);
            window.location.href = currentUrl.toString();
        }
    </script>
@endsection
