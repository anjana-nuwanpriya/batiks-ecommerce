@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Products Stock Report - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Products Stock Report</h1>
    </div>
@endsection

@section('content')
    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <h6 class="text-muted mb-2 fw-normal small">Total Products</h6>
                            <h3 class="mb-0 fw-bold text-dark">{{ number_format($totalProducts) }}</h3>
                        </div>
                        <div class="text-primary d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: rgba(0,123,255,0.1); border-radius: 12px;">
                            <i class="fas fa-box" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <h6 class="text-muted mb-2 fw-normal small">Total Stock Value</h6>
                            <h3 class="mb-0 fw-bold text-dark">Rs. {{ number_format($totalStockValue, 2) }}</h3>
                        </div>
                        <div class="text-success d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: rgba(40,167,69,0.1); border-radius: 12px;">
                            <i class="fas fa-dollar-sign" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <h6 class="text-muted mb-2 fw-normal small">Low Stock Items</h6>
                            <h3 class="mb-1 fw-bold text-dark">{{ number_format($lowStockItems) }}</h3>
                            <div class="d-flex align-items-center mt-1">
                                <i class="fas fa-exclamation-triangle text-warning me-1" style="font-size: 10px;"></i>
                                <small class="text-warning fw-medium">≤ 10 items</small>
                            </div>
                        </div>
                        <div class="text-warning d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: rgba(255,193,7,0.1); border-radius: 12px;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 col-sm-6 col-12 mb-3 mb-lg-0">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <h6 class="text-muted mb-2 fw-normal small">Out of Stock</h6>
                            <h3 class="mb-1 fw-bold text-dark">{{ number_format($outOfStockItems) }}</h3>
                            <div class="d-flex align-items-center mt-1">
                                <i class="fas fa-times-circle text-danger me-1" style="font-size: 10px;"></i>
                                <small class="text-danger fw-medium">0 items</small>
                            </div>
                        </div>
                        <div class="text-danger d-flex align-items-center justify-content-center"
                            style="width: 48px; height: 48px; background-color: rgba(220,53,69,0.1); border-radius: 12px;">
                            <i class="fas fa-times-circle" style="font-size: 20px;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @php
                $heads = ['Product Name', 'SKU', 'Variant', 'Current Stock', 'Unit Price', 'Stock Value', 'Status'];

                $config = [
                    'columns' => [null, null, null, null, null, null, null],
                    'lengthMenu' => [20, 30, 50, 100],
                ];

                $data = [];

                foreach ($stockData as $stock) {
                    $statusBadge =
                        '<span class="badge badge-' . $stock['status_class'] . '">' . $stock['status'] . '</span>';
                    $data[] = [
                        $stock['product_name'],
                        $stock['sku'],
                        $stock['variant'],
                        number_format($stock['current_stock']),
                        'Rs ' . number_format($stock['unit_price'], 2),
                        'Rs ' . number_format($stock['stock_value'], 2),
                        $statusBadge,
                    ];
                }

                $config['data'] = $data;
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" with-buttons
                hoverable compressed />
        </div>
    </div>
@endsection
