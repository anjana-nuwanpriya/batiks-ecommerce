
@extends('adminlte::page')
@section('title', 'Products - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('All Products') }}</h5>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Products') }}</li>
            </ol>
        </div>
    </div>

    <div class="d-flex justify-content-end mt-4">
        <a href="{{ route('product.create') }}" class="btn btn-dark btn-sm text-right">
            {{ __('Create Product') }}
        </a>
    </div>

@endsection

@section('content')
    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    ['label' => '<input type="checkbox" id="selectAll">', 'width' => 5, 'no-export' => true, 'escape' => false],
                    ['label' => '', 'width' => 2, 'no-export' => true], // Drag handle column
                    ['label' => 'Sort Order', 'width' => 8, 'no-export' => true], // Sort order column
                    'ID',
                    ['label' => 'Name', 'width' => 20],
                    'Stock',
                    'Price',
                    ['label' => 'Featured', 'width' => 10],
                    ['label' => 'Status', 'width' => 10],
                    ['label' => 'Actions', 'no-export' => true, 'width' => 5],
                ];

                $config = [
                    'order' => [], // Disable default ordering for drag-and-drop
                    'columns' => [
                        ['orderable' => false], // Checkbox column
                        ['orderable' => false], // Drag handle column
                        ['orderable' => false], // Sort order column
                        null, // ID
                        null, // Name
                        null, // Stock
                        null, // Price
                        null, // Featured
                        null, // Status
                        ['orderable' => false] // Actions column
                    ],
                    'lengthMenu' => [ 10, 30, 50, 100],
                    'rowReorder' => false, // We'll use custom sortable
                ];

                $data = array();
            @endphp

            @foreach ($products as $value)
                @php
                    $select = '<input type="checkbox" class="row-checkbox" value="'.$value->id.'">';

                    // Drag handle
                    $dragHandle = '<i class="fas fa-grip-vertical text-muted drag-handle" style="cursor: move;" title="Drag to reorder"></i>';

                    // Sort Order with editable input
                    $sortOrder = '<div class="d-flex align-items-center">
                        <input type="number" class="form-control form-control-sm sort-order-input"
                               value="'.$value->sort_order.'"
                               data-product-id="'.$value->id.'"
                               style="width: 60px; text-align: center;"
                               min="1"
                               title="Click to edit sort order">
                        <button class="btn btn-xs btn-outline-success ml-1 save-sort-order d-none"
                                data-product-id="'.$value->id.'"
                                title="Save">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>';

                    $id = $value->id;
                    if($value->thumbnail){
                        $thumbnail = '<img src="'.$value->thumbnail.'" alt="'.$value->name.'" class="img-fluid img-thumbnail" style="width: 50px; height: 50px;">';
                    }else{
                        $thumbnail = '<img src="'.asset('assets/default.jpg').'" alt="'.$value->name.'" class="img-fluid img-thumbnail" style="width: 50px; height: 50px;">';
                    }
                    $name = $thumbnail.' '.$value->name;
                    $stock = ($value->stocks->sum('qty') > 0) ? '<span class="badge badge-success">In Stock</span>' : '<span class="badge badge-danger">Out of Stock</span>';
                    $price = formatCurrency($value->price);
                    $featured = generateStatusSwitch($value, 'product.featured', 'is_featured');
                    $status = generateStatusSwitch($value, 'product.status', 'is_active');

                    // Edit
                    $btnEdit = '<a class="btn btn-xs btn-default text-primary mx-1 shadow" title="Edit" href="'.route('product.edit', $value->id).'" data-title="Edit - '.$value->name.'" data-id="'.$value->id.'">
                                <i class="fa fa-lg fa-fw fa-pen"></i>
                            </a>';

                    // Delete
                    $btnDelete = '<button class="btn btn-xs btn-default text-danger mx-1 shadow delete-record" title="Delete" data-id="'.$value->id.'" data-action="delete" data-url="'.route('product.destroy', $value).'" data-title="Delete - '.$value->name.'">
                                <i class="fa fa-lg fa-fw fa-trash"></i>
                            </button>';

                    // Actions
                    $actions = '<nobr>'.$btnEdit.' '.$btnDelete.'</nobr>';

                    $data[] = [$select, $dragHandle, $sortOrder, $id, $name, $stock, $price, $featured, $status, $actions];
                    $config["data"] = $data;
                @endphp
            @endforeach

            <!-- Quick Sort Toolbar -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0"><i class="fas fa-sort"></i> Quick Sort Options</h6>
                </div>
                <div class="card-body py-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm quick-sort" data-sort="name" data-direction="asc">
                            <i class="fas fa-sort-alpha-down"></i> Name A-Z
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-sm quick-sort" data-sort="name" data-direction="desc">
                            <i class="fas fa-sort-alpha-up"></i> Name Z-A
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm quick-sort" data-sort="created_at" data-direction="desc">
                            <i class="fas fa-clock"></i> Newest First
                        </button>
                        <button type="button" class="btn btn-outline-success btn-sm quick-sort" data-sort="created_at" data-direction="asc">
                            <i class="fas fa-history"></i> Oldest First
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm quick-sort" data-sort="price" data-direction="asc">
                            <i class="fas fa-dollar-sign"></i> Price Low-High
                        </button>
                        <button type="button" class="btn btn-outline-warning btn-sm quick-sort" data-sort="price" data-direction="desc">
                            <i class="fas fa-dollar-sign"></i> Price High-Low
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h5><i class="icon fas fa-info"></i> Multiple Ways to Sort!</h5>
                <ul class="mb-0">
                    <li><strong>Drag & Drop:</strong> Grab the grip icon (⋮⋮) to reorder manually</li>
                    <li><strong>Quick Sort:</strong> Use the buttons above for instant sorting</li>
                    <li><strong>Direct Edit:</strong> Click on sort order numbers to edit directly</li>
                </ul>
            </div>

            {{-- De --}}
            <button id="delete-selected" class="btn btn-danger mb-2 d-none"> <i class="fa fa-lg fa-fw fa-trash"></i> </button>

            {{-- Table --}}
            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable with-buttons compressed/>
        </div>
    </section>


@endsection

@push('js')
    <!-- SortableJS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <script>
        let table;
        let sortable;

        $(document).ready(function() {
            // Initialize DataTable
            table = $('#admin-table').DataTable();

            // Initialize Sortable after DataTable is ready
            initializeSortable();

            $('#selectAll').on('change', function () {
                $('.row-checkbox').prop('checked', this.checked);
                toggleDeleteButton();
            });

            // Toggle delete button on individual checkbox change
            $(document).on('change', '.row-checkbox', function () {
                // If all checked, check the "select-all" checkbox
                $('#selectAll').prop('checked', $('.row-checkbox:checked').length === $('.row-checkbox').length);
                toggleDeleteButton();
            });

            function toggleDeleteButton() {
                if ($('.row-checkbox:checked').length > 0) {
                    $('#delete-selected').removeClass('d-none');
                } else {
                    $('#delete-selected').addClass('d-none');
                }
            }

            $('#delete-selected').on('click', function () {
                let selectedIds = $('.row-checkbox:checked').map(function () {
                    return $(this).val();
                }).get();

                if (selectedIds.length === 0) return;

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to delete selected product(s).",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('product.destroy.all') }}",
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: selectedIds
                            },
                            onBeforeSend: function(xhr) {
                                $('##delete-selected').addClass('btn-loading').prop('disabled', true);
                            },
                            success: function (response) {
                                if (response.status) {
                                    Swal.fire('Deleted!', response.message, 'success');
                                    selectedIds.forEach(function(id) {
                                        table.row($(`.row-checkbox[value="${id}"]`).closest('tr')).remove().draw();
                                    });
                                    $('#select-all').prop('checked', false);
                                    toggleDeleteButton();
                                } else {
                                    Swal.fire('Warning', response.message, 'warning');
                                }
                            },
                            error: function (xhr) {
                                let message = 'Something went wrong!';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    message = xhr.responseJSON.message;
                                }
                                Swal.fire('Error', message, 'error');
                            },
                            complete: function () {
                                $('#delete-selected').removeClass('btn-loading').prop('disabled', false);
                            }
                        });
                    }
                });
            });

            // Quick Sort functionality
            $('.quick-sort').on('click', function() {
                const sortBy = $(this).data('sort');
                const direction = $(this).data('direction');
                const button = $(this);
                const originalText = button.html();

                // Visual feedback
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Sorting...');

                $.ajax({
                    url: '{{ route("product.reset-sort-order") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        sort_by: sortBy,
                        direction: direction
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: response.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Reload page to show new order
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            // Show error toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: response.message || 'Failed to sort products',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true
                            });
                            button.prop('disabled', false).html(originalText);
                        }
                    },
                    error: function(xhr) {
                        // Show error toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error sorting products',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true
                        });
                        button.prop('disabled', false).html(originalText);
                    }
                });
            });

            // Sort Order Input functionality
            $(document).on('focus', '.sort-order-input', function() {
                $(this).data('original-value', $(this).val());
                $(this).siblings('.save-sort-order').removeClass('d-none');
            });

            $(document).on('blur', '.sort-order-input', function() {
                const input = $(this);
                const originalValue = input.data('original-value');
                const currentValue = input.val();

                if (originalValue == currentValue) {
                    input.siblings('.save-sort-order').addClass('d-none');
                }
            });

            $(document).on('keypress', '.sort-order-input', function(e) {
                if (e.which === 13) { // Enter key
                    $(this).siblings('.save-sort-order').click();
                }
            });

            $(document).on('click', '.save-sort-order', function() {
                const button = $(this);
                const input = button.siblings('.sort-order-input');
                const productId = button.data('product-id');
                const newOrder = input.val();
                const originalValue = input.data('original-value');

                if (!newOrder || newOrder < 1) {
                    // Show error toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Sort order must be a positive number',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    input.val(originalValue);
                    button.addClass('d-none');
                    return;
                }

                // Visual feedback
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: '{{ route("product.update-order") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        order_data: [{
                            id: productId,
                            order: newOrder
                        }]
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Sort order updated successfully!',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            button.addClass('d-none').prop('disabled', false).html('<i class="fas fa-check"></i>');
                            input.data('original-value', newOrder);

                            // Reload page after a short delay to show updated order
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            // Show error toast
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Failed to update sort order',
                                showConfirmButton: false,
                                timer: 4000,
                                timerProgressBar: true
                            });
                            input.val(originalValue);
                            button.addClass('d-none').prop('disabled', false).html('<i class="fas fa-check"></i>');
                        }
                    },
                    error: function(xhr) {
                        // Show error toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Error updating sort order',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true
                        });
                        input.val(originalValue);
                        button.addClass('d-none').prop('disabled', false).html('<i class="fas fa-check"></i>');
                    }
                });
            });
        });

        function initializeSortable() {
            const tableBody = document.querySelector('#admin-table tbody');

            if (tableBody) {
                sortable = new Sortable(tableBody, {
                    handle: '.drag-handle',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        updateProductOrder();
                    }
                });
            }
        }

        function updateProductOrder() {
            const rows = $('#admin-table tbody tr');
            const orderData = [];

            rows.each(function(index) {
                const editBtn = $(this).find('a[data-id]');
                if (editBtn.length > 0) {
                    const productId = editBtn.data('id');
                    orderData.push({
                        id: productId,
                        order: index + 1
                    });
                }
            });

            // Send AJAX request to update order
            $.ajax({
                url: '{{ route("product.update-order") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    order_data: orderData
                },
                success: function(response) {
                    if (response.success) {
                        // Show success toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Product order updated successfully!',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    } else {
                        // Show error toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Failed to update product order',
                            showConfirmButton: false,
                            timer: 4000,
                            timerProgressBar: true
                        });
                        location.reload(); // Reload to restore original order
                    }
                },
                error: function(xhr) {
                    // Show error toast
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Error updating product order',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                    location.reload(); // Reload to restore original order
                }
            });
        }
    </script>

    <style>
        .sortable-ghost {
            opacity: 0.4;
            background: #f8f9fa;
        }

        .sortable-chosen {
            background: #e3f2fd;
        }

        .sortable-drag {
            background: #fff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .drag-handle:hover {
            color: #007bff !important;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .sort-order-input {
            border: 1px solid #dee2e6;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .sort-order-input:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .save-sort-order {
            transition: all 0.2s ease;
        }

        .save-sort-order:hover {
            background-color: #28a745;
            color: white;
        }

        .quick-sort {
            transition: all 0.2s ease;
        }

        .quick-sort:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>

    @include('common.scripts')
@endpush
