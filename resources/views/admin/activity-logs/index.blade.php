@extends('adminlte::page')

@section('title', 'Admin Activity Logs')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-history"></i> Admin Activity Logs</h1>
        <div>
            <button class="btn btn-info" onclick="loadStats()">
                <i class="fas fa-chart-bar"></i> Statistics
            </button>
            <button class="btn btn-warning" onclick="showCleanupModal()">
                <i class="fas fa-trash-alt"></i> Cleanup
            </button>
            <a href="{{ route('admin.activity-logs.export') }}{{ request()->getQueryString() ? '?' . request()->getQueryString() : '' }}"
               class="btn btn-success">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>
@stop

@section('content')
    <!-- Quick Stats Cards -->
    <div class="row mb-3" id="stats-cards" style="display: none;">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 id="total-activities">-</h3>
                    <p>Total Activities</p>
                </div>
                <div class="icon">
                    <i class="fas fa-history"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3 id="today-activities">-</h3>
                    <p>Today's Activities</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3 id="week-activities">-</h3>
                    <p>This Week</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-week"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3 id="month-activities">-</h3>
                    <p>This Month</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Period Filters -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Quick Filters</h3>
        </div>
        <div class="card-body">
            <div class="btn-group mb-3" role="group">
                <a href="{{ route('admin.activity-logs.index', ['period' => 'today']) }}"
                   class="btn btn-outline-primary {{ request('period') == 'today' ? 'active' : '' }}">Today</a>
                <a href="{{ route('admin.activity-logs.index', ['period' => 'yesterday']) }}"
                   class="btn btn-outline-primary {{ request('period') == 'yesterday' ? 'active' : '' }}">Yesterday</a>
                <a href="{{ route('admin.activity-logs.index', ['period' => 'this_week']) }}"
                   class="btn btn-outline-primary {{ request('period') == 'this_week' ? 'active' : '' }}">This Week</a>
                <a href="{{ route('admin.activity-logs.index', ['period' => 'this_month']) }}"
                   class="btn btn-outline-primary {{ request('period') == 'this_month' ? 'active' : '' }}">This Month</a>
                <a href="{{ route('admin.activity-logs.index') }}"
                   class="btn btn-outline-secondary {{ !request('period') ? 'active' : '' }}">All Time</a>
            </div>
        </div>
    </div>

    <!-- Advanced Filters -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Advanced Filters</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="user_id">User</label>
                            <select name="user_id" id="user_id" class="form-control">
                                <option value="">All Users</option>
                                @foreach($filterData['users'] as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="module">Module</label>
                            <select name="module" id="module" class="form-control">
                                <option value="">All Modules</option>
                                @foreach($filterData['modules'] as $module)
                                    <option value="{{ $module['value'] }}" {{ request('module') == $module['value'] ? 'selected' : '' }}>
                                        {{ $module['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="operation_type">Operation</label>
                            <select name="operation_type" id="operation_type" class="form-control">
                                <option value="">All Operations</option>
                                @foreach($filterData['operation_types'] as $operationType)
                                    <option value="{{ $operationType }}" {{ request('operation_type') == $operationType ? 'selected' : '' }}>
                                        {{ ucfirst(str_replace('_', ' ', $operationType)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_from">Date From</label>
                            <input type="date" name="date_from" id="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_to">Date To</label>
                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">Search Description</label>
                            <input type="text" name="search" id="search" class="form-control"
                                   value="{{ request('search') }}" placeholder="Search in descriptions...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="batch_uuid">Batch UUID</label>
                            <input type="text" name="batch_uuid" id="batch_uuid" class="form-control"
                                   value="{{ request('batch_uuid') }}" placeholder="Group related operations">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="subject_type">Subject Type</label>
                            <select name="subject_type" id="subject_type" class="form-control">
                                <option value="">All Types</option>
                                @foreach($filterData['subject_types'] as $subjectType)
                                    <option value="{{ $subjectType }}" {{ request('subject_type') == $subjectType ? 'selected' : '' }}>
                                        {{ class_basename($subjectType) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fas fa-times"></i> Clear All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Activity History ({{ $activities->total() }} records)</h3>
        </div>
        <div class="card-body">
            @if($activities->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th width="140">Date & Time</th>
                                <th width="120">User</th>
                                <th width="120">Module</th>
                                <th width="100">Operation</th>
                                <th>Description</th>
                                <th width="80">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <small class="d-block">{{ $activity->created_at->format('Y-m-d') }}</small>
                                        <small class="d-block text-muted">{{ $activity->created_at->format('H:i:s') }}</small>
                                        <small class="text-info">{{ $activity->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        @if($activity->causer)
                                            <div class="user-info">
                                                <strong class="d-block">{{ $activity->causer->name }}</strong>
                                                <small class="text-muted">{{ Str::limit($activity->causer->email, 20) }}</small>
                                            </div>
                                        @else
                                            <span class="badge badge-secondary">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $moduleClass = match($activity->log_name) {
                                                'stock_management' => 'badge-primary',
                                                'product_management' => 'badge-success',
                                                'order_management' => 'badge-info',
                                                'user_management' => 'badge-warning',
                                                'system_management' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $moduleClass }}">
                                            {{ Str::limit(str_replace('_management', '', $activity->log_name), 10) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $operationType = $activity->properties['operation_type'] ?? 'general';
                                            $operationClass = match(true) {
                                                str_contains($operationType, 'create') => 'badge-success',
                                                str_contains($operationType, 'update') => 'badge-primary',
                                                str_contains($operationType, 'delete') => 'badge-danger',
                                                str_contains($operationType, 'import') => 'badge-info',
                                                str_contains($operationType, 'export') => 'badge-warning',
                                                str_contains($operationType, 'error') => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $operationClass }}" style="font-size: 10px;">
                                            {{ Str::limit(str_replace('_', ' ', $operationType), 12) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="activity-description">
                                            {{ Str::limit($activity->description, 100) }}
                                        </div>

                                        @if(isset($activity->properties['batch_uuid']))
                                            <div class="mt-1">
                                                <small class="text-muted">
                                                    <i class="fas fa-link"></i>
                                                    <a href="{{ route('admin.activity-logs.index', ['batch_uuid' => $activity->properties['batch_uuid']]) }}"
                                                       class="text-primary">
                                                        Batch: {{ substr($activity->properties['batch_uuid'], 0, 8) }}...
                                                    </a>
                                                </small>
                                            </div>
                                        @endif

                                        @if($activity->subject)
                                            <div class="mt-1">
                                                <small class="text-info">
                                                    <i class="fas fa-arrow-right"></i>
                                                    {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                                </small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info"
                                                onclick="showActivityDetails({{ $activity->id }})"
                                                title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $activities->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-history fa-4x text-muted mb-3"></i>
                    <h4>No Activity Logs Found</h4>
                    <p class="text-muted">No admin activities match your current filters.</p>
                    <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-primary">
                        <i class="fas fa-refresh"></i> View All Activities
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Activity Details Modal -->
    <div class="modal fade" id="activityDetailsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-info-circle"></i> Activity Details
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="activityDetailsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Modal -->
    <div class="modal fade" id="statisticsModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-chart-bar"></i> Activity Statistics
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="statisticsContent">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2">Loading statistics...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cleanup Modal -->
    <div class="modal fade" id="cleanupModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-trash-alt"></i> Cleanup Old Activity Logs
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="cleanupForm">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Warning:</strong> This action will permanently delete old activity logs and cannot be undone.
                        </div>
                        <div class="form-group">
                            <label for="cleanup_days">Delete logs older than (days):</label>
                            <input type="number" name="days" id="cleanup_days" class="form-control"
                                   min="1" max="365" value="90" required>
                            <small class="form-text text-muted">
                                Recommended: 90 days for regular cleanup, 365 days for annual cleanup.
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Delete Old Logs
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop

@section('js')
<script>
// Show activity details
function showActivityDetails(activityId) {
    $('#activityDetailsModal').modal('show');

    $.ajax({
        url: `{{ route('admin.activity-logs.index') }}/${activityId}`,
        method: 'GET',
        success: function(response) {
            const activity = response.activity;
            const moduleName = response.module_name;
            const properties = response.formatted_properties;

            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle text-primary"></i> Basic Information</h6>
                        <table class="table table-sm table-bordered">
                            <tr><td><strong>ID:</strong></td><td>${activity.id}</td></tr>
                            <tr><td><strong>Date:</strong></td><td>${new Date(activity.created_at).toLocaleString()}</td></tr>
                            <tr><td><strong>User:</strong></td><td>${activity.causer ? activity.causer.name + ' (' + activity.causer.email + ')' : 'System'}</td></tr>
                            <tr><td><strong>Module:</strong></td><td><span class="badge badge-primary">${moduleName}</span></td></tr>
                            <tr><td><strong>Subject:</strong></td><td>${activity.subject_type ? activity.subject_type + ' #' + activity.subject_id : 'N/A'}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-cog text-success"></i> Operation Details</h6>
                        <table class="table table-sm table-bordered">
            `;

            if (properties.operation_type) {
                content += `<tr><td><strong>Operation:</strong></td><td><span class="badge badge-info">${properties.operation_type.replace(/_/g, ' ')}</span></td></tr>`;
            }

            if (properties.batch_uuid) {
                content += `<tr><td><strong>Batch UUID:</strong></td><td><code style="font-size: 11px;">${properties.batch_uuid}</code></td></tr>`;
            }

            content += `</table></div></div>`;

            // Description
            content += `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6><i class="fas fa-comment text-info"></i> Description</h6>
                        <div class="alert alert-light">${activity.description}</div>
                    </div>
                </div>
            `;

            // Properties
            if (Object.keys(properties).length > 0) {
                content += `
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6><i class="fas fa-database text-warning"></i> Additional Properties</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered">
                `;

                for (const [key, value] of Object.entries(properties)) {
                    if (!['operation_type', 'batch_uuid'].includes(key)) {
                        content += `<tr><td><strong>${key.replace(/_/g, ' ')}:</strong></td><td><pre style="font-size: 11px; margin: 0;">${value}</pre></td></tr>`;
                    }
                }

                content += `</table></div></div></div>`;
            }

            document.getElementById('activityDetailsContent').innerHTML = content;
        },
        error: function() {
            document.getElementById('activityDetailsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load activity details. Please try again.
                </div>
            `;
        }
    });
}

// Load statistics
function loadStats() {
    $('#statisticsModal').modal('show');

    $.ajax({
        url: '{{ route("admin.activity-logs.stats") }}',
        method: 'GET',
        success: function(response) {
            const stats = response.stats;
            const moduleStats = response.module_stats;
            const userStats = response.user_stats;
            const operationStats = response.operation_stats;

            let content = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-line text-primary"></i> Activity Overview</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Total Activities:</strong></td><td><span class="badge badge-primary">${stats.total_activities}</span></td></tr>
                            <tr><td><strong>Today:</strong></td><td><span class="badge badge-success">${stats.today_activities}</span></td></tr>
                            <tr><td><strong>This Week:</strong></td><td><span class="badge badge-info">${stats.this_week_activities}</span></td></tr>
                            <tr><td><strong>This Month:</strong></td><td><span class="badge badge-warning">${stats.this_month_activities}</span></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-users text-success"></i> Top Active Users</h6>
                        <table class="table table-sm">
            `;

            userStats.slice(0, 5).forEach(user => {
                content += `<tr><td>${user.user_name}</td><td><span class="badge badge-primary">${user.count}</span></td></tr>`;
            });

            content += `</table></div></div>`;

            // Module statistics
            if (Object.keys(moduleStats).length > 0) {
                content += `
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h6><i class="fas fa-cubes text-info"></i> Activities by Module</h6>
                            <table class="table table-sm">
                `;

                Object.entries(moduleStats).forEach(([module, count]) => {
                    content += `<tr><td>${module.replace(/_/g, ' ')}</td><td><span class="badge badge-info">${count}</span></td></tr>`;
                });

                content += `</table></div>`;
            }

            // Operation statistics
            if (Object.keys(operationStats).length > 0) {
                content += `
                        <div class="col-md-6">
                            <h6><i class="fas fa-cogs text-warning"></i> Top Operations</h6>
                            <table class="table table-sm">
                `;

                Object.entries(operationStats).slice(0, 5).forEach(([operation, count]) => {
                    content += `<tr><td>${operation.replace(/_/g, ' ')}</td><td><span class="badge badge-warning">${count}</span></td></tr>`;
                });

                content += `</table></div></div>`;
            }

            document.getElementById('statisticsContent').innerHTML = content;

            // Update header stats if visible
            if ($('#stats-cards').is(':visible')) {
                $('#total-activities').text(stats.total_activities);
                $('#today-activities').text(stats.today_activities);
                $('#week-activities').text(stats.this_week_activities);
                $('#month-activities').text(stats.this_month_activities);
            } else {
                $('#stats-cards').fadeIn();
                $('#total-activities').text(stats.total_activities);
                $('#today-activities').text(stats.today_activities);
                $('#week-activities').text(stats.this_week_activities);
                $('#month-activities').text(stats.this_month_activities);
            }
        },
        error: function() {
            document.getElementById('statisticsContent').innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Failed to load statistics. Please try again.
                </div>
            `;
        }
    });
}

// Show cleanup modal
function showCleanupModal() {
    $('#cleanupModal').modal('show');
}

// Handle cleanup form submission
$('#cleanupForm').on('submit', function(e) {
    e.preventDefault();

    const days = $('#cleanup_days').val();

    Swal.fire({
        title: 'Confirm Cleanup',
        text: `Are you sure you want to delete all activity logs older than ${days} days? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete them!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '{{ route("admin.activity-logs.cleanup") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    days: days
                },
                success: function(response) {
                    $('#cleanupModal').modal('hide');
                    Swal.fire({
                        icon: 'success',
                        title: 'Cleanup Complete',
                        text: response.message,
                        timer: 3000
                    });
                    location.reload();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Cleanup Failed',
                        text: 'An error occurred during cleanup. Please try again.'
                    });
                }
            });
        }
    });
});

// Auto-load stats on page load
$(document).ready(function() {
    // Auto-load stats if there are activities
    if ($('.table tbody tr').length > 0) {
        setTimeout(loadStats, 1000);
    }
});
</script>
@stop

@section('css')
<style>
.activity-description {
    font-size: 13px;
    line-height: 1.4;
}

.user-info {
    font-size: 12px;
}

.table-sm td {
    padding: 0.3rem;
    font-size: 12px;
}

.badge {
    font-size: 10px;
}

.modal-xl {
    max-width: 1200px;
}

pre {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 0.25rem;
    border: 1px solid #dee2e6;
}

.btn-group .btn {
    font-size: 12px;
}

.small-box h3 {
    font-size: 2rem;
}

@media (max-width: 768px) {
    .table-responsive {
        font-size: 11px;
    }

    .btn-group {
        flex-wrap: wrap;
    }

    .btn-group .btn {
        margin-bottom: 5px;
    }
}
</style>
@stop
