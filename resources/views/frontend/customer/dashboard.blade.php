@extends('frontend.layouts.app')

@section('title', 'Dashboard')

@section('content')

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="[['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Dashboard']]" />

<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3">
                @include('components.sidebar')
            </div>

            <!-- Main Content -->
            <div class="col-md-9 p-4 main-content">
                <div class="row">
                   <div class="col-12 col-md-4 mb-4">
                        <div class="card shadow-sm border-0 rounded-3" style="max-width: 300px;">
                            <div class="card-body d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-1">Total Orders</h6>
                                    <h3 class="mb-0 fw-semibold">{{ $orders->count() }}</h3>
                                    <p class="text-muted small mb-0">+2 from last month</p>
                                </div>
                                <div class="text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-package h-4 w-4 text-muted-foreground"><path d="m7.5 4.27 9 5.15"></path><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"></path><path d="m3.3 7 8.7 5 8.7-5"></path><path d="M12 22V12"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-md-4 mb-4">
                        <div class="card shadow-sm border-0 rounded-3" style="max-width: 300px;">
                            <div class="card-body d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="text-muted mb-1">Wishlist Items</h6>
                                    <h3 class="mb-0 fw-semibold">{{ Auth::user()->wishlist()->count() }}</h3>
                                    <p class="text-muted small mb-0">3 back in stock</p>
                                </div>
                                <div class="text-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart h-4 w-4 text-muted-foreground"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <!-- Order History Card -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">Recent Orders</h5>
                                <p class="card-text text-muted mb-4">Your latest purchase activity</p>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead style="background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);">
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Date</th>
                                                <th>Total</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if($orders->count() > 0)
                                            @foreach ($orders as $order)
                                            <tr>
                                                <td>#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</td>
                                                <td>{{ $order->created_at->format('d M, Y') }}</td>
                                                <td class="text-success fw-bold">LKR {{ number_format($order->grand_total, 2) }} <small class="text-muted">({{ $order->items->count() }} Products)</small></td>
                                                <td>
                                                    @if($order->payment_status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                    @elseif($order->payment_status == 'paid')
                                                    <span class="badge bg-success">Paid</span>
                                                    @elseif($order->payment_status == 'failed')
                                                    <span class="badge bg-danger">Failed</span>
                                                    @endif
                                                </td>
                                                <td><a href="{{ route('user.order.view', $order->id) }}" class="btn  btn-outline-dark  btn-sm">View Details</a></td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr>
                                                <td colspan="5" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                                                        <p class="mb-0">No orders found</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                                <a href="{{ route('user.order-list') }}" class="btn btn-style-1">View All Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
