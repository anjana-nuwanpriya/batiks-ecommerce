@extends('adminlte::page')

@section('title', 'Edit Overseas Order')

@section('content_header')
    <h1>Edit Order {{ $overseasOrder->order_number }}</h1>
@stop

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.overseas-orders.update', $overseasOrder) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                <option value="pending" {{ $overseasOrder->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $overseasOrder->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="shipped" {{ $overseasOrder->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $overseasOrder->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $overseasOrder->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($overseasOrder->status !== 'shipped')
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle"></i> Stock will be reduced when status is changed to "Shipped"
                                </small>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="shipping_cost">Shipping Cost</label>
                            <input type="number" name="shipping_cost" id="shipping_cost"
                                   class="form-control @error('shipping_cost') is-invalid @enderror"
                                   step="0.01" min="0" value="{{ old('shipping_cost', $overseasOrder->shipping_cost) }}" required>
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
                                   class="form-control mb-2" value="{{ old('shipping_address.name', $overseasOrder->shipping_address['name']) }}" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="shipping_address[phone]" placeholder="Phone"
                                   class="form-control mb-2" value="{{ old('shipping_address.phone', $overseasOrder->shipping_address['phone']) }}" required>
                        </div>
                        <div class="col-md-12">
                            <textarea name="shipping_address[address]" placeholder="Address"
                                      class="form-control mb-2" rows="2" required>{{ old('shipping_address.address', $overseasOrder->shipping_address['address']) }}</textarea>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="shipping_address[city]" placeholder="City"
                                   class="form-control mb-2" value="{{ old('shipping_address.city', $overseasOrder->shipping_address['city']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="shipping_address[state]" placeholder="State/Province"
                                   class="form-control mb-2" value="{{ old('shipping_address.state', $overseasOrder->shipping_address['state']) }}" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="shipping_address[country]" placeholder="Country"
                                   class="form-control mb-2" value="{{ old('shipping_address.country', $overseasOrder->shipping_address['country']) }}" required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Order Items (Read Only)</label>
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
                        </table>
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes">Notes</label>
                    <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $overseasOrder->notes) }}</textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Order</button>
                    <a href="{{ route('admin.overseas-orders.show', $overseasOrder) }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@stop
