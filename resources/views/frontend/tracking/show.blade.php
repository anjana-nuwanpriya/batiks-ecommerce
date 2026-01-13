@extends('frontend.layouts.app')

@section('title', 'Tracking - ' . $waybillNo)

@section('content')
    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="[
        ['url' => route('home'), 'label' => 'Home'],
        ['url' => route('tracking.index'), 'label' => 'Track Order'],
        ['label' => $waybillNo],
    ]" />

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <h2 class="fw-bold mb-2">Order Tracking</h2>
                        <p class="text-muted">Waybill Number: <span class="fw-medium text-success">{{ $waybillNo }}</span>
                        </p>
                    </div>

                    @if (isset($trackingData['error']))
                        <!-- Error State -->
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4 text-center">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="las la-exclamation-triangle text-danger" style="font-size: 2.5rem;"></i>
                                </div>
                                <h4 class="fw-bold text-danger mb-3">Tracking Information Unavailable</h4>
                            </div>
                        </div>
                    @else
                        <!-- Order Information -->
                        @if (isset($order))
                            <div class="card shadow-sm border-0 rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5 class="fw-bold mb-3">Order Details</h5>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Order Number:</span>
                                                <span
                                                    class="fw-medium">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Order Date:</span>
                                                <span class="fw-medium">{{ $order->created_at->format('d M, Y') }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Total Amount:</span>
                                                <span
                                                    class="fw-medium">{{ formatCurrency($order->grand_total) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h5 class="fw-bold mb-3">Delivery Information</h5>
                                            @php
                                                $shippingAddress = is_string($order->shipping_address)
                                                    ? json_decode($order->shipping_address)
                                                    : (object) $order->shipping_address;
                                            @endphp
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Recipient:</span>
                                                <span class="fw-medium">{{$shippingAddress->name }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Phone:</span>
                                                <span class="fw-medium">{{ $shippingAddress->phone ?? 'N/A' }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">City:</span>
                                                <span class="fw-medium">{{ $shippingAddress->city ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Tracking Timeline -->
                        <div class="card shadow-sm border-0 rounded-4 mb-4">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold mb-0">Tracking Timeline</h5>
                                </div>

                                <div id="tracking-timeline">
                                    @if (is_array($trackingData) && count($trackingData) > 0)
                                        <div class="tracking-timeline">
                                            @foreach ($trackingData as $index => $status)
                                                <div class="timeline-item {{ $index === 0 ? 'active' : '' }}">
                                                    <div class="timeline-marker">
                                                        <div class="timeline-dot"></div>
                                                        @if ($index < count($trackingData) - 1)
                                                            <div class="timeline-line"></div>
                                                        @endif
                                                    </div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="fw-bold mb-1 text-dark">
                                                                {{ $status['status'] ?? 'Status Update' }}</h6>
                                                            <div class="text-end">
                                                                @if (isset($status['statusDate']))
                                                                    <small class="text-success fw-medium d-block">
                                                                        <i class="las la-clock me-1"></i>
                                                                        {{ \Carbon\Carbon::parse($status['statusDate'])->format('d M, Y') }}
                                                                    </small>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($status['statusDate'])->format('H:i A') }}
                                                                    </small>
                                                                @elseif(isset($status['dateTime']))
                                                                    <small class="text-success fw-medium d-block">
                                                                        <i class="las la-clock me-1"></i>
                                                                        {{ \Carbon\Carbon::parse($status['dateTime'])->format('d M, Y') }}
                                                                    </small>
                                                                    <small class="text-muted">
                                                                        {{ \Carbon\Carbon::parse($status['dateTime'])->format('H:i A') }}
                                                                    </small>
                                                                @else
                                                                    <small class="text-muted">N/A</small>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <div class="status-details">
                                                            @if (isset($status['branchName']) && !empty($status['branchName']))
                                                                <div class="mb-2">
                                                                    <span
                                                                        class="badge bg-primary bg-opacity-10 text-primary px-3 py-2">
                                                                        <i
                                                                            class="las la-building me-1"></i>{{ $status['branchName'] }}
                                                                    </span>
                                                                </div>
                                                            @endif

                                                            @if (isset($status['location']) && !empty($status['location']))
                                                                <p class="text-muted mb-1">
                                                                    <i class="las la-map-marker-alt me-1 text-danger"></i>
                                                                    <span
                                                                        class="fw-medium">{{ $status['location'] }}</span>
                                                                </p>
                                                            @endif

                                                            @if (isset($status['remarks']) && !empty($status['remarks']))
                                                                <p class="text-muted mb-0">
                                                                    <i class="las la-info-circle me-1 text-info"></i>
                                                                    {{ $status['remarks'] }}
                                                                </p>
                                                            @endif

                                                            @if (isset($status['description']) && !empty($status['description']))
                                                                <p class="text-muted mb-0">
                                                                    <i class="las la-file-alt me-1 text-secondary"></i>
                                                                    {{ $status['description'] }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4">
                                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                                style="width: 60px; height: 60px;">
                                                <i class="las la-clock text-muted fs-4"></i>
                                            </div>
                                            <h6 class="fw-bold">No Tracking Information Available</h6>
                                            <p class="text-muted">Tracking information will be updated once your order is
                                                processed.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Current Status Card -->
                        @if (is_array($trackingData) && count($trackingData) > 0)
                            @php $currentStatus = $trackingData[0]; @endphp
                            <div class="card shadow-sm border-0 rounded-4 mb-4">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="las la-shipping-fast text-success fs-4"></i>
                                                </div>
                                                <div>
                                                    <h5 class="fw-bold mb-1">Current Status</h5>
                                                    <p class="text-success fw-medium mb-1">
                                                        {{ $currentStatus['status'] ?? 'Processing' }}</p>
                                                    @if (isset($currentStatus['location']))
                                                        <small class="text-muted">
                                                            <i
                                                                class="las la-map-marker-alt me-1"></i>{{ $currentStatus['location'] }}
                                                        </small>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                            @if (isset($currentStatus['statusDate']))
                                                <small class="text-muted d-block">Last Updated</small>
                                                <span
                                                    class="fw-medium text-success">{{ \Carbon\Carbon::parse($currentStatus['statusDate'])->format('d M, Y H:i') }}</span>
                                                @if (isset($currentStatus['branchName']) && !empty($currentStatus['branchName']))
                                                    <div class="mt-1">
                                                        <small class="badge bg-primary bg-opacity-10 text-success">
                                                            {{ $currentStatus['branchName'] }}
                                                        </small>
                                                    </div>
                                                @endif
                                            @elseif(isset($currentStatus['dateTime']))
                                                <small class="text-muted d-block">Last Updated</small>
                                                <span
                                                    class="fw-medium text-success">{{ \Carbon\Carbon::parse($currentStatus['dateTime'])->format('d M, Y H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/tracking.js') }}"></script>
    <script>
        // Page-specific tracking configuration
        document.addEventListener('DOMContentLoaded', function() {
            // Add waybill data attribute for JavaScript access
            const timelineElement = document.getElementById('tracking-timeline');
            if (timelineElement) {
                timelineElement.setAttribute('data-waybill', '{{ $waybillNo }}');
            }
        });
    </script>
@endsection
