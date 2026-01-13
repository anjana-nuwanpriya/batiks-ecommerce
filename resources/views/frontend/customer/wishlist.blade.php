@extends('frontend.layouts.app')

@section('content')

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="[['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Wishlist']]" />



    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                    @include('components.sidebar') <!-- Include the sidebar component -->
                </div>

                <!-- Main Content -->
                <div class="col-md-9 p-4 main-content">
                    @if($wishlist->count() > 0)
                        <div class="row">
                            @foreach($wishlist as $item)
                                <div class="col-md-3 mb-4">
                                    <div class="card h-100 product-card">
                                        @if($item->product->thumbnail)
                                            <img src="{{ asset($item->product->thumbnail) }}" class="card-img-top img-fluid p-2" alt="{{ $item->product->name }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <img src="{{ asset('assets/default.jpg') }}" class="card-img-top img-fluid p-2" alt="{{ $item->product->name }}" style="height: 200px; object-fit: cover;">
                                        @endif
                                        <div class="card-body pb-0">
                                            <h5 class="card-title">{{ $item->product->name }}</h5>
                                            <p class="card-text">{{ formatCurrency($item->product->cartPrice($item->product->stocks->first()->id)) }}</p>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('sf.product.show', $item->product->slug) }}" class="btn btn-style-1 font-12"> View Product </a>
                                                <button class="btn btn-danger btn-sm remove-wishlist" onclick="removeFromWishlist('{{ $item->product->slug }}')">
                                                    <i class="las la-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <h3>Your wishlist is empty</h3>
                            <p>Browse our products and add items to your wishlist</p>
                            <a href="{{ route('sf.products.list') }}" class="btn btn-style-1">Browse Products</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

@endsection
