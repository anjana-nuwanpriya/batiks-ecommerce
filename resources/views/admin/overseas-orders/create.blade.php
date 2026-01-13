@extends('adminlte::page')

@section('title', 'Create Overseas Order')

@section('plugins.Select2', true)
@section('plugins.Sweetalert2', true)

@section('adminlte_css_pre')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('content_header')
    <h1>Create Overseas Order</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.overseas-orders.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="user_id">Customer</label>
                            <div class="input-group">
                                <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Customer</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-success" id="create-customer-btn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shipping_cost">Shipping Cost</label>
                            <input type="number" name="shipping_cost" id="shipping_cost"
                                   class="form-control @error('shipping_cost') is-invalid @enderror"
                                   step="0.01" min="0" value="{{ old('shipping_cost') }}" required>
                            @error('shipping_cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Shipping Address</label>
                    <div class="row">
                        <div class="col-md-6">
                            <input type="text" name="shipping_address[name]" placeholder="Full Name"
                                   class="form-control mb-2" value="{{ old('shipping_address.name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="shipping_address[phone]" placeholder="Phone"
                                   class="form-control mb-2" value="{{ old('shipping_address.phone') }}" required>
                        </div>
                        <div class="col-md-12">
                            <textarea name="shipping_address[address]" placeholder="Address"
                                      class="form-control mb-2" rows="2" required>{{ old('shipping_address.address') }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="shipping_address[city]" placeholder="City"
                                   class="form-control mb-2" value="{{ old('shipping_address.city') }}" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="shipping_address[state]" placeholder="State/Province"
                                   class="form-control mb-2" value="{{ old('shipping_address.state') }}" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="shipping_address[country]" placeholder="Country"
                                   class="form-control mb-2" value="{{ old('shipping_address.country') }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Order Items</label>
                    <div id="order-items">
                        <div class="order-item border p-3 mb-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <label>Product</label>
                                    <select name="items[0][product_id]" class="form-control product-select select2" required>
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Variant</label>
                                    <select name="items[0][variant]" class="form-control variant-select">
                                        <option value="">Select Variant</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Quantity</label>
                                    <input type="number" name="items[0][quantity]" placeholder="Qty"
                                           class="form-control quantity-input" min="1" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Unit Price</label>
                                    <input type="number" name="items[0][unit_price]" placeholder="Unit Price"
                                           class="form-control price-input" step="0.01" min="0" required>
                                </div>
                                <div class="col-md-2">
                                    <label>Stock</label>
                                    <input type="text" class="form-control stock-display" readonly placeholder="Stock">
                                </div>
                                <div class="col-md-1">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn btn-danger btn-block remove-item">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" id="add-item" class="btn btn-secondary">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Create Order</button>
                    <a href="{{ route('admin.overseas-orders.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Customer Modal -->
    <div class="modal fade" id="createCustomerModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create New Customer</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="createCustomerForm">
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="customer_name">Name</label>
                            <input type="text" id="customer_name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_email">Email</label>
                            <input type="email" id="customer_email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="customer_phone">Phone</label>
                            <input type="text" id="customer_phone" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create Customer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
let itemIndex = 1;

$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Create customer modal
    $('#create-customer-btn').click(function() {
        $('#createCustomerModal').modal('show');
    });

    // Handle customer creation
    $('#createCustomerForm').submit(function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('admin.overseas-orders.customer.create') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                name: $('#customer_name').val(),
                email: $('#customer_email').val(),
                phone: $('#customer_phone').val()
            },
            success: function(response) {
                if (response.success) {
                    // Add new customer to select
                    const newOption = new Option(
                        response.user.name + ' (' + response.user.email + ')',
                        response.user.id,
                        true,
                        true
                    );
                    $('#user_id').append(newOption).trigger('change');

                    // Close modal and reset form
                    $('#createCustomerModal').modal('hide');
                    $('#createCustomerForm')[0].reset();

                    Swal.fire('Success!', 'Customer created successfully', 'success');
                }
            },
            error: function(xhr) {
                let errors = xhr.responseJSON.errors;
                let errorMessage = 'Error creating customer';
                if (errors) {
                    errorMessage = Object.values(errors).flat().join('\n');
                }
                Swal.fire('Error!', errorMessage, 'error');
            }
        });
    });

    // Handle product selection change
    $(document).on('change', '.product-select', function() {
        const productId = $(this).val();
        const variantSelect = $(this).closest('.order-item').find('.variant-select');
        const stockDisplay = $(this).closest('.order-item').find('.stock-display');

        if (productId) {
            $.ajax({
                url: "{{ route('admin.overseas-orders.product.variants') }}",
                type: 'GET',
                data: { product_id: productId },
                success: function(response) {
                    variantSelect.empty().append('<option value="">Select Variant</option>');

                    if (response.success && response.variants.length > 0) {
                        response.variants.forEach(function(variant) {
                            variantSelect.append(
                                '<option value="' + variant.variant + '" data-stock="' + variant.qty + '">' +
                                variant.variant + ' (Stock: ' + variant.qty + ')' +
                                '</option>'
                            );
                        });
                    } else {
                        variantSelect.append('<option value="">No variants available</option>');
                    }
                }
            });
        } else {
            variantSelect.empty().append('<option value="">Select Variant</option>');
            stockDisplay.val('');
        }
    });

    // Handle variant selection change
    $(document).on('change', '.variant-select', function() {
        const selectedOption = $(this).find('option:selected');
        const stock = selectedOption.data('stock') || 0;
        const stockDisplay = $(this).closest('.order-item').find('.stock-display');
        stockDisplay.val(stock);
    });

    // Add new item
    $('#add-item').click(function() {
        const container = $('#order-items');
        const newItem = $('.order-item').first().clone();

        // Update input names and reset values
        newItem.find('input, select').each(function() {
            const name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('[0]', '[' + itemIndex + ']'));
            }
            $(this).val('');
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
        });

        // Clear variant options
        newItem.find('.variant-select').empty().append('<option value="">Select Variant</option>');
        newItem.find('.stock-display').val('');

        container.append(newItem);

        // Reinitialize Select2 for new item
        newItem.find('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        itemIndex++;
    });

    // Remove item
    $(document).on('click', '.remove-item', function() {
        if ($('.order-item').length > 1) {
            $(this).closest('.order-item').remove();
        } else {
            Swal.fire('Warning!', 'At least one item is required', 'warning');
        }
    });
});
</script>
@stop
