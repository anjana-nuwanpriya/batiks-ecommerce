@extends('adminlte::page')

@section('title', 'Overseas Order Details')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Order {{ $overseasOrder->order_number }}</h1>
        <div>
            <a href="{{ route('admin.overseas-orders.edit', $overseasOrder) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('admin.overseas-orders.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Items</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Variant</th>
                                    <th>Quantity</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($overseasOrder->items as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->variant ?: '-' }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>{{ formatCurrency($item->unit_price) }}</td>
                                        <td>{{ formatCurrency($item->total_price) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4">Subtotal</th>
                                    <th>{{ formatCurrency($overseasOrder->total_amount) }}</th>
                                </tr>
                                <tr>
                                    <th colspan="4">Shipping Cost</th>
                                    <th>{{ formatCurrency($overseasOrder->shipping_cost) }}</th>
                                </tr>
                                <tr class="table-success">
                                    <th colspan="4">Total</th>
                                    <th>{{ formatCurrency($overseasOrder->total_with_shipping) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Order Information</h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-{{ $overseasOrder->status === 'shipped' ? 'success' : ($overseasOrder->status === 'cancelled' ? 'danger' : 'warning') }}">
                                {{ ucfirst($overseasOrder->status) }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Customer:</dt>
                        <dd class="col-sm-8">{{ $overseasOrder->user->name }}</dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">{{ $overseasOrder->user->email }}</dd>

                        <dt class="col-sm-4">Created:</dt>
                        <dd class="col-sm-8">{{ $overseasOrder->created_at->format('M d, Y H:i') }}</dd>

                        <dt class="col-sm-4">Updated:</dt>
                        <dd class="col-sm-8">{{ $overseasOrder->updated_at->format('M d, Y H:i') }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Shipping Address</h3>
                </div>
                <div class="card-body">
                    <address>
                        <strong>{{ $overseasOrder->shipping_address['name'] }}</strong><br>
                        {{ $overseasOrder->shipping_address['address'] }}<br>
                        {{ $overseasOrder->shipping_address['city'] }}, {{ $overseasOrder->shipping_address['state'] }}<br>
                        {{ $overseasOrder->shipping_address['country'] }}<br>
                        <abbr title="Phone">P:</abbr> {{ $overseasOrder->shipping_address['phone'] }}
                    </address>
                </div>
            </div>

            @if($overseasOrder->notes)
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Notes</h3>
                    </div>
                    <div class="card-body">
                        <p>{{ $overseasOrder->notes }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
