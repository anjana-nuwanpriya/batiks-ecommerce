@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Shipping Settings - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.FilePond', true)


@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Shipping Settings') }}</h5>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Shipping Settings') }}</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')
    <section class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="border p-3">
                        <h6 class="font-weight-bold">Shipping Settings</h6>
                        <div class="alert alert-info">
                            <p class="mb-0">
                                This is the shipping settings for the website. You can set the shipping type, weight rates,
                                and flat rates here. It controls the shipping cost for the website.
                            </p>
                        </div>
                        <form action="{{ route('shipping.setting.update') }}" class="ajax-form" method="post">
                            @csrf
                            <div class="form-group mt-3">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="shipping_type" id="product_wise"
                                        value="product_wise" {{ $shipping_type == 'product_wise' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="product_wise">
                                        Product Wise Shipping
                                    </label>
                                </div>

                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="shipping_type" id="flat_rate"
                                        value="flat_rate" {{ $shipping_type == 'flat_rate' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="flat_rate">
                                        Flat Rate
                                    </label>
                                </div>

                                <div id="flat_rate_input" class="form-group mt-2"
                                    style="display: {{ $shipping_type == 'flat_rate' ? 'block' : 'none' }};">
                                    <label for="flat_rate_amount">Flat Rate Amount (LKR)</label>
                                    <input type="number" class="form-control" step="0.01" name="shipping_flat_rate"
                                        id="flat_rate_amount" placeholder="Enter flat rate amount"
                                        value="{{ get_setting('shipping_flat_rate') }}">
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shipping_type" id="free_shipping"
                                        value="free_shipping" {{ $shipping_type == 'free_shipping' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="free_shipping">
                                        Free Shipping
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark btn-sm w-100 mt-3">Save</button>
                            </div>
                        </form>
                    </div>
                    <div class="border p-3">
                        <form action="{{ route('shipping.setting.update') }}" class="ajax-form" method="post">
                            @csrf
                            <div class="form-group">
                                <label for="cod_max_amount">COD Max Amount (LKR)</label>
                                <input type="number" class="form-control" step="0.01" name="cod_max_amount"
                                    id="cod_max_amount" placeholder="Enter COD max amount"
                                    value="{{ get_setting('cod_max_amount') }}">
                            </div>
                            <div class="form-group">
                                <label for="max_kg_cod_value">Max KG COD Value</label>
                                <input type="number" class="form-control" step="0.01" name="max_kg_cod_value"
                                    id="max_kg_cod_value" placeholder="Enter max KG COD value"
                                    value="{{ get_setting('max_kg_cod_value') }}">
                            </div>
                            <div class="form-group">
                                <label for="free_delivery_from">Free Delivery From (LKR)</label>
                                <input type="number" class="form-control" step="0.01" name="free_delivery_from"
                                    id="free_delivery_from" placeholder="Enter free delivery threshold"
                                    value="{{ get_setting('free_delivery_from') }}">
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-dark btn-sm w-100 mt-3">Save</button>
                            </div>
                        </form>
                        <!-- COD & Delivery Settings Form -->

                    </div>
                </div>
                <div class="col-md-8">
                    <div class="border p-3">
                        <h6 class="font-weight-bold">Weight Rates</h6>
                        <form action="{{ route('shipping.weight.update') }}" class="ajax-form" method="post">
                            @csrf
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered" id="weightTable">
                                    <thead>
                                        <tr>
                                            <th>Weight (G)</th>
                                            <th>Price (LKR)</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($shipping_weights as $weight)
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="weight[{{ $weight->id }}]" placeholder="Weight"
                                                        value="{{ $weight->weight }}">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control"
                                                        name="price[{{ $weight->id }}]" placeholder="Cost"
                                                        value="{{ $weight->price }}">
                                                </td>
                                                <td>
                                                    <button type="button"
                                                        class="btn btn-danger btn-sm remove-row">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-outline-dark btn-sm" id="addWeight">Add
                                    Weight</button>
                            </div>
                            <div class="form-group">
                                <label for="additional_cost">Additional Cost (Per KG) (LKR)</label>
                                <input type="number" class="form-control" step="0.01" name="additional_cost"
                                    id="additional_cost" placeholder="Additional Cost"
                                    value="{{ $shipping_additional_cost }}">
                            </div>
                            <button type="submit" class="btn btn-dark btn-sm w-100 mt-3">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="row">

    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            // Show/hide flat rate input based on radio selection
            $('input[name="shipping_type"]').change(function() {
                if ($(this).val() === 'flat_rate') {
                    $('#flat_rate_input').show();
                } else {
                    $('#flat_rate_input').hide();
                }
            });

            // Add new row
            $('#addWeight').click(function() {
                var rowCount = $('#weightTable tbody tr').length + 1;
                var newRow = `
                    <tr>
                        <td>
                            <input type="text" class="form-control" name="weight[${rowCount}]" placeholder="Weight">
                        </td>
                        <td>
                            <input type="text" class="form-control" name="price[${rowCount}]" placeholder="Cost">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-row">Remove</button>
                        </td>
                    </tr>
                `;
                $('#weightTable tbody').append(newRow);
            });

            // Remove row
            $(document).on('click', '.remove-row', function() {
                $(this).closest('tr').remove();
            });
        });
    </script>

    @include('common.scripts')
@endpush
