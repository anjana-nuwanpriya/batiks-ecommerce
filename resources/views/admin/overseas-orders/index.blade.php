@extends('adminlte::page')

@section('title', 'Overseas Orders - '.env('APP_NAME'))

@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('Overseas Orders') }}</h5>
        </div>
        <div class="d-flex align-items-center">
            <a href="{{ route('admin.overseas-orders.create') }}" class="btn btn-primary btn-sm mr-2">
                <i class="fas fa-plus mr-1"></i>Create New Order
            </a>
            <ol class="breadcrumb float-sm-right mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item active">{{ __('Overseas Orders') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
<section class="card">
    <div class="card-body">
        @php
            $heads = [
                'Order Number',
                'Customer Name',
                'Customer Email',
                'Total Amount',
                'Shipping Cost',
                'Status',
                'Created',
                'Actions',
            ];

            $config = [
                'order' => [[0, 'desc']],
                'columns' => [null, null, null, null, null, null, null, null],
                'lengthMenu' => [20, 30, 50, 100],
            ];

            $data = array();

            foreach ($orders as $order) {
                $orderNumber = $order->order_number;
                $customer = $order->user->name;
                $email = $order->user->email;
                $totalAmount =  formatCurrency($order->total_amount);
                $shippingCost = formatCurrency($order->shipping_cost);

                switch ($order->status) {
                    case 'pending':
                        $status = '<span class="badge badge-warning">Pending</span>';
                        break;
                    case 'processing':
                        $status = '<span class="badge badge-info">Processing</span>';
                        break;
                    case 'shipped':
                        $status = '<span class="badge badge-success">Shipped</span>';
                        break;
                    case 'delivered':
                        $status = '<span class="badge badge-success">Delivered</span>';
                        break;
                    case 'cancelled':
                        $status = '<span class="badge badge-danger">Cancelled</span>';
                        break;
                    default:
                        $status = '<span class="badge badge-secondary">Unknown</span>';
                        break;
                }

                $created = $order->created_at->diffForHumans();

                $btnView = '<a href="'.route('admin.overseas-orders.show', $order->id).'" class="btn btn-xs btn-default text-primary mx-1 shadow" title="View">
                                <i class="fa fa-lg fa-fw fa-eye"></i>
                            </a>';

                $btnEdit = '<a href="'.route('admin.overseas-orders.edit', $order->id).'" class="btn btn-xs btn-default text-warning mx-1 shadow" title="Edit">
                                <i class="fa fa-lg fa-fw fa-edit"></i>
                            </a>';

                $btnDelete = '<form action="'.route('admin.overseas-orders.destroy', $order->id).'" method="POST" class="d-inline" onsubmit="return confirm(\'Are you sure?\')">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="submit" class="btn btn-xs btn-default text-danger mx-1 shadow" title="Delete">
                                    <i class="fa fa-lg fa-fw fa-trash"></i>
                                </button>
                            </form>';

                $actions = '<nobr>'.$btnView.$btnEdit.$btnDelete.'</nobr>';

                $data[] = [
                    $orderNumber,
                    $customer,
                    $email,
                    $totalAmount,
                    $shippingCost,
                    $status,
                    $created,
                    $actions
                ];
            }
            $config["data"] = $data;
        @endphp

        <x-adminlte-datatable id="overseas-orders-table" :heads="$heads" head-theme="light" :config="$config" hoverable with-buttons compressed/>

    </div>
</section>
@endsection
