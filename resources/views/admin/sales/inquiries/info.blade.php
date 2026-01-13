<div class="inquiry-info">
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Contact Information</h5>
            <div class="card-tools">
                @php
                    $statusClass = match ($inquiry->status) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge badge-{{ $statusClass }}">{{ ucfirst($inquiry->status) }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Company Name</strong></label>
                        <p class="mb-2">{{ $inquiry->company }}</p>
                    </div>
                    <div class="form-group">
                        <label><strong>Contact Person</strong></label>
                        <p class="mb-2">{{ $inquiry->name }}</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><strong>Email</strong></label>
                        <p class="mb-2">
                            <a href="mailto:{{ $inquiry->email }}" class="text-dark">{{ $inquiry->email }}</a>
                        </p>
                    </div>
                    <div class="form-group">
                        <label><strong>Phone</strong></label>
                        <p class="mb-2">
                            <a href="tel:{{ $inquiry->phone }}" class="text-dark">{{ $inquiry->phone }}</a>
                        </p>
                    </div>
                </div>
            </div>
            @if ($inquiry->message)
                <div class="form-group">
                    <label><strong>Message</strong></label>
                    <div class="alert alert-light">
                        {{ $inquiry->message }}
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">
                        <i class="fas fa-calendar"></i> Created: {{ $inquiry->created_at->format('M d, Y h:i A') }}
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Product Details</h5>
            <div class="card-tools">
                <span class="badge badge-primary">{{ $inquiry->products->count() }} Product(s)</span>
            </div>
        </div>
        <div class="card-body">
            @if ($inquiry->products->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th width="35%">Product Name</th>
                                <th width="25%">Variant Details</th>
                                <th width="15%" class="text-center">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($inquiry->products as $productInquiry)
                                <tr>
                                    <td>
                                        @if ($productInquiry->product)
                                            <strong>{{ $productInquiry->product->name }}</strong>
                                            @if ($productInquiry->product->sku)
                                                <br><small class="text-muted">Product SKU:
                                                    {{ $productInquiry->product->sku }}</small>
                                            @endif
                                        @else
                                            <span class="text-danger">
                                                <i class="fas fa-exclamation-triangle"></i> Product not found (ID:
                                                {{ $productInquiry->product_id }})
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($productInquiry->variant_id && $productInquiry->variant)
                                            <div class="variant-details">
                                                <span
                                                    class="badge badge-secondary mb-1">{{ $productInquiry->variant->variant }}</span>
                                                @if ($productInquiry->variant->sku)
                                                    <br><small class="text-muted">Variant SKU:
                                                        {{ $productInquiry->variant->sku }}</small>
                                                @endif
                                                @if ($productInquiry->variant->weight)
                                                    <br><small class="text-muted">Weight:
                                                        {{ $productInquiry->variant->weight }}</small>
                                                @endif
                                                @if ($productInquiry->variant->qty !== null)
                                                    <br><small class="text-muted">Current Stock:
                                                        <span
                                                            class="badge badge-{{ $productInquiry->variant->qty > 0 ? 'success' : 'danger' }} badge-sm">
                                                            {{ $productInquiry->variant->qty }} units
                                                        </span>
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="badge badge-light">Standard Variant</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span
                                            class="badge badge-info">{{ number_format($productInquiry->quantity) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No products specified in this inquiry.
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Status Management</h5>
            <div class="card-tools">
                @php
                    $currentStatusClass = match ($inquiry->status) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    };
                @endphp
                <span class="badge badge-{{ $currentStatusClass }}">Current: {{ ucfirst($inquiry->status) }}</span>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('product-inquiries.update', $inquiry) }}" method="POST" class="ajax-form">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="status"><strong>Update Status</strong></label>
                            <select name="status" id="status" class="form-control select2" style="width: 100%;">
                                <option value="pending" {{ $inquiry->status == 'pending' ? 'selected' : '' }}>
                                    🟡 Pending - Awaiting Review
                                </option>
                                <option value="processing" {{ $inquiry->status == 'processing' ? 'selected' : '' }}>
                                    🔄 Processing - Currently Being Handled
                                </option>
                                <option value="completed" {{ $inquiry->status == 'completed' ? 'selected' : '' }}>
                                    🟢 Completed - Inquiry Resolved
                                </option>
                                <option value="rejected" {{ $inquiry->status == 'rejected' ? 'selected' : '' }}>
                                    🔴 Rejected - Inquiry Declined
                                </option>
                            </select>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> Customer will receive an email notification when
                                status changes.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Status & Send Notification
                            </button>
                            <button type="button" class="btn btn-outline-secondary ml-2"
                                onclick="$('#viewModal').modal('hide');">
                                <i class="fas fa-times"></i> Cancel
                            </button>
                        </div>
                        <div>
                            <small class="text-muted">
                                Last updated: {{ $inquiry->updated_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Status History (if you want to add this feature later) -->
            <div class="mt-4 pt-3 border-top">
                <h6 class="text-muted mb-2">
                    <i class="fas fa-history"></i> Quick Actions
                </h6>
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-info" onclick="sendFollowUpEmail()">
                        <i class="fas fa-envelope"></i> Send Follow-up Email
                    </button>
                    {{-- <button type="button" class="btn btn-outline-success" onclick="createOrder()">
                        <i class="fas fa-shopping-cart"></i> Convert to Order
                    </button>
                    <button type="button" class="btn btn-outline-warning" onclick="scheduleCallback()">
                        <i class="fas fa-phone"></i> Schedule Callback
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Quick action functions
    function sendFollowUpEmail() {
        Swal.fire({
            title: 'Send Follow-up Email',
            text: 'This will send a follow-up email to the customer.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, send email!'
        }).then((result) => {
            if (result.isConfirmed) {
                // You can implement this functionality later
                Swal.fire('Feature Coming Soon!', 'Follow-up email functionality will be available soon.',
                    'info');
            }
        });
    }

    function createOrder() {
        Swal.fire({
            title: 'Convert to Order',
            text: 'This will create a new order based on this inquiry.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, create order!'
        }).then((result) => {
            if (result.isConfirmed) {
                // You can implement this functionality later
                Swal.fire('Feature Coming Soon!', 'Order conversion functionality will be available soon.',
                    'info');
            }
        });
    }

    function scheduleCallback() {
        Swal.fire({
            title: 'Schedule Callback',
            html: `
            <div class="form-group text-left">
                <label for="callback-date">Callback Date & Time:</label>
                <input type="datetime-local" id="callback-date" class="form-control" min="${new Date().toISOString().slice(0, 16)}">
            </div>
            <div class="form-group text-left">
                <label for="callback-notes">Notes:</label>
                <textarea id="callback-notes" class="form-control" rows="3" placeholder="Add notes for the callback..."></textarea>
            </div>
        `,
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Schedule Callback',
            preConfirm: () => {
                const date = document.getElementById('callback-date').value;
                const notes = document.getElementById('callback-notes').value;

                if (!date) {
                    Swal.showValidationMessage('Please select a callback date and time');
                    return false;
                }

                return {
                    date: date,
                    notes: notes
                };
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // You can implement this functionality later
                Swal.fire('Feature Coming Soon!', 'Callback scheduling functionality will be available soon.',
                    'info');
            }
        });
    }

    // Handle form submission with better feedback
    $(document).ready(function() {
        $('.ajax-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const originalText = submitBtn.html();

            // Show loading state
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                            timer: 3000,
                            showConfirmButton: false
                        });

                        // Optionally refresh the modal or close it
                        setTimeout(() => {
                            $('#viewModal').modal('hide');
                            // You might want to refresh the main table here
                            if (typeof table !== 'undefined' && table.ajax) {
                                table.ajax.reload();
                            }
                        }, 1500);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Something went wrong!'
                        });
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong!';

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage = errors.join('<br>');
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage
                    });
                },
                complete: function() {
                    // Restore button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });
    });
</script>
