@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Create Product - '.env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)
@section('plugins.Flatpickr', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Create Flash Deal') }}</h1>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('flash.deal.index') }}" class="text-muted">{{ __('Flash Deals') }}</a></li>
                <li class="breadcrumb-item active">{{ __('Create Flash Deal') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('flash.deal.store') }}" method="POST" class="ajax-form position-relative">
        @csrf
        <div class="row pb-5">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Flash Deal Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">{{ __('Title') }} <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" id="title" placeholder="{{ __('Title') }}">
                            <small class="field-notice text-danger" rel="title"></small>
                        </div>
                        <div class="form-group">
                            <label for="date_range">{{ __('Date Range') }} <span class="text-danger">*</span></label>
                            <input type="text" name="date_range" class="form-control" id="date_range" placeholder="{{ __('Select date range') }}" autocomplete="off">
                            <small class="field-notice text-danger" rel="date_range"></small>
                        </div>
                        <div class="">
                            <x-file-uploader
                                pondName="flash_deal_banner"
                                pondID="flash_deal_banner"
                                pondCollection="flash_deal_banner"
                                pondInstanceName="flash_deal_banner"
                                pondLable="Upload Flash Deal Banner"
                                pondMedia=""
                                inputLabel="Flash Deal Banner (Size: 1920x800px)"
                            />
                            <small class="field-notice text-danger" rel="flash_deal_banner"></small>
                        </div>
                        <div class="form-group">
                            <label for="products">{{ __('Products') }} <span class="text-danger">*</span></label>
                            <x-adminlte-select2 name="products[]" id="products" multiple>
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </x-adminlte-select2>
                            <small class="field-notice text-danger" rel="products"></small>
                        </div>

                        <div id="selected-products-section">
                            <!-- Selected products with qty and discount will appear here -->
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Options') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="status">{{ __('Status') }}</label>
                            <x-adminlte-select2 name="status" id="status">
                                <option value="1">{{ __('Active') }}</option>
                                <option value="0">{{ __('Inactive') }}</option>
                            </x-adminlte-select2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="btns-container">
            <button type="submit" class="btn btn-dark">{{ __('Save') }}</button>
            <a href="{{ route('product.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(function() {
            $('#date_range').flatpickr({
                mode: 'range',
                dateFormat: 'Y-m-d',
                allowInput: true,
                locale: {
                    firstDayOfWeek: 1
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            function renderSelectedProducts() {
                let selected = $('#products').val() || [];
                let $container = $('#selected-products-section');
                $container.empty();

                if(selected.length === 0) return;

                @php
                    $productsArr = [];
                    foreach($products as $product) {
                        $productsArr[$product->id] = $product->name;
                    }
                @endphp
                let productsData = @json($productsArr);

                selected.forEach(function(productId) {
                    let html = `
                        <div class="card mb-2 product-discount-row" data-product-id="${productId}">
                            <div class="card-body py-2">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <strong>${productsData[productId]}</strong>
                                        <input type="hidden" name="selected_products[]" value="${productId}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" name="qty[${productId}]" class="form-control" min="1" step="1" placeholder="{{ __('Qty') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" name="discount[${productId}]" class="form-control" step="0.01" min="0" placeholder="{{ __('Discount') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="discount_type[${productId}]" class="form-control">
                                            <option value="percent">{{ __('Percent') }}</option>
                                            <option value="fixed">{{ __('Fixed Amount') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $container.append(html);
                });
            }

            $('#products').on('change', renderSelectedProducts);

            // Initial render if old input exists
            renderSelectedProducts();
        });
    </script>

    @include('common.scripts')

@endpush