@extends('adminlte::page')

@section('title', 'SMS Service Status - ' . env('APP_NAME'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5>{{ __('SMS Service Status') }}</h5>
        </div>
        <div>
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" class="text-muted"><i
                            class="fa fa-home"></i></a></li>
                <li class="breadcrumb-item"><a href="{{ route('site.setting') }}"
                        class="text-muted">{{ __('Site Settings') }}</a></li>
                <li class="breadcrumb-item active">{{ __('SMS Status') }}</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sms"></i> {{ __('SMS Service Configuration Status') }}
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-primary" onclick="refreshStatus()">
                            <i class="fas fa-sync-alt"></i> {{ __('Refresh') }}
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="status-content">
                        <div class="text-center">
                            <i class="fas fa-spinner fa-spin fa-2x"></i>
                            <p class="mt-2">{{ __('Loading SMS status...') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Test Section -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-paper-plane"></i> {{ __('Quick SMS Test') }}
                    </h3>
                </div>
                <div class="card-body">
                    <form id="quick-sms-test">
                        @csrf
                        <div class="form-group">
                            <label for="quick_test_phone">{{ __('Phone Number') }}</label>
                            <input type="text" class="form-control" id="quick_test_phone" name="test_phone"
                                placeholder="{{ __('e.g., +94771234567') }}" required>
                        </div>
                        <div class="form-group">
                            <label for="quick_test_message">{{ __('Message') }}</label>
                            <textarea class="form-control" id="quick_test_message" name="test_message" rows="3"
                                placeholder="{{ __('Enter test message') }}">Quick test from {{ config('app.name') }} - {{ now()->format('Y-m-d H:i:s') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-block">
                            <i class="fas fa-paper-plane"></i> {{ __('Send Test SMS') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> {{ __('SMS Tools') }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical btn-block">
                        <button type="button" class="btn btn-warning mb-2" onclick="clearTokens()">
                            <i class="fas fa-trash"></i> {{ __('Clear SMS Tokens') }}
                        </button>
                        <button type="button" class="btn btn-info mb-2" onclick="refreshStatus()">
                            <i class="fas fa-sync-alt"></i> {{ __('Refresh Status') }}
                        </button>
                        <a href="{{ route('site.setting') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> {{ __('Back to Settings') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            loadSmsStatus();

            // Quick SMS test form
            $('#quick-sms-test').on('submit', function(e) {
                e.preventDefault();

                const submitBtn = $(this).find('button[type="submit"]');
                const originalText = submitBtn.html();

                submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Sending...').prop('disabled', true);

                $.ajax({
                    url: '{{ route('test.sms') }}',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response.message || 'Failed to send test SMS');
                    },
                    complete: function() {
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });

        function loadSmsStatus() {
            $.ajax({
                url: '{{ route('sms.status') }}',
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        displayStatus(response.data);
                    } else {
                        $('#status-content').html(
                            '<div class="alert alert-danger">Failed to load SMS status</div>');
                    }
                },
                error: function() {
                    $('#status-content').html('<div class="alert alert-danger">Error loading SMS status</div>');
                }
            });
        }

        function displayStatus(data) {
            const serviceStatus = data.service_status;
            const tokenStatus = data.token_status;
            const configStatus = data.configuration_status;
            const envVars = data.environment_variables;

            let html = `
        <div class="row">
            <div class="col-md-6">
                <h5><i class="fas fa-cog"></i> Service Configuration</h5>
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Service Enabled</strong></td>
                        <td>${getStatusBadge(serviceStatus.enabled)}</td>
                    </tr>
                    <tr>
                        <td><strong>All Required Variables Set</strong></td>
                        <td>${getStatusBadge(configStatus.all_required_vars_set)}</td>
                    </tr>`;

            if (configStatus.missing_variables && configStatus.missing_variables.length > 0) {
                html += `
                    <tr>
                        <td><strong>Missing Variables</strong></td>
                        <td><span class="badge badge-warning">${configStatus.missing_variables.join(', ')}</span></td>
                    </tr>`;
            }

            html += `
                    <tr>
                        <td><strong>Username Configured</strong></td>
                        <td>${getConfigBadge(envVars.HUTCH_SMS_USERNAME)}</td>
                    </tr>
                    <tr>
                        <td><strong>Password Configured</strong></td>
                        <td>${getConfigBadge(envVars.HUTCH_SMS_PASSWORD)}</td>
                    </tr>
                    <tr>
                        <td><strong>Campaign Configured</strong></td>
                        <td>${getConfigBadge(envVars.HUTCH_CAMPAIGN_NAME)}</td>
                    </tr>
                    <tr>
                        <td><strong>Mask Configured</strong></td>
                        <td>${getConfigBadge(envVars.HUTCH_MASK)}</td>
                    </tr>
                    <tr>
                        <td><strong>Debug Mode</strong></td>
                        <td>${getConfigBadge(envVars.HUTCH_DEBUG)}</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5><i class="fas fa-key"></i> Token Status</h5>
                <table class="table table-bordered">
                    <tr>
                        <td><strong>Access Token</strong></td>
                        <td>${getTokenStatusBadge(tokenStatus.access_token)}</td>
                    </tr>
                    <tr>
                        <td><strong>Refresh Token</strong></td>
                        <td>${getTokenStatusBadge(tokenStatus.refresh_token)}</td>
                    </tr>
                    <tr>
                        <td><strong>Access Token Expires</strong></td>
                        <td><span class="badge badge-info">${tokenStatus.access_token_expires || 'N/A'}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Refresh Token Expires</strong></td>
                        <td><span class="badge badge-info">${tokenStatus.refresh_token_expires || 'N/A'}</span></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h5><i class="fas fa-list"></i> Environment Variables</h5>
                <table class="table table-bordered">
    `;

            Object.keys(envVars).forEach(key => {
                html += `
            <tr>
                <td><strong>${key}</strong></td>
                <td>${getConfigBadge(envVars[key])}</td>
            </tr>
        `;
            });

            html += `
                </table>
            </div>
        </div>
    `;

            // Show service status details if available
            if (serviceStatus.error) {
                html += `
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle"></i> Service Status Note:</h6>
                        <p>${serviceStatus.error}</p>
                    </div>
                </div>
            </div>
        `;
            }

            $('#status-content').html(html);
        }

        function getStatusBadge(status) {
            if (status) {
                return '<span class="badge badge-success"><i class="fas fa-check"></i> Yes</span>';
            } else {
                return '<span class="badge badge-danger"><i class="fas fa-times"></i> No</span>';
            }
        }

        function getConfigBadge(value) {
            if (value === 'Set' || value === 'Enabled') {
                return '<span class="badge badge-success">' + value + '</span>';
            } else if (value === 'Not Set' || value === 'Disabled') {
                return '<span class="badge badge-danger">' + value + '</span>';
            } else {
                return '<span class="badge badge-info">' + value + '</span>';
            }
        }

        function getTokenStatusBadge(status) {
            if (status === 'Valid') {
                return '<span class="badge badge-success"><i class="fas fa-check"></i> ' + status + '</span>';
            } else if (status === 'Not Found') {
                return '<span class="badge badge-danger"><i class="fas fa-times"></i> ' + status + '</span>';
            } else {
                return '<span class="badge badge-warning">' + status + '</span>';
            }
        }

        function refreshStatus() {
            $('#status-content').html(`
        <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p class="mt-2">Refreshing SMS status...</p>
        </div>
    `);
            loadSmsStatus();
        }

        function clearTokens() {
            if (confirm(
                    'Are you sure you want to clear SMS tokens? This will require fresh authentication for the next SMS.'
                    )) {
                $.ajax({
                    url: '{{ route('sms.clear.tokens') }}',
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            refreshStatus();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        toastr.error(response.message || 'Failed to clear tokens');
                    }
                });
            }
        }
    </script>
@endpush
