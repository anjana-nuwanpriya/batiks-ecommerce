@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Create Admin Order - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h5>{{ __('Create Order') }}</h5>

        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Create Order') }}</li>
            </ol>
        </div>
    </div>


@endsection

@section('content')

    <div class=" py-4">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0">Customer Order</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.order.store') }}" method="POST" class="ajax-form">
                            @csrf

                            <!-- Customer Selection Section -->
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <x-adminlte-select2 name="customer" label="Select Customer" id="userSelect">
                                            <option value="">Select Customer</option>
                                            @foreach ($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }} -
                                                    {{ $user->phone ? $user->phone : $user->email }}
                                                </option>
                                            @endforeach
                                        </x-adminlte-select2>
                                        <small class="text-danger field-notice" style="position: relative; top: -20px"
                                            rel="customer"></small>
                                    </div>

                                    <div class="custom-control custom-checkbox mb-3">
                                        <input type="checkbox" class="custom-control-input" id="createNewUser">
                                        <label class="custom-control-label" for="createNewUser">Create New User</label>
                                    </div>

                                    <div class="section-divider"></div>

                                    <!-- Address Section -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="mb-0 font-weight-bold">Shipping Address</label>
                                        <button type="button" class="btn btn-outline-dark btn-add" data-toggle="modal"
                                            data-target="#addAddressModal">
                                            <i class="fas fa-plus mr-1"></i> Add Address
                                        </button>
                                    </div>

                                    <div id="addressContainer" class="row mb-4">
                                        <!-- Address cards would be dynamically added here -->
                                        <div class="alert alert-light border w-100">
                                            No addresses added yet. Click "Add Address" to add a shipping location.
                                        </div>

                                        <small class="text-danger field-notice" rel="selected_address_id"></small>
                                    </div>

                                    <div class="section-divider"></div>

                                    <!-- Products Section -->
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <label class="mb-0 font-weight-bold">Products</label>
                                        <button type="button" class="btn btn-outline-dark btn-add" data-toggle="modal"
                                            data-target="#addProductModal">
                                            <i class="fas fa-plus mr-1"></i> Add Product
                                        </button>
                                    </div>

                                    <div id="productsContainer">
                                        <!-- Product cards would be dynamically added here -->
                                        <div class="alert alert-light border">
                                            No products added yet. Click "Add Product" to start building your order.
                                        </div>

                                        <small class="text-danger field-notice" rel="products"></small>
                                    </div>

                                    <div class="section-divider"></div>


                                </div>

                                <!-- Order Summary Section -->
                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Order Summary</h6>
                                        </div>
                                        <div class="card-body">
                                            <p class="mb-1">Items: <span class="float-right" id="itemCount">0</span></p>
                                            <p class="mb-1">Subtotal: <span class="float-right" id="subtotal">LKR
                                                    0.00</span></p>
                                            <p class="mb-3">Shipping Cost: <span class="float-right" id="shipping_fee">LKR
                                                    0.00</span></p>
                                            <div class="section-divider my-2"></div>
                                            <p class="font-weight-bold">Total: <span class="float-right" id="total">LKR
                                                    0.00</span></p>

                                            <!-- Payment Method Section -->
                                            <div class="mt-4 border-top pt-3">
                                                <label class="mb-2">Payment Method</label>
                                                <div class="custom-control custom-radio mb-2">
                                                    <input type="radio" id="payment_cod" name="payment_method"
                                                        class="custom-control-input" value="COD" checked>
                                                    <label class="custom-control-label" for="payment_cod">
                                                        Cash on Delivery (COD)
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-radio mb-2">
                                                    <input type="radio" id="payment_cash" name="payment_method"
                                                        class="custom-control-input" value="CASH">
                                                    <label class="custom-control-label" for="payment_cash">
                                                        Cash Payment
                                                    </label>
                                                </div>
                                                <div class="custom-control custom-radio mb-3">
                                                    <input type="radio" id="payment_bank" name="payment_method"
                                                        class="custom-control-input" value="BANK">
                                                    <label class="custom-control-label" for="payment_bank">
                                                        Bank Transfer
                                                    </label>
                                                </div>
                                                <small class="text-danger field-notice" rel="payment_method"></small>

                                                <!-- Payment Reference Field -->
                                                <div class="form-group mt-3" id="payment_reference_group"
                                                    style="display: none;">
                                                    <label for="payment_reference">Payment Reference</label>
                                                    <input type="text" name="payment_reference" id="payment_reference"
                                                        class="form-control"
                                                        placeholder="Enter payment reference/transaction ID">
                                                    <small class="text-muted">Enter transaction ID, receipt number, or
                                                        reference</small>
                                                    <small class="text-danger field-notice"
                                                        rel="payment_reference"></small>
                                                </div>

                                                <!-- Payment Status Field -->
                                                <div class="form-group mt-3" id="payment_status_group"
                                                    style="display: none;">
                                                    <label for="payment_status">Payment Status</label>
                                                    <select name="payment_status" id="payment_status"
                                                        class="form-control">
                                                        <option value="pending">Pending</option>
                                                        <option value="paid">Paid</option>
                                                    </select>
                                                    <small class="text-danger field-notice" rel="payment_status"></small>
                                                </div>
                                            </div>

                                            <!-- Courier Partner Section -->
                                            <div class="mt-4 border-top pt-3">
                                                <label class="mb-2">Courier Partner</label>
                                                <select name="courier_partner" id="courier_partner" class="form-control">
                                                    @php $availableCouriers = getAvailableCourierServices(); @endphp
                                                    @if(empty($availableCouriers))
                                                        <option value="">No courier services available - Please enable in settings</option>
                                                    @else
                                                        @foreach($availableCouriers as $key => $value)
                                                            <option value="{{ $key }}" {{ $loop->first ? 'selected' : '' }}>{{ $value }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                <small class="text-danger field-notice" rel="courier_partner"></small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <button type="submit" class="btn btn-dark btn-block">Place Order</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modal')

    <!-- New User Modal -->
    <div class="modal fade" id="newUserModal" tabindex="-1" role="dialog" aria-labelledby="newUserModalLabel"
        aria-hidden="true">
        <form action="{{ route('admin.customer.store') }}" method="POST">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newUserModalLabel">Create New User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">Name <small class="text-danger">*</small></label>
                                    <input type="text" name="name" class="form-control" id="name">
                                    <div class="field-notice text-danger" rel="name"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone <small class="text-danger">*</small></label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        placeholder="77XXXXXXXX">
                                    <div class="field-notice text-danger" rel="phone"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" id="email">
                                    <div class="field-notice text-danger" rel="email"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="field-notice text-danger" rel="error"></div>
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark" id="saveNewUser">Save User</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" role="dialog" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <form action="{{ route('admin.customer.store.address') }}" method="POST">
            @csrf
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">Add New Address</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address">Address <span class="text-danger">*</span></label>
                                    <textarea name="address" class="form-control" id="address" rows="2"></textarea>
                                    <div class="field-notice text-danger" rel="address"></div>
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="zip_code">Zip/Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" name="zip_code" class="form-control" id="zip_code">
                                    <div class="field-notice text-danger" rel="zip_code"></div>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="cityInput">City <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="text" name="city" class="form-control city-autocomplete"
                                            id="cityInput" placeholder="Start typing city name..." autocomplete="off">
                                        <div class="city-suggestions" id="citySuggestions" style="display: none;"></div>
                                    </div>
                                    {{-- <small class="text-muted">
                                        <button type="button" class="btn btn-link btn-sm p-0"
                                            onclick="testCitySearch()">Test API</button>
                                        | <span id="cityStatus">Ready</span>
                                    </small> --}}
                                    <div class="field-notice text-danger" rel="city"></div>
                                </div>
                            </div>
                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="state">Province / State <span class="text-danger">*</span></label>
                                    <input type="text" name="state" class="form-control" id="state">
                                    <div class="field-notice text-danger" rel="state"></div>
                                </div>
                            </div> --}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="country">Country <span class="text-danger">*</span></label>
                                    <select name="country" class="form-control" id="country">
                                        <option value="">Select Country</option>
                                        <option value="Sri Lanka" selected>Sri Lanka</option>
                                    </select>
                                    <div class="field-notice text-danger" rel="country"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="phone">Phone <span class="text-danger">*</span></label>
                                    <input type="tel" name="phone" class="form-control" id="phone">
                                    <div class="field-notice text-danger" rel="phone"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-dark">Save Address</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="productSearch">Search Product</label>
                                <div class="position-relative">
                                    <input type="text" id="productSearch" class="form-control"
                                        placeholder="Type to search products..." autocomplete="off">
                                    <div id="productDropdown" class="dropdown-menu w-100"
                                        style="max-height: 300px; overflow-y: auto; display: none;">
                                        <!-- Search results will appear here -->
                                    </div>
                                </div>
                                <input type="hidden" name="product" id="productSelect">
                                <small class="text-danger" rel="product"></small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="variantSection" class="d-none">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Select Variant</label>
                                    <div id="variantOptions" class="d-flex flex-wrap gap-2 variant-option">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-dark" id="addProductToOrder">Add to Order</button>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <style>
        .product-search-item:hover {
            background-color: #f8f9fa;
        }

        #productDropdown {
            position: absolute;
            top: 100%;
            left: 0;
            z-index: 1000;
            border: 1px solid #dee2e6;
            border-radius: 0.25rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        #productDropdown .dropdown-item {
            padding: 0.5rem 1rem;
            cursor: pointer;
            border-bottom: 1px solid #f8f9fa;
        }

        #productDropdown .dropdown-item:last-child {
            border-bottom: none;
        }

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

@push('js')
    <script>
        $(document).ready(function() {
            // City Autocomplete using event delegation (works with modals)
            let cityDebounceTimer;
            let citySuggestions = [];
            let cityCurrentFocus = -1;

            // Use event delegation to handle city input
            $(document).on('input', '#cityInput', function() {
                const query = $(this).val().trim();
                const suggestionsDiv = $('#citySuggestions');

                console.log('City input event:', query);

                clearTimeout(cityDebounceTimer);

                if (query.length < 2) {
                    suggestionsDiv.hide().empty();
                    return;
                }

                cityDebounceTimer = setTimeout(() => {
                    searchCities(query);
                }, 300);
            });

            // Handle keyboard navigation
            $(document).on('keydown', '#cityInput', function(e) {
                const suggestionsDiv = $('#citySuggestions');

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    cityCurrentFocus++;
                    addActiveCityItem();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    cityCurrentFocus--;
                    addActiveCityItem();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (cityCurrentFocus > -1 && citySuggestions[cityCurrentFocus]) {
                        selectCityItem(citySuggestions[cityCurrentFocus]);
                    }
                } else if (e.key === 'Escape') {
                    suggestionsDiv.hide().empty();
                }
            });

            // Handle blur
            $(document).on('blur', '#cityInput', function() {
                setTimeout(() => {
                    $('#citySuggestions').hide();
                }, 150);
            });

            function searchCities(query) {
                console.log('Searching cities:', query);
                $('#cityStatus').text('Searching...');

                $.ajax({
                    url: '{{ route('api.cities.search') }}',
                    method: 'GET',
                    data: {
                        q: query
                    },
                    success: function(data) {
                        console.log('City search results:', data);
                        citySuggestions = data;
                        displayCitySuggestions(data);
                        $('#cityStatus').text(`Found ${data.length} cities`);
                    },
                    error: function(xhr, status, error) {
                        console.error('City search error:', error);
                        $('#citySuggestions').hide().empty();
                        $('#cityStatus').text('Search failed');
                    }
                });
            }

            function displayCitySuggestions(cities) {
                const suggestionsDiv = $('#citySuggestions');

                if (!cities || cities.length === 0) {
                    suggestionsDiv.hide().empty();
                    return;
                }

                let html = '';
                cities.forEach((city, index) => {
                    html += `<div class="city-suggestion-item" data-index="${index}">${city.label}</div>`;
                });

                suggestionsDiv.html(html).show();
                cityCurrentFocus = -1;
            }

            // Handle city suggestion clicks
            $(document).on('click', '.city-suggestion-item', function() {
                const index = $(this).data('index');
                if (citySuggestions[index]) {
                    selectCityItem(citySuggestions[index]);
                }
            });

            function selectCityItem(city) {
                $('#cityInput').val(city.value);
                $('#citySuggestions').hide().empty();
                cityCurrentFocus = -1;
                console.log('Selected city:', city);
            }

            function addActiveCityItem() {
                const items = $('.city-suggestion-item');

                items.removeClass('active');

                if (cityCurrentFocus >= items.length) cityCurrentFocus = 0;
                if (cityCurrentFocus < 0) cityCurrentFocus = items.length - 1;

                if (items[cityCurrentFocus]) {
                    $(items[cityCurrentFocus]).addClass('active');
                }
            }

            // Test function for debugging
            window.testCitySearch = function() {
                console.log('Testing city search API...');
                $.ajax({
                    url: '{{ route('api.cities.search') }}',
                    method: 'GET',
                    data: {
                        q: 'Ka'
                    },
                    success: function(data) {
                        console.log('Test results:', data);
                        alert('API test successful! Found ' + data.length +
                            ' cities. Check console for details.');
                    },
                    error: function(xhr, status, error) {
                        console.error('Test error:', error);
                        alert('API test failed: ' + error);
                    }
                });
            };

            $('#createNewUser').change(function() {
                if (this.checked) {
                    $('#newUserModal').modal('show');
                    $('.select2').prop('disabled', true);
                } else {
                    $('.select2').prop('disabled', false);
                }
            });

            $('#newUserModal').on('hidden.bs.modal', function() {
                if ($('#createNewUser').is(':checked')) {
                    $('#createNewUser').prop('checked', false);
                    $('.select2').prop('disabled', false);
                }
            });

            // Reset product modal when closed
            $('#addProductModal').on('hidden.bs.modal', function() {
                $('#productSearch').val('');
                $('#productSelect').val('');
                $('#productDropdown').hide();
                $('#variantSection').addClass('d-none');
                $('#variantOptions').empty();
            });

            // Handle customer address loading when user changes
            $('#userSelect').on('change', function() {
                let userId = $(this).val();
                if (userId) {
                    $.ajax({
                        url: '{{ route('admin.customer.address') }}',
                        type: 'GET',
                        data: {
                            user_id: userId
                        },
                        success: function(response) {
                            if (response.success) {
                                // Clear existing addresses
                                $('#addressContainer').empty();

                                if (response.addresses.length > 0) {
                                    // Add each address to the container
                                    response.addresses.forEach(function(address) {
                                        let addressCard = `
                                            <div class="col-md-6 mb-3">
                                                <div class="card border-primary">
                                                    <div class="card-body position-relative">
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input" name="selected_address"
                                                                id="address_${address.id}" value="${address.id}">
                                                            <label class="custom-control-label d-block pl-4" for="address_${address.id}">
                                                                <p class="font-weight-bold mb-1">
                                                                    <i class="fas fa-map-marker-alt mr-1"></i> ${address.address}
                                                                </p>
                                                                <p class="mb-1 text-muted">${address.city},</p>
                                                                <p class="mb-1 text-muted">${address.country}</p>
                                                                <p class="mb-0"><i class="fas fa-phone mr-1"></i>${address.phone}</p>
                                                            </label>
                                                        </div>

                                                        <button type="button" class="btn btn-outline-danger btn-sm position-absolute"
                                                            style="top: 10px; right: 10px; z-index: 1;"
                                                            onclick="deleteAddress(${address.id})" title="Delete Address">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        `;
                                        $('#addressContainer').append(addressCard);
                                    });

                                    // Add change event listener for address selection
                                    $('input[name="selected_address"]').on('change',
                                        function() {
                                            calculateShippingCost();
                                        });
                                } else {
                                    $('#addressContainer').html(`
                                        <div class="alert alert-light border w-100">
                                            No addresses found for this customer.
                                        </div>
                                    `);
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Error loading addresses:', xhr);
                            $('#addressContainer').html(`
                                <div class="alert alert-danger">
                                    Error loading addresses. Please try again.
                                </div>
                            `);
                        }
                    });
                } else {
                    // Clear addresses when no user is selected
                    $('#addressContainer').html(`
                        <div class="alert alert-light border">
                            No addresses added yet. Click "Add Address" to add a shipping location.
                        </div>
                    `);
                }
            });

            // Handle new user creation
            $('#newUserModal form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');
                let data = form.serialize();

                $(this).find('.field-notice').text('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        form.find('button[type="submit"]').addClass('btn-loading').prop(
                            'disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {

                            swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success'
                            });

                            // Create new option
                            let newOption = new Option(response.user.name + ' - ' + response
                                .user.phone, response.user.id, true, true);

                            // Append it to the select
                            $('#userSelect').append(newOption).trigger('change');

                            // Close modal and reset form
                            $('#newUserModal').modal('hide');
                            form[0].reset();

                            // Uncheck create new user checkbox
                            $('#createNewUser').prop('checked', false);
                            $('.select2').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('[rel="' + field + '"]').text(errors[field][0]);
                        }
                    },
                    complete: function() {
                        form.find('button[type="submit"]').removeClass('btn-loading').prop(
                            'disabled', false);
                    }
                });
            });

            // Handle address saving
            $('#addAddressModal form').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                let url = form.attr('action');

                // Check if user is selected
                if (!$('#userSelect').val()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Please Select Customer',
                        text: 'You need to select a customer before adding an address',
                        confirmButtonText: 'OK'
                    });
                    return false;
                }

                let data = form.serialize() + '&user_id=' + $('#userSelect').val();

                $(this).find('.field-notice').text('');

                $.ajax({
                    url: url,
                    type: 'POST',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    beforeSend: function() {
                        form.find('button[type="submit"]').addClass('btn-loading').prop(
                            'disabled', true);
                    },
                    success: function(response) {
                        if (response.success) {
                            swal.fire({
                                title: 'Success',
                                text: response.message,
                                icon: 'success'
                            });

                            // Close modal and reset form
                            $('#addAddressModal').modal('hide');
                            // Trigger change event on userSelect to refresh address list
                            $('#userSelect').trigger('change');
                            form[0].reset();
                        }
                    },
                    error: function(xhr) {
                        // Handle validation errors
                        let errors = xhr.responseJSON.errors;
                        for (let field in errors) {
                            $('[rel="' + field + '"]').text(errors[field][0]);
                        }
                    },
                    complete: function() {
                        form.find('button[type="submit"]').removeClass('btn-loading').prop(
                            'disabled', false);
                    }
                });
            });

            // Initialize custom product search
            let searchTimeout;
            let selectedProductId = null;

            $('#productSearch').on('input', function() {
                let query = $(this).val().trim();

                if (query.length < 2) {
                    $('#productDropdown').hide();
                    return;
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    searchProducts(query);
                }, 300);
            });

            function searchProducts(query) {
                $.ajax({
                    url: '{{ route('admin.product.search') }}',
                    type: 'GET',
                    data: {
                        search: query,
                        page: 1
                    },
                    success: function(response) {
                        displaySearchResults(response.items);
                    },
                    error: function() {
                        $('#productDropdown').hide();
                    }
                });
            }

            function displaySearchResults(products) {
                let dropdown = $('#productDropdown');
                dropdown.empty();

                if (products.length === 0) {
                    dropdown.append('<div class="dropdown-item text-muted">No products found</div>');
                } else {
                    products.forEach(function(product) {
                        let item = $('<a href="#" class="dropdown-item product-search-item" data-id="' +
                            product.id + '">' + product.name + '</a>');
                        dropdown.append(item);
                    });
                }

                dropdown.show();
            }

            // Handle product selection from dropdown
            $(document).on('click', '.product-search-item', function(e) {
                e.preventDefault();
                let productId = $(this).data('id');
                let productName = $(this).text();

                $('#productSearch').val(productName);
                $('#productSelect').val(productId);
                $('#productDropdown').hide();

                // Trigger the existing change event
                $('#productSelect').trigger('change');
            });

            // Hide dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#productSearch, #productDropdown').length) {
                    $('#productDropdown').hide();
                }
            });

            // Clear selection when input is cleared
            $('#productSearch').on('keyup', function() {
                if ($(this).val().trim() === '') {
                    $('#productSelect').val('');
                    $('#variantSection').addClass('d-none');
                    $('#variantOptions').empty();
                }
            });

            // Product selection and variant handling
            $('#productSelect').on('change', function() {
                let productId = $(this).val();
                if (productId) {
                    $.ajax({
                        url: '{{ route('admin.product.variants') }}',
                        type: 'GET',
                        data: {
                            product_id: productId
                        },
                        success: function(response) {
                            if (response.success) {
                                $('#variantSection').removeClass('d-none');
                                $('#variantOptions').empty();

                                response.variants.forEach(function(variant) {
                                    let checked = variant.variant === 'Standard' ?
                                        'checked' : '';
                                    let display = variant.variant === 'Standard' ?
                                        'd-none' : '';
                                    let disabled = variant.qty < 1 ? 'disabled' : '';
                                    let outOfStockText = variant.qty < 1 ?
                                        '<span class="text-danger small">Out of Stock</span>' :
                                        '';
                                    let labelClass = variant.qty < 1 ? 'opacity-50' :
                                        '';

                                    let variantOption = `
                                        <div class="mr-2 hover-bg-light ${display}">
                                            <input type="radio" class="" name="variant"
                                                id="variant_${variant.id}" value="${variant.id}"
                                                data-stock="${variant.qty}"
                                                ${checked} ${disabled}>
                                            <label class="d-flex flex-column border justify-content-center align-items-center p-2 ${labelClass}" for="variant_${variant.id}">
                                                <span class="font-weight-medium">${variant.variant}</span>
                                                <span class="badge badge-pill badge-light text-muted mb-1">
                                                    <i class="fas fa-cubes mr-1 small"></i>${variant.qty} available
                                                </span>
                                                ${outOfStockText}
                                            </label>
                                        </div>`;

                                    $('#variantOptions').append(variantOption);
                                });


                                // Set max quantity based on selected variant
                                $('input[name="variant"]').on('change', function() {
                                    let maxQty = $(this).data('stock');
                                    $('.quantity-input').attr('max', maxQty);
                                });
                            }
                        }
                    });
                } else {
                    $('#variantSection').addClass('d-none');
                }
            });

            // Quantity controls
            $('.quantity-decrease').click(function() {
                let input = $(this).siblings('.quantity-input');
                let value = parseInt(input.val());
                if (value > 1) {
                    input.val(value - 1);
                }
            });

            $('.quantity-increase').click(function() {
                let input = $(this).siblings('.quantity-input');
                let value = parseInt(input.val());
                let max = parseInt(input.attr('max'));
                if (value < max) {
                    input.val(value + 1);
                }
            });

            // Add product to order
            $('#addProductToOrder').click(function() {
                let productId = $('#productSelect').val();
                let variantId = $('input[name="variant"]:checked').val();
                let quantity = 1;

                if (!productId || !variantId) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Information',
                        text: 'Please select a product and variant',
                        confirmButtonText: 'OK'
                    });
                    return;
                }

                $.ajax({
                    url: '{{ route('admin.order.add.product') }}',
                    type: 'POST',
                    data: {
                        product_id: productId,
                        variant_id: variantId,
                        quantity: quantity,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Add product card to container
                            let productCard = `
                            <div class="card mb-2 product-item px-2 py-2" data-product-id="${productId}" data-variant-id="${variantId}">
                                <input type="hidden" name="product_id[]" value="${productId}">
                                <input type="hidden" name="variant_id[]" value="${variantId}">

                                <div class="row align-items-center">
                                    <!-- Product Info -->
                                    <div class="col-md-5">
                                        <h6 class="mb-1">${response.product.name}</h6>
                                        <small class="text-muted">Variant: ${response.variant.variant}</small>
                                    </div>

                                    <!-- Quantity Controller -->
                                    <div class="col-md-3">
                                        <div class="input-group input-group-sm w-75">
                                            <div class="input-group-prepend">
                                                <button type="button" class="btn btn-outline-secondary quantity-decrease">
                                                    <i class="fas fa-minus"></i>
                                                </button>
                                            </div>
                                            <input type="number" class="form-control text-center quantity-input"
                                                value="${quantity}" min="1" name="quantity[]" max="${response.variant.qty}">
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary quantity-increase">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price & Total -->
                                    <div class="col-md-3 text-right">
                                        <p style="font-size:12.8px" class="mb-1">Price: LKR ${response.price.toFixed(2)}</small><br>
                                        <p style="font-weight: 600;" class="mb-0">Total: LKR ${(response.price * quantity).toFixed(2)}</strong>
                                    </div>

                                    <!-- Delete Button -->
                                    <div class="col-md-1 text-right">
                                        <button type="button" class="btn btn-sm btn-danger delete-product">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            `;


                            if ($('#productsContainer .alert').length) {
                                $('#productsContainer').empty();
                            }
                            $('#productsContainer').append(productCard);

                            // Update order summary
                            updateOrderSummary();
                            // Calculate shipping cost when product is added
                            calculateShippingCost();

                            // Close modal and reset form
                            $('#addProductModal').modal('hide');
                            $('#productSearch').val('');
                            $('#productSelect').val('');
                            $('#productDropdown').hide();
                            $('#variantSection').addClass('d-none');
                            $('#variantOptions').empty();
                            $('.quantity-input').val(1);
                        }
                    }
                });
            });

            // Update quantity control styles
            $(document).on('click', '.product-item .quantity-decrease', function() {
                let input = $(this).closest('.input-group').find('.quantity-input');
                let value = parseInt(input.val());
                if (value > 1) {
                    input.val(value - 1);
                    updateProductTotal($(this).closest('.product-item'));
                    updateOrderSummary();
                    calculateShippingCost();
                }
            });

            $(document).on('click', '.product-item .quantity-increase', function() {
                let input = $(this).closest('.input-group').find('.quantity-input');
                let value = parseInt(input.val());
                let max = parseInt(input.attr('max'));
                if (value < max) {
                    input.val(value + 1);
                    updateProductTotal($(this).closest('.product-item'));
                    updateOrderSummary();
                    calculateShippingCost();
                }
            });

            $(document).on('change', '.product-item .quantity-input', function() {
                let value = parseInt($(this).val());
                let max = parseInt($(this).attr('max'));
                let min = parseInt($(this).attr('min'));

                if (value < min) {
                    $(this).val(min);
                } else if (value > max) {
                    $(this).val(max);
                }

                updateProductTotal($(this).closest('.product-item'));
                updateOrderSummary();
                calculateShippingCost();
            });

            $(document).on('click', '.product-item .delete-product', function() {
                $(this).closest('.product-item').remove();
                updateOrderSummary();
                calculateShippingCost();

                // Show empty message if no products
                if ($('.product-item').length === 0) {
                    $('#productsContainer').html(`
                        <div class="alert alert-light border">
                            No products added yet. Click "Add Product" to start building your order.
                        </div>
                    `);
                }
            });

            function updateProductTotal(productItem) {
                let quantity = parseInt(productItem.find('.quantity-input').val());
                let price = parseFloat(productItem.find('.text-right p:first').text().replace('Price: LKR ', ''));
                let total = price * quantity;
                productItem.find('.text-right p:last').text('Total: LKR ' + total.toFixed(2));
            }

            function updateOrderSummary() {
                let totalItems = 0;
                let subtotal = 0;

                $('.product-item').each(function() {
                    let quantity = parseInt($(this).find('.quantity-input').val());
                    let price = parseFloat($(this).find('.text-right p:first').text().replace('Price: LKR ',
                        ''));
                    totalItems += quantity;
                    subtotal += price * quantity;
                });

                $('#itemCount').text(totalItems);
                $('#subtotal').text('LKR ' + subtotal.toFixed(2));
                $('#total').text('LKR ' + subtotal.toFixed(2));
            }

            function calculateShippingCost() {

                let totalWeight = 0;
                $('.product-item').each(function() {
                    let productId = $(this).find('input[name="product_id[]"]').val();
                    let variantId = $(this).find('input[name="variant_id[]"]').val();
                    let quantity = $(this).find('input[name="quantity[]"]').val();
                });

                let selectedAddressId = $('input[name="selected_address"]:checked').val();

                $.ajax({
                    url: '{{ route('admin.order.calculate.shipping') }}',
                    type: 'POST',
                    data: {
                        product_ids: $('input[name="product_id[]"]').map(function() {
                            return $(this).val();
                        }).get(),
                        variant_ids: $('input[name="variant_id[]"]').map(function() {
                            return $(this).val();
                        }).get(),
                        quantity: $('input[name="quantity[]"]').map(function() {
                            return $(this).val();
                        }).get(),
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            subtotal = parseFloat($('#subtotal').text().replace('LKR ', ''));
                            // Handle successful shipping calculation
                            let shippingCost = parseFloat(response.shipping_cost);
                            $('#shipping_fee').text('LKR ' + shippingCost.toFixed(2));
                            $('#total').text('LKR ' + (subtotal + shippingCost).toFixed(2));
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Failed to calculate shipping cost'
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Something went wrong calculating shipping cost'
                        });
                    }
                });
            }

            // Handle payment method changes
            $('input[name="payment_method"]').on('change', function() {
                let paymentMethod = $(this).val();

                if (paymentMethod === 'CASH' || paymentMethod === 'BANK') {
                    $('#payment_reference_group').show();
                    $('#payment_status_group').show();

                    // Make reference required for bank transfers
                    if (paymentMethod === 'BANK') {
                        $('#payment_reference').attr('placeholder',
                            'Enter bank transfer reference/transaction ID');
                    } else {
                        $('#payment_reference').attr('placeholder', 'Enter payment reference (optional)');
                    }
                } else {
                    $('#payment_reference_group').hide();
                    $('#payment_status_group').hide();
                    $('#payment_reference').removeAttr('required');
                    $('#payment_reference').val('');
                    $('#payment_status').val('pending');
                }
            });

            // Add address deletion functionality
            window.deleteAddress = function(addressId) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('admin.customer.delete.address') }}',
                            type: 'POST',
                            data: {
                                address_id: addressId,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Address has been deleted.',
                                        'success'
                                    );
                                    // Refresh the address list
                                    $('#userSelect').trigger('change');
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        response.message || 'Something went wrong.',
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    'Something went wrong.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            };
        });
    </script>

    @include('common.scripts')
@endpush
