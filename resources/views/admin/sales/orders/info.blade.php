@extends('adminlte::page')
@section('title', 'Orders - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@push('css')
    <style>
        /* City Autocomplete Styles */
        .city-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 4px 4px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1050;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .city-suggestion-item {
            padding: 10px 15px;
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s;
        }

        .city-suggestion-item:hover,
        .city-suggestion-item.active {
            background-color: #f8f9fa;
        }

        .city-suggestion-item:last-child {
            border-bottom: none;
        }

        .form-group {
            position: relative;
        }
    </style>
@endpush

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1">{{ __('Order Details') }}</h4>
            <p class="text-muted mb-0">Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.receipt', $order->id) }}" class="btn btn-outline-dark btn-sm mr-1" target="_blank">
                <i class="fas fa-print mr-1"></i> Print Receipt
            </a>
            <a href="{{ route('admin.invoice', $order->id) }}" class="btn btn-dark btn-sm" target="_blank">
                <i class="fas fa-download mr-1"></i> Download Invoice
            </a>
        </div>
    </div>
@endsection

@section('content')
    <!-- Order Summary Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <h6 class="text-muted mb-1">Order Total</h6>
                            <h4 class="text-dark mb-0">{{ formatCurrency($order->grand_total) }}</h4>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6 class="text-muted mb-1">Order Date</h6>
                            <p class="mb-0">{{ \Carbon\Carbon::parse($order->created_at)->format('M d, Y') }}</p>
                            <small
                                class="text-muted">{{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}</small>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6 class="text-muted mb-1">Payment Status</h6>
                            @php
                                $paymentStatusClass = match ($order->payment_status) {
                                    'paid' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    'refunded' => 'info',
                                    'cancelled' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <span
                                class="badge badge-{{ $paymentStatusClass }} px-3 py-2">{{ ucfirst($order->payment_status) }}</span>
                        </div>
                        <div class="col-md-3 text-center">
                            <h6 class="text-muted mb-1">Delivery Status</h6>
                            @php
                                $deliveryStatusClass = match ($order->delivery_status ?? 'pending') {
                                    'Delivered' => 'success',
                                    'Transfer', 'Return Complete' => 'info',
                                    'Waiting' => 'warning',
                                    default => 'secondary',
                                };
                            @endphp
                            <span
                                class="badge badge-{{ $deliveryStatusClass }} px-3 py-2">{{ $order->delivery_status ?? 'Pending' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Order Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-shopping-cart mr-2 text-dark"></i>{{ __('Order Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Reference Number</label>
                                <p class="mb-0 font-weight-bold">{{ $order->code }}</p>
                            </div>
                            <div class="info-group mb-3">
                                <label class="text-muted small">Waybill Number</label>
                                @if (!empty($order->waybill_no))
                                    <p class="mb-0 font-weight-bold text-info">{{ $order->waybill_no }}</p>
                                @else
                                    @if ($order->payment_status != 'refunded' || $order->payment_status != 'cancelled')
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 text-muted mr-2">No waybill created</p>
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                id="createWaybillBtn">
                                                <i class="fas fa-plus mr-1"></i>Create Waybill
                                            </button>
                                        </div>
                                    @endif
                                @endif
                            </div>
                            <div class="info-group mb-3">
                                <label class="text-muted small">Payment Method</label>
                                <p class="mb-0">{{ Str::title($order->payment_method) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-group mb-3">
                                <label class="text-muted small">Order Status</label>
                                <x-adminlte-select2 name="order_status" id="order_status" class="select2">
                                    <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>
                                        Pending
                                    </option>
                                    <option value="cancelled"
                                        {{ $order->payment_status == 'cancelled' ? 'selected' : '' }}>
                                        Cancelled</option>
                                    <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>
                                        Failed</option>
                                    <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid
                                    </option>
                                    <option value="refunded" {{ $order->payment_status == 'refunded' ? 'selected' : '' }}>
                                        Refunded</option>
                                </x-adminlte-select2>
                            </div>
                            <div class="info-group mb-3">
                                <label class="text-muted small">Delivery Status</label>
                                <x-adminlte-select2 name="delivery_status" id="delivery_status" class="select2">
                                    <option value="Waiting" {{ $order->delivery_status == 'Waiting' ? 'selected' : '' }}>
                                        Waiting</option>
                                    <option value="Transfer" {{ $order->delivery_status == 'Transfer' ? 'selected' : '' }}>
                                        Transfer</option>
                                    <option value="Return Complete"
                                        {{ $order->delivery_status == 'Return Complete' ? 'selected' : '' }}>Return
                                        Complete
                                    </option>
                                    <option value="Delivered"
                                        {{ $order->delivery_status == 'Delivered' ? 'selected' : '' }}>Delivered</option>
                                </x-adminlte-select2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-user mr-2 text-success"></i>{{ __('Customer Information') }}</h5>
                </div>
                <div class="card-body">
                    @php
                        // Handle shipping address - it might be JSON string or already decoded array
                        $shippingAddress = is_string($order->shipping_address)
                            ? json_decode($order->shipping_address, true)
                            : $order->shipping_address;
                        $shippingAddress = $shippingAddress ?? [];
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            @if (!empty($order->user))
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Customer Name</label>
                                    <p class="mb-0 font-weight-bold">{{ $order->user->name }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Email Address</label>
                                    <p class="mb-0">{{ $order->user->email }}</p>
                                </div>
                                @if (!empty($order->user->phone))
                                    <div class="info-group mb-3">
                                        <label class="text-muted small">Phone Number</label>
                                        <p class="mb-0">{{ $order->user->phone }}</p>
                                    </div>
                                @endif
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Customer Since</label>
                                    <p class="mb-0">
                                        {{ \Carbon\Carbon::parse($order->user->created_at)->format('M d, Y') }}</p>
                                </div>
                            @else
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Customer Name</label>
                                    <p class="mb-0 font-weight-bold">{{ $shippingAddress['name'] ?? 'N/A' }} <span
                                            class="badge badge-secondary">Guest</span></p>
                                </div>
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Email Address</label>
                                    <p class="mb-0">{{ $shippingAddress['email'] ?? 'N/A' }}</p>
                                </div>
                                @if (!empty($shippingAddress['phone']))
                                    <div class="info-group mb-3">
                                        <label class="text-muted small">Phone Number</label>
                                        <p class="mb-0">{{ $shippingAddress['phone'] }}</p>
                                    </div>
                                @endif
                            @endif
                        </div>
                        <div class="col-md-6">
                            <div class="info-group">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="text-muted small mb-0">Shipping Address</label>
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-toggle="modal"
                                        data-target="#editShippingModal">
                                        <i class="fas fa-edit mr-1"></i>Edit
                                    </button>
                                </div>
                                <div class="shipping-address bg-light p-3 rounded">
                                    <p class="mb-1 font-weight-bold">{{ $shippingAddress['name'] ?? 'N/A' }}</p>
                                    <p class="mb-1">{{ $shippingAddress['address'] ?? 'N/A' }}</p>
                                    <p class="mb-1">{{ $shippingAddress['city'] ?? 'N/A' }},
                                        {{ $shippingAddress['state'] ?? 'N/A' }}
                                        {{ $shippingAddress['postal_code'] ?? '' }}</p>
                                    <p class="mb-1">{{ $shippingAddress['country'] ?? 'Sri Lanka' }}</p>
                                    <p class="mb-0 text-muted"><i
                                            class="fas fa-phone mr-1"></i>{{ $shippingAddress['phone'] ?? 'N/A' }}</p>
                                </div>
                            </div>
                            @if (!empty($order->note))
                                <div class="info-group mt-3">
                                    <label class="text-muted small">Special Notes</label>
                                    <div class="bg-warning-light p-3 rounded">
                                        <p class="mb-0">{{ $order->note }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Proof Section -->
            @if ($order->payment_method == 'bank_transfer' && $order->hasMedia('payment_proof'))
                @php
                    $paymentProof = $order->getFirstMedia('payment_proof');
                @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-bottom">
                        <h5 class="mb-0"><i class="fas fa-receipt mr-2 text-info"></i>{{ __('Payment Proof') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Payment Method</label>
                                    <p class="mb-0">{{ Str::title($order->payment_method) }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <label class="text-muted small">Upload Date</label>
                                    <p class="mb-0">{{ $paymentProof->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="info-group mb-3">
                                    <label class="text-muted small">File Info</label>
                                    <p class="mb-0">
                                        <span class="badge badge-light">{{ $paymentProof->name }}</span><br>
                                        <small class="text-muted">
                                            {{ strtoupper($paymentProof->extension) }} •
                                            {{ $paymentProof->human_readable_size }}
                                        </small>
                                    </p>
                                </div>
                                <div class="info-group">
                                    <a href="{{ $paymentProof->getUrl() }}" class="btn btn-outline-primary btn-sm"
                                        target="_blank">
                                        <i class="fas fa-external-link-alt mr-1"></i>View Full Size
                                    </a>
                                    <a href="{{ $paymentProof->getUrl() }}" class="btn btn-outline-secondary btn-sm ml-2"
                                        download="{{ $paymentProof->name }}">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-group">
                                    <label class="text-muted small">Payment Proof Preview</label>
                                    <div class="payment-proof-container bg-light p-3 rounded text-center">
                                        @if (
                                            $paymentProof->hasGeneratedConversion('thumb') ||
                                                in_array(strtolower($paymentProof->extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                            <img src="{{ $paymentProof->hasGeneratedConversion('thumb') ? $paymentProof->getUrl('thumb') : $paymentProof->getUrl() }}"
                                                alt="Payment Proof" class="img-fluid rounded shadow-sm"
                                                style="max-height: 300px; cursor: pointer;"
                                                onclick="window.open('{{ $paymentProof->getUrl() }}', '_blank')">
                                        @else
                                            <div class="text-center py-4">
                                                @php
                                                    $iconClass = match (strtolower($paymentProof->extension)) {
                                                        'pdf' => 'fas fa-file-pdf text-danger',
                                                        'doc', 'docx' => 'fas fa-file-word text-primary',
                                                        'xls', 'xlsx' => 'fas fa-file-excel text-success',
                                                        'zip', 'rar' => 'fas fa-file-archive text-warning',
                                                        default => 'fas fa-file-alt text-muted',
                                                    };
                                                @endphp
                                                <i class="{{ $iconClass }} fa-3x mb-3"></i>
                                                <p class="mb-2 font-weight-bold">{{ $paymentProof->name }}</p>
                                                <small class="text-muted">
                                                    {{ strtoupper($paymentProof->extension) }} File •
                                                    {{ $paymentProof->human_readable_size }}
                                                </small>
                                                <div class="mt-3">
                                                    <a href="{{ $paymentProof->getUrl() }}"
                                                        class="btn btn-sm btn-outline-primary" target="_blank">
                                                        <i class="fas fa-eye mr-1"></i>Open File
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Order Items -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-bottom">
                    <h5 class="mb-0"><i class="fas fa-box mr-2 text-warning"></i>{{ __('Order Summary') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <tbody>
                                @foreach ($order->items as $item)
                                    <tr class="border-bottom">
                                        <td class="py-3">
                                            <div class="d-flex">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">
                                                        <a href="{{ route('sf.product.show', $item->product->slug) }}"
                                                            target="_blank" class="text-dark text-decoration-none">
                                                            {{ $item->product->name }}
                                                        </a>
                                                    </h6>
                                                    <small class="text-muted">{{ $item->variant }}</small>
                                                    <div class="mt-1">
                                                        <span class="badge badge-light">Qty: {{ $item->quantity }}</span>
                                                        <span class="text-muted mx-2">×</span>
                                                        <span
                                                            class="font-weight-bold">{{ formatCurrency($item->unit_price) }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-right">
                                                    <p class="mb-0 font-weight-bold text-dark">
                                                        {{ formatCurrency($item->total_price) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping Cost:</span>
                            <span class="font-weight-bold">{{ formatCurrency($order->shipping_cost) }}</span>
                        </div>
                        @if ($order->handling_fee > 0)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Convenience Fee:</span>
                                <span class="font-weight-bold">{{ formatCurrency($order->handling_fee) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between">
                            <span class="h6 mb-0">Grand Total:</span>
                            <span
                                class="h5 mb-0 text-dark font-weight-bold">{{ formatCurrency($order->grand_total) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Shipping Address Modal -->
    <div class="modal fade" id="editShippingModal" tabindex="-1" role="dialog"
        aria-labelledby="editShippingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editShippingModalLabel">
                        <i class="fas fa-edit mr-2"></i>Edit Shipping Address
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="editShippingForm">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_name">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_name" name="name"
                                        value="{{ $shippingAddress['name'] ?? '' }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_phone">Phone Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="shipping_phone" name="phone"
                                        value="{{ $shippingAddress['phone'] ?? '' }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="shipping_email">Email Address</label>
                            <input type="email" class="form-control" id="shipping_email" name="email"
                                value="{{ $shippingAddress['email'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label for="shipping_address">Street Address <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="shipping_address" name="address" rows="3" required>{{ $shippingAddress['address'] ?? '' }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_city">City <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" name="city" class="form-control city-autocomplete"
                                            id="shipping_city" placeholder="Start typing city name..."
                                            value="{{ $shippingAddress['city'] ?? '' }}" autocomplete="off" required>
                                        <div class="city-suggestions" id="shippingCitySuggestions"
                                            style="display: none;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_state">State/Province</label>
                                    <input type="text" class="form-control" id="shipping_state" name="state"
                                        value="{{ $shippingAddress['state'] ?? '' }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_postal_code">Postal Code</label>
                                    <input type="text" class="form-control" id="shipping_postal_code"
                                        name="postal_code" value="{{ $shippingAddress['postal_code'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shipping_country">Country</label>
                                    <input type="text" class="form-control" id="shipping_country" name="country"
                                        value="{{ $shippingAddress['country'] ?? 'Sri Lanka' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Update Address
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // City Autocomplete for Shipping Address Modal
        let shippingCityDebounceTimer;
        let shippingCitySuggestions = [];
        let shippingCityCurrentFocus = -1;

        // Initialize city autocomplete when modal is shown
        $('#editShippingModal').on('shown.bs.modal', function() {
            setupShippingCityAutocomplete();
        });

        function setupShippingCityAutocomplete() {
            const cityInput = document.getElementById('shipping_city');
            const suggestionsDiv = document.getElementById('shippingCitySuggestions');

            if (!cityInput || !suggestionsDiv) return;

            // Input event listener
            $(cityInput).off('input.cityAutocomplete').on('input.cityAutocomplete', function() {
                const query = this.value.trim();

                clearTimeout(shippingCityDebounceTimer);

                if (query.length < 2) {
                    $(suggestionsDiv).hide().empty();
                    return;
                }

                shippingCityDebounceTimer = setTimeout(() => {
                    searchShippingCities(query);
                }, 300);
            });

            // Keyboard navigation
            $(cityInput).off('keydown.cityAutocomplete').on('keydown.cityAutocomplete', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    shippingCityCurrentFocus++;
                    addActiveShippingCityItem();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    shippingCityCurrentFocus--;
                    addActiveShippingCityItem();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (shippingCityCurrentFocus > -1 && shippingCitySuggestions[shippingCityCurrentFocus]) {
                        selectShippingCity(shippingCitySuggestions[shippingCityCurrentFocus]);
                    }
                } else if (e.key === 'Escape') {
                    $(suggestionsDiv).hide().empty();
                }
            });

            // Handle blur
            $(cityInput).off('blur.cityAutocomplete').on('blur.cityAutocomplete', function() {
                setTimeout(() => {
                    $(suggestionsDiv).hide();
                }, 150);
            });
        }

        function searchShippingCities(query) {
            $.ajax({
                url: '{{ route('api.cities.search') }}',
                method: 'GET',
                data: {
                    q: query
                },
                success: function(data) {
                    shippingCitySuggestions = data;
                    displayShippingCitySuggestions(data);
                },
                error: function(xhr, status, error) {
                    console.error('City search error:', error);
                    $('#shippingCitySuggestions').hide().empty();
                }
            });
        }

        function displayShippingCitySuggestions(cities) {
            const suggestionsDiv = $('#shippingCitySuggestions');

            if (!cities || cities.length === 0) {
                suggestionsDiv.hide().empty();
                return;
            }

            let html = '';
            cities.forEach((city, index) => {
                html += `<div class="city-suggestion-item" data-index="${index}">${city.label}</div>`;
            });

            suggestionsDiv.html(html).show();
            shippingCityCurrentFocus = -1;
        }

        // Handle city suggestion clicks
        $(document).on('click', '#shippingCitySuggestions .city-suggestion-item', function() {
            const index = $(this).data('index');
            if (shippingCitySuggestions[index]) {
                selectShippingCity(shippingCitySuggestions[index]);
            }
        });

        function selectShippingCity(city) {
            console.log(11111111);
            $('#shipping_city').val(city.value);
            $('#shippingCitySuggestions').hide().empty();
            shippingCityCurrentFocus = -1;
        }

        function addActiveShippingCityItem() {
            const items = $('#shippingCitySuggestions .city-suggestion-item');

            items.removeClass('active');

            if (shippingCityCurrentFocus >= items.length) shippingCityCurrentFocus = 0;
            if (shippingCityCurrentFocus < 0) shippingCityCurrentFocus = items.length - 1;

            if (items[shippingCityCurrentFocus]) {
                $(items[shippingCityCurrentFocus]).addClass('active');
            }
        }

        $('#order_status').on('change', function() {
            $.ajax({
                url: "{{ route('order.status.update', $order->id) }}",
                type: 'PUT',
                data: {
                    status: $(this).val()
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Order status updated successfully',
                        icon: 'success'
                    });
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'Error',
                        text: xhr.responseJSON.message,
                        icon: 'error'
                    });
                }
            });
        });

        $('#delivery_status').on('change', function() {
            $.ajax({
                url: "{{ route('order.delivery.status.update', $order->id) }}",
                type: 'PUT',
                data: {
                    delivery_status: $(this).val()
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Success',
                        text: 'Delivery status updated successfully',
                        icon: 'success'
                    });
                },
                error: function(response) {
                    Swal.fire({
                        title: 'Error',
                        text: 'Delivery status update failed',
                        icon: 'error'
                    });
                }
            });
        });

        // Edit Shipping Address functionality
        $('#editShippingForm').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Disable submit button and show loading
            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Updating...');

            // Collect form data
            const formData = {
                name: $('#shipping_name').val(),
                phone: $('#shipping_phone').val(),
                email: $('#shipping_email').val(),
                address: $('#shipping_address').val(),
                city: $('#shipping_city').val(),
                state: $('#shipping_state').val(),
                postal_code: $('#shipping_postal_code').val(),
                country: $('#shipping_country').val()
            };

            $.ajax({
                url: "{{ route('admin.order.update.shipping', $order->id) }}",
                type: 'PUT',
                data: {
                    shipping_address: formData
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Shipping address updated successfully',
                            icon: 'success'
                        }).then(() => {
                            // Close modal and reload page to show updated address
                            $('#editShippingModal').modal('hide');
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message || 'Failed to update shipping address',
                            icon: 'error'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to update shipping address';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join(', ');
                    }

                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error'
                    });
                },
                complete: function() {
                    // Re-enable submit button
                    submitBtn.prop('disabled', false);
                    submitBtn.html(originalText);
                }
            });
        });

        // Create Waybill functionality
        $('#createWaybillBtn').on('click', function() {
            const button = $(this);
            const originalText = button.html();

            // Show service selection dialog
            Swal.fire({
                title: 'Select Courier Service',
                text: 'Choose which courier service to use for creating the waybill:',
                icon: 'question',
                showCancelButton: true,
                showDenyButton: true,
                confirmButtonText: '<i class="fas fa-truck mr-1"></i>PromptXpress',
                denyButtonText: '<i class="fas fa-shipping-fast mr-1"></i>Fardar',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#007bff',
                denyButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                customClass: {
                    confirmButton: 'btn btn-primary mx-1',
                    denyButton: 'btn btn-success mx-1',
                    cancelButton: 'btn btn-secondary mx-1'
                },
                buttonsStyling: false
            }).then((result) => {
                let selectedService = null;

                if (result.isConfirmed) {
                    selectedService = 'PromptXpress';
                } else if (result.isDenied) {
                    selectedService = 'Fardar';
                } else {
                    return; // User cancelled
                }

                // Show confirmation dialog with selected service
                Swal.fire({
                    title: 'Create Waybill',
                    text: `This will create a waybill for this order using ${selectedService} service.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, create waybill'
                }).then((confirmResult) => {
                    if (confirmResult.isConfirmed) {
                        // Disable button and show loading
                        button.prop('disabled', true);
                        button.html('<i class="fas fa-spinner fa-spin mr-1"></i>Creating...');

                        $.ajax({
                            url: "{{ route('admin.order.create.waybill', $order->id) }}",
                            type: 'POST',
                            data: {
                                courier_service: selectedService
                            },
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire({
                                        title: 'Success!',
                                        text: `Waybill created successfully with ${selectedService}: ` +
                                            response.waybill_no,
                                        icon: 'success'
                                    }).then(() => {
                                        // Reload page to show the waybill number
                                        location.reload();
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Error',
                                        text: response.message,
                                        icon: 'error'
                                    });
                                    // Re-enable button
                                    button.prop('disabled', false);
                                    button.html(originalText);
                                }
                            },
                            error: function(xhr) {
                                let errorMessage = 'Failed to create waybill';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }

                                Swal.fire({
                                    title: 'Error',
                                    text: errorMessage,
                                    icon: 'error'
                                });

                                // Re-enable button
                                button.prop('disabled', false);
                                button.html(originalText);
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
