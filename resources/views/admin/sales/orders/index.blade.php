@extends('adminlte::page')
@section('title', 'Orders - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('All Orders') }}</h5>
        </div>
        <div class="d-flex align-items-center">
            <ol class="breadcrumb float-sm-right mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Orders') }}</li>
            </ol>
        </div>
    </div>
@endsection


@section('content')
    <section class="card">
        <div class="card-body">
            @php
                $heads = [
                    'Order ID',
                    'Customer Name',
                    'Customer Phone',
                    'Customer Email',
                    'Payment Method',
                    'Waybill No',
                    'Status',
                    'Total',
                    'Created',
                    'Actions',
                ];

                $config = [
                    'order' => [],
                    'columns' => [null, null, null, null, null, null, null, null, null, null],
                    'lengthMenu' => [20, 30, 50, 100],
                ];

                $data = [];

                foreach ($orders as $order) {
                    $id = '#' . Str::padLeft($order->id, 4, '0');

                    // Handle shipping address - it might be JSON string or already decoded array
                    $shippingAddress = is_string($order->shipping_address)
                        ? json_decode($order->shipping_address, true)
                        : $order->shipping_address;

                    $customer = !empty($order->user)
                        ? $order->user->name
                        : ($shippingAddress['name'] ?? 'Unknown') . ' (Guest)';
                    $phone = !empty($order->user) ? $order->user->phone : $shippingAddress['phone'] ?? 'N/A';
                    $email = !empty($order->user) ? $order->user->email : $shippingAddress['email'] ?? 'N/A';

                    $customer,
                    $paymentMethod = ucfirst($order->payment_method ?? 'N/A');

                    // Waybill number
                    $waybillNo = $order->waybill_no ?? '<span class="text-muted">Not Generated</span>';

                    switch ($order->payment_status) {
                        case 'pending':
                            $status = '<span class="badge badge-warning">Pending</span>';
                            break;
                        case 'paid':
                            $status = '<span class="badge badge-success">Paid</span>';
                            break;
                        case 'failed':
                            $status = '<span class="badge badge-danger">Failed</span>';
                            break;
                        case 'cancelled':
                            $status = '<span class="badge badge-danger">Cancelled</span>';
                            break;
                        case 'refunded':
                            $status = '<span class="badge badge-danger">Refunded</span>';
                            break;
                        default:
                            $status = '<span class="badge badge-secondary">Pending</span>';
                            break;
                    }

                    $total = formatCurrency($order->grand_total);
                    $created = $order->created_at->diffForHumans();

                    $btnView =
                        '<a href="' .
                        route('admin.order.show', $order->id) .
                        '" class="btn btn-xs btn-default text-primary mx-1 shadow" title="View">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';

                    $actions = '<nobr>' . $btnView . '</nobr>';

                    $data[] = [
                        $id,
                        $customer,
                        $phone,
                        $email,
                        $paymentMethod,
                        $waybillNo,
                        $status,
                        $total,
                        $created,
                        $actions,
                    ];
                    $config['data'] = $data;
                }
            @endphp

            <x-adminlte-datatable id="admin-table" :heads="$heads" head-theme="light" :config="$config" hoverable
                with-buttons compressed />

        </div>
    </section>
@endsection
