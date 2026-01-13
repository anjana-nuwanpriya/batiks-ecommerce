@extends('frontend.layouts.app')

@section('title', 'Dashboard')

@section('content')
@push('styles')
<style>
body { --bs-primary: #00664b; --bs-primary-rgb: 0, 102, 75; }
</style>
@endpush

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="[['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Order History']]" />

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                    @include('components.sidebar') <!-- Include the sidebar component -->
                </div>

                <!-- Main Content -->
                <div class="col-md-9 p-4 main-content">
                    <!-- Order History Card -->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Order History</h5>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Waybill</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($orders as $order)
                                        <tr>
                                            <td>
                                                <span class="fw-bold">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            </td>
                                            <td>{{ $order->created_at->format('d M, Y') }}</td>
                                            <td>
                                                <span class="fw-medium">{{ formatCurrency($order->grand_total) }}</span>
                                                <br>
                                                <small class="text-muted">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</small>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column gap-1">
                                                    @if($order->payment_status == 'pending')
                                                        <span class="badge bg-warning">Payment Pending</span>
                                                    @elseif($order->payment_status == 'paid')
                                                        <span class="badge bg-success">Paid</span>
                                                    @endif

                                                    @if($order->delivery_status)
                                                        <span class="badge bg-info">{{ $order->delivery_status }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($order->waybill_no)
                                                    <div class="waybill-display">
                                                        <span class="waybill-label">Waybill:</span>
                                                        {{ $order->waybill_no }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">Not assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-1 flex-wrap">
                                                    @if($order->waybill_no)
                                                        <a href="{{ route('tracking.order', $order->id) }}"
                                                           class="btn btn-primary btn-sm"
                                                           title="Track Order">
                                                            <i class="las la-shipping-fast"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('user.order.view', $order->id) }}"
                                                       class="btn btn-outline-primary btn-sm"
                                                       title="View Details">
                                                        <i class="las la-eye"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                @if($orders->hasPages())
                                    <div class="d-flex justify-content-center mt-4">
                                        {{ $orders->links() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



@endsection
