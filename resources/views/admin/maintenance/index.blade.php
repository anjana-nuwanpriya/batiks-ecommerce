@extends('adminlte::page')

@section('title', 'Maintenance Mode')

@section('content_header')
    <h1>Maintenance Mode Management</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i>
                        Maintenance Mode Control
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $isDown ? 'danger' : 'success' }}">
                            {{ $isDown ? 'MAINTENANCE MODE ON' : 'SITE ONLINE' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @if ($isDown)
                        <div class="alert alert-warning">
                            <h5><i class="icon fas fa-exclamation-triangle"></i> Site is in Maintenance Mode</h5>
                            @if ($maintenanceData)
                                @if (isset($maintenanceData['message']))
                                    <p><strong>Message:</strong> {{ $maintenanceData['message'] }}</p>
                                @endif
                                @if (isset($maintenanceData['retry']))
                                    <p><strong>Retry After:</strong> {{ $maintenanceData['retry'] }} seconds</p>
                                @endif
                                @if (isset($maintenanceData['allowed']))
                                    <p><strong>Allowed IPs:</strong> {{ implode(', ', $maintenanceData['allowed']) }}</p>
                                @endif
                                @if (isset($maintenanceData['secret']))
                                    <p><strong>Secret Token:</strong> {{ $maintenanceData['secret'] }}</p>
                                    <p><small class="text-muted">Access URL:
                                            {{ url('/?secret=' . $maintenanceData['secret']) }}</small></p>
                                @endif
                            @endif
                        </div>

                        <button type="button" class="btn btn-success btn-lg" onclick="disableMaintenance()">
                            <i class="fas fa-play"></i> Disable Maintenance Mode
                        </button>
                    @else
                        <form id="maintenanceForm">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="message">Custom Message</label>
                                        <input type="text" class="form-control" id="message" name="message"
                                            placeholder="We're performing scheduled maintenance...">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="retry">Retry After (seconds)</label>
                                        <input type="number" class="form-control" id="retry" name="retry"
                                            placeholder="60" min="1">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="allowed_ips">Allowed IPs (comma separated)</label>
                                        <input type="text" class="form-control" id="allowed_ips" name="allowed_ips"
                                            placeholder="127.0.0.1, 192.168.1.100">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="secret">Secret Bypass Token</label>
                                        <input type="text" class="form-control" id="secret" name="secret"
                                            placeholder="maintenance-bypass-token">
                                    </div>
                                </div>
                            </div>

                            <button type="button" class="btn btn-danger btn-lg" onclick="enableMaintenance()">
                                <i class="fas fa-pause"></i> Enable Maintenance Mode
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Information
                    </h3>
                </div>
                <div class="card-body">
                    <h5>What is Maintenance Mode?</h5>
                    <p>Maintenance mode temporarily disables your website for all users except those with special access.
                    </p>

                    <h5>Features:</h5>
                    <ul>
                        <li><strong>Custom Message:</strong> Display a custom message to visitors</li>
                        <li><strong>Retry After:</strong> Tell browsers when to check back</li>
                        <li><strong>IP Whitelist:</strong> Allow specific IPs to access the site</li>
                        <li><strong>Secret Token:</strong> Create a bypass URL for testing</li>
                    </ul>

                    <h5>Best Practices:</h5>
                    <ul>
                        <li>Schedule maintenance during low traffic hours</li>
                        <li>Notify users in advance</li>
                        <li>Keep maintenance windows short</li>
                        <li>Test with secret token before going live</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        function enableMaintenance() {
            const formData = new FormData(document.getElementById('maintenanceForm'));

            fetch('{{ route('admin.maintenance.enable') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        setTimeout(() => {
                            window.location.reload();
                        }, 1500);
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'Failed to enable maintenance mode',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'An error occurred',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                });
        }

        function disableMaintenance() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to disable maintenance mode?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, disable it!'
            }).then((result) => {
                if (!result.isConfirmed) {
                    return;
                }

                fetch('{{ route('admin.maintenance.disable') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Failed to disable maintenance mode',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'An error occurred',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    });
            });
        }

        // Auto-refresh status every 30 seconds
        setInterval(() => {
            fetch('{{ route('admin.maintenance.status') }}')
                .then(response => response.json())
                .then(data => {
                    // Update status badge if needed
                    const currentStatus = {{ $isDown ? 'true' : 'false' }};
                    if (data.is_down !== currentStatus) {
                        window.location.reload();
                    }
                });
        }, 30000);
    </script>
@stop
