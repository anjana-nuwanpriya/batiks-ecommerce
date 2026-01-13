@extends('adminlte::page')
@section('title', 'Dashboard - ' . env('APP_NAME'))
@section('plugins.Chartjs', true)

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')

    <div class="row">

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ formatCurrency($dashboard->total_sales, 'LKR', true) }}</h3>
                    <p>{{ __('Total Sales') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ number_format($dashboard->total_orders, 0, '.', ',') }}</h3>
                    <p>{{ __('Total Orders') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ number_format($dashboard->total_products, 0, '.', ',') }}</h3>
                    <p>{{ __('Total Products') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-box"></i>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ number_format($dashboard->total_customers, 0, '.', ',') }}</h3>
                    <p>{{ __('Total Customers') }}</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Row -->
    <div class="row">
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Sales Analystics
                    </h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" style="min-height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Latest Orders
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Payment Status</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latest_orders as $order)
                                <tr class="clickable-row" data-href="{{ route('admin.order.show', $order->id) }}">
                                    <td><a
                                            href="{{ route('admin.order.show', $order->id) }}">#{{ Str::padLeft($order->id, 4, '0') }}</a>
                                    </td>
                                    @if ($order->user)
                                        <td>{{ $order->user->name }}</td>
                                    @else
                                        @php
                                            $shippingAddress = is_string($order->shipping_address)
                                                ? json_decode($order->shipping_address)
                                                : (object) $order->shipping_address;
                                        @endphp
                                        <td>{{ $shippingAddress->name ?? 'Guest' }} (Guest)</td>
                                    @endif
                                    <td>{{ $order->created_at->format('d-m-Y h:i A') }}</td>
                                    <td>
                                        @if ($order->payment_status == 'paid')
                                            <span class="badge badge-success">Paid</span>
                                        @else
                                            <span class="badge badge-danger">Pending</span>
                                        @endif
                                    </td>
                                    <td>{{ formatCurrency($order->grand_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <!-- User Table -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-users"></i>
                        Latest Customers
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Joined</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($latest_users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->created_at }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
@stop

@section('css')
    <style>
        .small-box {
            border-radius: 0.25rem;
        }
            background-color: #f8f9fa;
        }

        #notification-dropdown .dropdown-header {
            background-color: #e9ecef;
            font-weight: bold;
            padding: 8px 15px;
        }

        .notification-item {
            transition: background-color 0.2s ease;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
        }

        /* Dashboard notification cards */
        .alert .icon {
            margin-right: 10px;
        }

        /* Notification badge animation */
        #notification-count {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
            }
        }
    </style>
@stop

@section('js')
    <script>
        // Example chart script (using Chart.js)
        var ctx = document.getElementById('salesChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($dashboard->weekly_sales['labels']),
                datasets: [{
                    label: 'Sales',
                    data: @json($dashboard->weekly_sales['data']),
                    backgroundColor: 'rgba(54, 162, 235, 0.1)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rs.' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });
    </script>

    @include('common.scripts')
@stop
