@extends('frontend.layouts.app')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Order #' . str_pad($order->id, 4, '0', STR_PAD_LEFT))
@section('content')
@push('styles')
<style>
body { --bs-primary: #00664b; --bs-primary-rgb: 0, 102, 75; }
</style>
@endpush

    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="[
        ['url' => route('home'), 'label' => 'Home'],
        ['url' => '', 'label' => 'Order History'],
        ['label' => '#' . str_pad($order->id, 4, '0', STR_PAD_LEFT)],
    ]" />

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                    @include('components.sidebar')
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <!-- Header Section -->
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h3 class="mb-2 fw-bold">Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</h3>
                                    <div class="d-flex align-items-center gap-3 text-muted">
                                        <span><i
                                                class="las la-calendar me-1"></i>{{ $order->created_at->format('d M, Y') }}</span>
                                        <span><i class="las la-box me-1"></i>{{ $order->items->count() }}
                                            {{ Str::plural('Item', $order->items->count()) }}</span>
                                        <span
                                            class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }} px-3 py-2">
                                            {{ Str::title($order->payment_status) }}
                                        </span>
                                        @if($order->waybill_no)
                                            <span class="text-muted">
                                                <i class="las la-shipping-fast me-1"></i>Waybill: {{ $order->waybill_no }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="d-flex gap-2">
                                    @if($order->waybill_no)
                                        <a href="{{ route('tracking.order', $order->id) }}" class="btn btn-success">
                                            <i class="las la-shipping-fast me-1"></i> Track Order
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.invoice', $order->id) }}" class="btn btn-outline-success"
                                        target="_blank">
                                        <i class="las la-file-invoice me-1"></i> Invoice
                                    </a>
                                    <a href="{{ route('user.order-list') }}" class="btn btn-secondary">
                                        <i class="las la-arrow-left me-1"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    @php
                        $shippingAddress = is_string($order->shipping_address)
                            ? json_decode($order->shipping_address)
                            : (object) $order->shipping_address;
                    @endphp

                    <!-- Order Status Tracker -->
                    {{-- <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Order Status</h5>
                            <div class="order-status">
                                <div class="status-line">
                                    <div class="active-line"></div>
                                </div>
                                <div class="status-points">
                                    <div class="status-point {{ $order->payment_status == 'paid' ? 'completed' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-credit-card" viewBox="0 0 16 16">
                                            <path
                                                d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z" />
                                            <path
                                                d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="status-point {{ $order->payment_status == 'paid' && $order->delivery_status == 'Waiting' || $order->delivery_status == 'Delivered' ? 'completed' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-box-seam" viewBox="0 0 16 16">
                                            <path
                                                d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5l2.404.961L10.404 2l-2.218-.887zm3.564 1.426L5.596 5 8 5.961 14.154 3.5l-2.404-.961zm3.25 1.7-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="status-point {{ $order->payment_status == 'paid' && $order->delivery_status == 'Transfer' || $order->delivery_status == 'Delivered' ? 'completed' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                                            <path
                                                d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                                        </svg>
                                    </div>
                                    <div
                                        class="status-point {{ $order->payment_status == 'paid' && $order->delivery_status == 'Delivered' ? 'completed' : '' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                            <path
                                                d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
                                            <path
                                                d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <small>Order received</small>
                                    <small>Processing</small>
                                    <small>On the way</small>
                                    <small>Delivered</small>
                                </div>
                            </div>
                        </div>
                    </div> --}}

                    <!-- Shipping Address -->
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-3">Shipping Address</h5>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-light rounded-circle p-2 me-3">
                                            <i class="las la-map-marker-alt text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $order->user->name }}</h6>
                                            <p class="text-muted mb-2">
                                                {{ $shippingAddress->address }}<br>
                                                {{ $shippingAddress->city }}, {{ $shippingAddress->state }}<br>
                                                {{ $shippingAddress->postal_code }}, {{ $shippingAddress->country }}
                                            </p>
                                            <div class="d-flex gap-4">
                                                <div>
                                                    <small class="text-muted d-block">Email</small>
                                                    <span>{{ $order->user->email }}</span>
                                                </div>
                                                <div>
                                                    <small class="text-muted d-block">Phone</small>
                                                    <span>{{ $shippingAddress->phone }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information (if bank transfer) -->
                    @if ($order->payment_method == 'bank_transfer' && $order->payment_status == 'pending')
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="las la-exclamation-triangle text-warning fs-5"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Payment Required</h5>
                                        <p class="text-muted mb-0">Please complete your bank transfer payment</p>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        @if (!$order->payment_proof)
                                            <div class="mb-4">
                                                <h6 class="fw-bold mb-3">Upload Payment Proof</h6>
                                                <form action="{{ route('user.order.upload-slip', $order->id) }}"
                                                    method="POST" class="ajax-form">
                                                    <div class="mb-3">
                                                        <x-file-uploader pondName="payment_proof" pondID="payment-proof"
                                                            pondCollection="payment_proof"
                                                            pondInstanceName="paymentProofPond"
                                                            pondLable="Upload Payment Proof" />
                                                        <div class="form-text">Please upload your payment receipt</div>
                                                        <div class="field-notice text-danger" rel="payment_proof"></div>
                                                    </div>
                                                    @csrf
                                                    <button type="submit" class="btn btn-style-1 w-100">
                                                        <i class="las la-upload me-1"></i> Upload Payment Proof
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="alert alert-success">
                                                <i class="las la-check-circle me-2"></i>Payment proof has been uploaded and
                                                is being reviewed
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="bg-light rounded-3 p-3">
                                            <h6 class="fw-bold mb-3">Bank Transfer Details</h6>
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Bank Name</small>
                                                <span class="fw-medium">{{ env('BANK_NAME') }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Account Name</small>
                                                <span class="fw-medium">{{ env('BANK_ACCOUNT_NAME') }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Account Number</small>
                                                <span class="fw-medium">{{ env('BANK_ACCOUNT_NUMBER') }}</span>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Branch</small>
                                                <span class="fw-medium">{{ env('BANK_BRANCH_NAME') }}</span>
                                            </div>
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Swift Code</small>
                                                <span class="fw-medium">{{ env('BANK_SWIFT_CODE') }}</span>
                                            </div>
                                            @if (env('BANK_NOTE'))
                                                <div class="border-top pt-2">
                                                    <small class="text-muted">{{ env('BANK_NOTE') }}</small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Order Summary -->
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Order Summary</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Payment Method:</span>
                                        <span
                                            class="fw-medium">{{ Str::title(str_replace('_', ' ', $order->payment_method)) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Payment Status:</span>
                                        <span
                                            class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                            {{ Str::title($order->payment_status) }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted">Delivery Status:</span>
                                        <span
                                            class="badge bg-{{ $order->delivery_status == 'Delivered' ? 'success' : 'info' }}">
                                            {{ Str::title($order->delivery_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    @php
                                        $subtotal = $order->items->sum(function ($item) {
                                            return $item->unit_price * $item->quantity;
                                        });
                                    @endphp
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Subtotal:</span>
                                        <span class="fw-medium">{{ formatCurrency($subtotal) }}</span>
                                    </div>
                                    @if ($order->coupon_discount > 0)
                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Discount:</span>
                                            <span
                                                class="fw-medium text-success">-{{ formatCurrency($order->coupon_discount) }}</span>
                                        </div>
                                    @endif
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Shipping:</span>
                                        <span class="fw-medium">
                                            @if ($order->shipping_cost > 0)
                                                {{ formatCurrency($order->shipping_cost) }}
                                            @else
                                                <span class="text-success">Free</span>
                                            @endif
                                        </span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold fs-5">Total:</span>
                                        <span
                                            class="fw-bold fs-5 text-primary">{{ formatCurrency($order->grand_total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">Order Items</h5>

                            <!-- Products Table -->
                            <div class="table-responsive mt-4 rounded-3 border">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">PRODUCT</th>
                                            <th>PRICE</th>
                                            <th>QUANTITY</th>
                                            <th class="text-end pe-4">SUBTOTAL</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-light rounded p-1 me-3"
                                                            style="width: 60px; height: 60px; overflow: hidden;">
                                                            @if ($item->product->thumbnail)
                                                                <img src="{{ asset($item->product->thumbnail) }}"
                                                                    alt="{{ $item->product->name }}" class="img-fluid">
                                                            @else
                                                                <img src="{{ asset('assets/default.jpg') }}"
                                                                    alt="{{ $item->product->name }}" class="img-fluid">
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <p class="mb-0 fw-medium">{{ $item->product->name }}</p>
                                                            @if ($item->variant)
                                                                <small class="text-muted">{{ $item->variant }}</small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ formatCurrency($item->unit_price) }}</td>
                                                <td>x{{ $item->quantity }}</td>
                                                <td class="text-end pe-4">{{ formatCurrency($item->total_price) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
