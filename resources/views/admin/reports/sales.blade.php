@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'Sales Report - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Flatpickr', true)
@section('plugins.Chartjs', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Sales Report</h1>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('sales.report') }}" method="get" class="mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Date Range</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="far fa-calendar-alt"></i>
                                    </span>
                                </div>
                                <input type="text" class="form-control float-right" id="date_range" name="date_range"
                                    value="{{ old('date_range', request('date_range')) }}">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Filter</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Total Revenue</h6>
                                    <h3 class="mb-0">{{ formatCurrency($summary->total_amount) }}</h3>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i> +12.5% from last period
                                    </small>
                                </div>
                                <div class="text-primary">
                                    <i class="fas fa-dollar-sign fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Units Sold</h6>
                                    <h3 class="mb-0">{{ number_format($summary->total_products) }}</h3>
                                    <small class="text-success">
                                        <i class="fas fa-arrow-up"></i> +8.2% from last period
                                    </small>
                                </div>
                                <div class="text-info">
                                    <i class="fas fa-shopping-cart fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">New Customers</h6>
                                    <h3 class="mb-0">{{ number_format($summary->new_customers) }}</h3>
                                    <small class="text-muted">
                                        <i class="fas fa-user-plus"></i> In selected period
                                    </small>
                                </div>
                                <div class="text-warning">
                                    <i class="fas fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-muted mb-1">Average Order Value</h6>
                                    <h3 class="mb-0">{{ formatCurrency($summary->average_order_value) }}</h3>
                                    <small class="text-muted">
                                        <i class="fas fa-calculator"></i> Per order
                                    </small>
                                </div>
                                <div class="text-success">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-line mr-1"></i>
                                Monthly Sales Trend
                            </h3>
                            <p class="card-text text-muted">Revenue and units sold over the last 12 months</p>
                        </div>
                        <div class="card-body">
                            <canvas id="monthlySalesChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>

                    <!-- Top Performing Products -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-trophy mr-1"></i>
                                Top Performing Products
                            </h3>
                            <p class="card-text text-muted">Best selling products by revenue</p>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if ($topProducts->count() > 0)
                                        @foreach ($topProducts as $index => $product)
                                            @php
                                                $badgeClass =
                                                    $index == 0
                                                        ? 'badge-primary'
                                                        : ($index == 1
                                                            ? 'badge-success'
                                                            : 'badge-warning');
                                            @endphp
                                            <div class="d-flex align-items-center mb-3 p-3 border rounded">
                                                <div class="mr-3">
                                                    <span class="badge {{ $badgeClass }} badge-lg"
                                                        style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; font-size: 14px;">{{ $index + 1 }}</span>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 font-weight-bold">{{ $product['name'] }}</h6>
                                                    <small class="text-muted">{{ $product['category'] }}</small>
                                                </div>
                                                <div class="text-right">
                                                    <div class="font-weight-bold">{{ formatCurrency($product['revenue']) }}
                                                    </div>
                                                    <small class="text-muted">{{ number_format($product['units_sold']) }}
                                                        units</small>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center text-muted py-4">
                                            <i class="fas fa-chart-bar fa-3x mb-3"></i>
                                            <p>No product data available for the selected period</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Sales by Category
                            </h3>
                            <p class="card-text text-muted">Revenue breakdown by product category</p>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart" style="min-height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $heads = ['Order ID', 'Products', 'Quantity', 'Cost', 'Amount', 'Profit', 'Date'];

                $config = [
                    'columns' => [null, null, null, null, null, null, null],
                    'lengthMenu' => [20, 30, 50, 100],
                ];

                $data = [];

                foreach ($orders->where('payment_status', 'paid') as $order) {
                    $products = $order->items
                        ->pluck('product.name')
                        ->map(function ($name, $key) use ($order) {
                            $variant = $order->items[$key]->variant;
                            return $name . ($variant && $variant !== 'Standard' ? ' - ' . $variant : '');
                        })
                        ->implode(', ');

                    // Calculate actual cost and profit
                    $totalCost = $order->items->sum(function ($item) {
                        return $item->cost * $item->quantity;
                    });
                    $totalQuantity = $order->items->sum('quantity');
                    $totalAmount = $order->grand_total;
                    $profit = $totalAmount - $totalCost;

                    // Color coding for profit
                    $profitColor = $profit >= 0 ? 'text-success' : 'text-danger';
                    $profitIcon = $profit >= 0 ? 'fa-arrow-up' : 'fa-arrow-down';

                    $data[] = [
                        '#' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
                        $products ?: 'No products',
                        number_format($totalQuantity) . ' units',
                        formatCurrency($totalCost),
                        formatCurrency($totalAmount),
                        '<span class="' .
                        $profitColor .
                        '"><i class="fas ' .
                        $profitIcon .
                        '"></i> ' .
                        formatCurrency($profit) .
                        '</span>',
                        $order->created_at->format('F d, Y'),
                    ];
                }

                $config['data'] = $data;
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" with-buttons
                hoverable compressed />
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(function() {
            const selectedRange = @json(request('date_range'));

            $('#date_range').flatpickr({
                today: true,
                mode: 'range',
                dateFormat: 'Y-m-d',
                defaultDate: selectedRange ? selectedRange.split(' to ') : [new Date(), new Date()],
                allowInput: true,
            });
        });
    </script>

    <script>
        // Monthly Sales Trend Chart
        var monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');

        // Use actual monthly data from controller
        var monthlyData = @json($monthlyData);
        var monthlyLabels = monthlyData.map(item => item.month);
        var revenueData = monthlyData.map(item => item.revenue);
        var unitsData = monthlyData.map(item => item.units);

        var monthlySalesChart = new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [{
                    label: 'Revenue ($000)',
                    data: revenueData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4,
                    yAxisID: 'y'
                }, {
                    label: 'Units Sold',
                    data: unitsData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.1)',
                    borderWidth: 3,
                    pointRadius: 4,
                    pointBackgroundColor: 'rgba(75, 192, 192, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointHoverRadius: 6,
                    fill: false,
                    tension: 0.4,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value + 'k';
                            }
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        beginAtZero: true,
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.datasetIndex === 0) {
                                    return 'Revenue: $' + context.raw + 'k';
                                } else {
                                    return 'Units: ' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            }
        });

        // Sales by Category Chart (Doughnut)
        var categoryCtx = document.getElementById('categoryChart').getContext('2d');

        // Use actual category data from controller
        var categoryData = @json($categoryData);
        var categoryLabels = Object.values(categoryData).map(item => item.category);
        var categoryPercentages = Object.values(categoryData).map(item => item.percentage);

        var categoryChart = new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels.length > 0 ? categoryLabels : ['No Data'],
                datasets: [{
                    data: categoryPercentages.length > 0 ? categoryPercentages : [100],
                    backgroundColor: [
                        '#4ECDC4',
                        '#FF6B6B',
                        '#FFE66D',
                        '#95E1D3',
                        '#A8E6CF',
                        '#C7CEEA',
                        '#DDA0DD',
                        '#98FB98',
                        '#F0E68C',
                        '#87CEEB'
                    ],
                    borderWidth: 0,
                    hoverBorderWidth: 3,
                    hoverBorderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (categoryLabels.length === 0) {
                                    return 'No data available';
                                }
                                return context.label + ': ' + context.parsed + '%';
                            }
                        }
                    }
                }
            }
        });
    </script>
@endpush
