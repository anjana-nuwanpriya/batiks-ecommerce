@extends('frontend.layouts.app')
@section('title', 'Order Complete | Nature\'s Virtue')

@section('content')

    @php
        $breadcrumbs = [['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Order Complete']];
    @endphp

    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="$breadcrumbs" />
    <section class="order-complete-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 col-xl-8">
                    <div class="card invoice-card">
                        <div class="invoice-body">
                            <!-- Success Message -->
                            <div class="text-center mb-4">
                                <div class="success-icon mb-3">
                                    <i class="las la-check-circle text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h2 class="mb-2">Order Confirmed!</h2>
                                <p class="text-muted mb-0">Thank you for your order</p>
                            </div>

                            <!-- Invoice Header Info -->
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h6 class="invoice-section-title">Bill To</h6>
                                    <div class="invoice-info">
                                        <div class="fw-bold">{{ $order->customer_name ?? ($order->user->name ?? 'Guest') }}
                                        </div>
                                        <div class="text-muted">
                                            {{ $order->customer_email ?? ($order->user->email ?? 'N/A') }}</div>
                                        <div class="text-muted">Phone: {{ $order->customer_phone ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <h6 class="invoice-section-title">Ship To</h6>
                                    <div class="invoice-info">
                                        @if ($order->shipping_address)
                                            @php
                                                $shippingData = is_string($order->shipping_address)
                                                    ? json_decode($order->shipping_address, true)
                                                    : $order->shipping_address;
                                            @endphp
                                            <div class="fw-bold">
                                                {{ $order->customer_name ?? ($order->user->name ?? 'Guest') }}</div>
                                            @if (is_array($shippingData))
                                                @if (!empty($shippingData['address']))
                                                    <div class="text-muted">{{ $shippingData['address'] }}</div>
                                                @endif
                                                @if (!empty($shippingData['city']) || !empty($shippingData['state']))
                                                    <div class="text-muted">
                                                        {{ $shippingData['city'] ?? '' }}@if (!empty($shippingData['city']) && !empty($shippingData['state']))
                                                            ,
                                                        @endif{{ $shippingData['state'] ?? '' }}
                                                    </div>
                                                @endif
                                                @if (!empty($shippingData['postal_code']))
                                                    <div class="text-muted">{{ $shippingData['postal_code'] }}</div>
                                                @endif
                                                <div class="text-muted">{{ $shippingData['country'] ?? 'Sri Lanka' }}</div>
                                                @if (!empty($shippingData['phone']))
                                                    <div class="text-muted">Phone: {{ $shippingData['phone'] }}</div>
                                                @endif
                                            @else
                                                <div class="text-muted">{{ $order->shipping_address }}</div>
                                            @endif
                                        @else
                                            <div class="text-muted">Same as billing address</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="invoice-section-title">Invoice Details</h6>
                                    <div class="invoice-info">
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Invoice Ref:</span>
                                            <span>{{ $order->code }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Date:</span>
                                            <span>{{ $order->created_at->format('M d, Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Section -->
                            <div class="mb-4">
                                <h6 class="invoice-section-title mb-3">Items</h6>
                                <div class="items-table">
                                    <!-- Desktop Header -->
                                    <div class="items-header d-none d-md-block">
                                        <div class="row">
                                            <div class="col-6">Description</div>
                                            <div class="col-2 text-center">Qty</div>
                                            <div class="col-2 text-end">Unit Price</div>
                                            <div class="col-2 text-end">Total</div>
                                        </div>
                                    </div>
                                    <div class="items-body">
                                        @php
                                            $subtotal = 0;
                                        @endphp
                                        @if ($order->items && $order->items->count() > 0)
                                            @foreach ($order->items as $item)
                                                <!-- Desktop Layout -->
                                                <div class="item-row d-none d-md-block">
                                                    <div class="row align-items-center">
                                                        <div class="col-6">
                                                            <div class="d-flex align-items-center">
                                                                <div class="item-icon me-3">
                                                                    @if ($item->product && $item->product->thumbnail)
                                                                        <img src="{{ $item->product->thumbnail }}"
                                                                            alt="{{ $item->product->name }}"
                                                                            class="rounded" height="40" width="40">
                                                                    @else
                                                                        <img src="{{ asset('assets/default.jpg') }}"
                                                                            alt="{{ $item->product->name }}"
                                                                            class="rounded" height="40" width="40">
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold">
                                                                        {{ $item->product->name ?? 'Product' }}</div>
                                                                    @if ($item->variant)
                                                                        <small
                                                                            class="text-muted">{{ $item->variant }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-2 text-center">{{ $item->quantity }}</div>
                                                        <div class="col-2 text-end">
                                                            {{ formatCurrency($item->unit_price ?? $item->cost) }}
                                                        </div>
                                                        <div class="col-2 text-end fw-bold">
                                                            {{ formatCurrency($item->total_price ?? $item->cost * $item->quantity) }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Mobile Layout -->
                                                <div class="item-row-mobile d-block d-md-none border-bottom py-3">
                                                    <div class="d-flex align-items-start">
                                                        <div class="item-icon me-3">
                                                            @if ($item->product && $item->product->thumbnail)
                                                                <img src="{{ $item->product->thumbnail }}"
                                                                    alt="{{ $item->product->name }}" class="rounded"
                                                                    height="50" width="50">
                                                            @else
                                                                <img src="{{ asset('assets/default.jpg') }}"
                                                                    alt="{{ $item->product->name }}" class="rounded"
                                                                    height="50" width="50">
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="fw-bold mb-1">
                                                                {{ $item->product->name ?? 'Product' }}</div>
                                                            @if ($item->variant)
                                                                <div class="text-muted small mb-2">{{ $item->variant }}
                                                                </div>
                                                            @endif
                                                            <div class="row text-sm">
                                                                <div class="col-4">
                                                                    <span class="text-muted">Qty:</span><br>
                                                                    <span class="fw-bold">{{ $item->quantity }}</span>
                                                                </div>
                                                                <div class="col-4">
                                                                    <span class="text-muted">Price:</span><br>
                                                                    <span>{{ formatCurrency($item->unit_price ?? $item->cost) }}</span>
                                                                </div>
                                                                <div class="col-4">
                                                                    <span class="text-muted">Total:</span><br>
                                                                    <span
                                                                        class="fw-bold text-success">{{ formatCurrency($item->total_price ?? $item->cost * $item->quantity) }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @php
                                                    $subtotal += $item->total_price;
                                                @endphp
                                            @endforeach
                                        @else
                                            <div class="text-center text-muted py-4">
                                                <i class="las la-shopping-cart me-2"></i>
                                                No order items found
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Order Totals -->
                                <div class="order-totals mt-4">
                                    <div class="row">
                                        <div class="col-md-8"></div>
                                        <div class="col-md-4 col-12">
                                            <div class="totals-section">
                                                <div class="total-line d-flex justify-content-between">
                                                    <span>Subtotal:</span>

                                                    <span>{{ formatCurrency($subtotal) }}</span>
                                                </div>
                                                <div class="total-line d-flex justify-content-between">
                                                    <span>Shipping:</span>
                                                    <span
                                                        class="text-success">{{ $order->shipping_cost > 0 ? formatCurrency($order->shipping_cost) : 'FREE' }}</span>
                                                </div>
                                                @if ($order->tax_amount > 0)
                                                    <div class="total-line d-flex justify-content-between">
                                                        <span>Tax (VAT 15%):</span>
                                                        <span>{{ formatCurrency($order->tax_amount) }}</span>
                                                    </div>
                                                @endif
                                                @if ($order->discount_amount > 0)
                                                    <div class="total-line d-flex justify-content-between">
                                                        <span>Discount:</span>
                                                        <span
                                                            class="text-danger">-{{ formatCurrency($order->discount_amount) }}</span>
                                                    </div>
                                                @endif
                                                <div
                                                    class="total-line total-amount d-flex justify-content-between border-top pt-2 mt-2">
                                                    <span class="fw-bold">Total Amount:</span>
                                                    <span
                                                        class="fw-bold text-success fs-5">{{ formatCurrency($order->grand_total) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Instructions (if payment method is bank transfer) -->
                            @if (in_array(strtolower($order->payment_method), ['bank_transfer', 'bank', 'transfer']))
                                <div class="payment-instructions">
                                    <div class="payment-header">
                                        <i class="las la-credit-card me-2"></i>
                                        <span class="fw-bold">Payment Instructions</span>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-4 mb-md-0">
                                            <h6 class="payment-section-title">Bank Transfer Details</h6>
                                            <div class="bank-details">
                                                <div
                                                    class="bank-detail-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span class="detail-label">Bank Name:</span>
                                                    <span class="detail-value d-flex align-items-center">
                                                        <span
                                                            class="me-2">{{ env('BANK_NAME', 'Commercial Bank') }}</span>
                                                        <button class="copy-icon btn btn-sm btn-outline-secondary"
                                                            onclick="copyToClipboard('{{ env('BANK_NAME', 'Commercial Bank') }}')"
                                                            title="Copy">
                                                            <i class="las la-copy"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                                <div
                                                    class="bank-detail-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span class="detail-label">Account Name:</span>
                                                    <span class="detail-value d-flex align-items-center">
                                                        <span
                                                            class="me-2 text-end">{{ env('BANK_ACCOUNT_NAME', 'Company Name Ltd') }}</span>
                                                        <button class="copy-icon btn btn-sm btn-outline-secondary"
                                                            onclick="copyToClipboard('{{ env('BANK_ACCOUNT_NAME', 'Company Name Ltd') }}')"
                                                            title="Copy">
                                                            <i class="las la-copy"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                                <div
                                                    class="bank-detail-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span class="detail-label">Account Number:</span>
                                                    <span class="detail-value d-flex align-items-center">
                                                        <span
                                                            class="me-2 fw-bold">{{ env('BANK_ACCOUNT_NUMBER', '1234567890') }}</span>
                                                        <button class="copy-icon btn btn-sm btn-outline-secondary"
                                                            onclick="copyToClipboard('{{ env('BANK_ACCOUNT_NUMBER', '1234567890') }}')"
                                                            title="Copy">
                                                            <i class="las la-copy"></i>
                                                        </button>
                                                    </span>
                                                </div>
                                                <div
                                                    class="bank-detail-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span class="detail-label">Branch:</span>
                                                    <span
                                                        class="detail-value">{{ env('BANK_BRANCH_NAME', 'Main Branch') }}</span>
                                                </div>
                                                @if (env('BANK_SWIFT_CODE'))
                                                    <div
                                                        class="bank-detail-item d-flex justify-content-between align-items-center py-2">
                                                        <span class="detail-label">SWIFT Code:</span>
                                                        <span class="detail-value d-flex align-items-center">
                                                            <span class="me-2">{{ env('BANK_SWIFT_CODE') }}</span>
                                                            <button class="copy-icon btn btn-sm btn-outline-secondary"
                                                                onclick="copyToClipboard('{{ env('BANK_SWIFT_CODE') }}')"
                                                                title="Copy">
                                                                <i class="las la-copy"></i>
                                                            </button>
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <h6 class="payment-section-title">Payment Details</h6>
                                            <div class="payment-details">
                                                <div
                                                    class="payment-detail-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span class="detail-label">Amount:</span>
                                                    <span
                                                        class="detail-value fw-bold text-success fs-5">{{ formatCurrency($order->grand_total) }}</span>
                                                </div>
                                                <div
                                                    class="payment-detail-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                    <span class="detail-label">Reference:</span>
                                                    <span
                                                        class="detail-value fw-bold">#{{ Str::padLeft($order->id, 6, '0') }}</span>
                                                </div>
                                                <div
                                                    class="payment-detail-item d-flex justify-content-between align-items-center py-2">
                                                    <span class="detail-label">Due Date:</span>
                                                    <span
                                                        class="detail-value">{{ $order->created_at->addDays(7)->format('M d, Y') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="payment-instructions-note">
                                        <div class="instruction-icon">
                                            <i class="las la-exclamation-triangle"></i>
                                        </div>
                                        <div class="instruction-content">
                                            <div class="fw-bold mb-2">Important Payment Instructions:</div>
                                            <ul class="instruction-list">
                                                <li>Please use Invoice #{{ Str::padLeft($order->id, 6, '0') }} as the
                                                    reference when making the transfer</li>
                                                <li>Payment must be received within 7 days to avoid late fees</li>
                                                @if (env('BANK_NOTE'))
                                                    <li>{{ env('BANK_NOTE') }}</li>
                                                @else
                                                    <li>Send payment confirmation to info@naturesvirtue.lk</li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Button -->
                            <div class="text-center mt-4">
                                <a href="{{ route('sf.products.list') }}" class="btn btn-style-1">
                                    <i class="las la-shopping-bag me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            function copyToClipboard(text) {
                navigator.clipboard.writeText(text).then(function() {
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'toast-success';
                    toast.innerHTML = '<i class="las la-check-circle me-2"></i>Copied to clipboard!';
                    toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 5px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;
                    document.body.appendChild(toast);

                    setTimeout(() => {
                        toast.remove();
                    }, 2000);
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                });
            }

            // Auto-hide success message after 5 seconds
            document.addEventListener('DOMContentLoaded', function() {
                const successMessage = document.querySelector('.success-animation');
                if (successMessage) {
                    setTimeout(() => {
                        successMessage.style.opacity = '0.7';
                    }, 5000);
                }
            });
        </script>
    @endpush

@endsection
