@extends('frontend.layouts.app')

@section('content')
<div class="error-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="error-content text-center">

                    <!-- Error Title -->
                    <h1 class="error-title mb-3">{{ __('Internal Server Error') }}</h1>

                    <!-- Error Message -->
                    <p class="error-message mb-4">
                        {{ __('Oops! Something went wrong on our end. Our technical team has been notified and is working to fix this issue. Please try again in a few moments.') }}
                    </p>

                    <!-- Error Details (only in debug mode) -->
                    @if(config('app.debug') && isset($exception))
                        <div class="error-details mb-4">
                            <details class="bg-light p-3 rounded">
                                <summary class="text-danger fw-bold mb-2" style="cursor: pointer;">{{ __('Technical Details') }}</summary>
                                <div class="text-start">
                                    <p><strong>{{ __('Error:') }}</strong> {{ $exception->getMessage() }}</p>
                                    <p><strong>{{ __('File:') }}</strong> {{ $exception->getFile() }}</p>
                                    <p><strong>{{ __('Line:') }}</strong> {{ $exception->getLine() }}</p>
                                </div>
                            </details>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="error-actions mb-4">
                        <button onclick="window.location.reload()" class="btn btn-style-1 me-3">
                            <i class="las la-redo-alt"></i> {{ __('Try Again') }}
                        </button>
                        <a href="{{ route('home') }}" class="btn btn-outline-primary me-3">
                            <i class="las la-home"></i> {{ __('Go Home') }}
                        </a>
                        <a href="javascript:history.back()" class="btn btn-outline-secondary">
                            <i class="las la-arrow-left"></i> {{ __('Go Back') }}
                        </a>
                    </div>

                    <!-- Status Check -->
                    <div class="status-check mb-4">
                        <div class="d-flex justify-content-center align-items-center">
                            <div class="spinner-border text-primary me-2" role="status" id="statusSpinner" style="display: none;">
                                <span class="visually-hidden">{{ __('Loading...') }}</span>
                            </div>
                            <button onclick="checkServerStatus()" class="btn btn-link text-decoration-none" id="statusButton">
                                <i class="las la-heartbeat"></i> {{ __('Check Server Status') }}
                            </button>
                        </div>
                        <div id="statusResult" class="mt-2"></div>
                    </div>

                    <!-- Contact Support -->
                    <div class="support-section">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="support-card">
                                    <i class="las la-envelope support-icon"></i>
                                    <h6>{{ __('Email Support') }}</h6>
                                    <a href="mailto:{{ get_setting('email') }}" class="btn btn-sm btn-outline-primary">
                                        {{ get_setting('email') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="support-card">
                                    <i class="las la-phone support-icon"></i>
                                    <h6>{{ __('Phone Support') }}</h6>
                                    <a href="tel:{{ get_setting('phone') }}" class="btn btn-sm btn-outline-primary">
                                        {{ get_setting('phone') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="support-card">
                                    <i class="lab la-whatsapp support-icon"></i>
                                    <h6>{{ __('WhatsApp') }}</h6>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('phone')) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        {{ __('Chat Now') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error ID for reference -->
                    <div class="error-reference mt-4">
                        <small class="text-muted">
                            {{ __('Error Reference:') }} #{{ substr(md5(time()), 0, 8) }} | {{ now()->format('Y-m-d H:i:s') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.error-page-wrapper {
    min-height: 60vh;
    display: flex;
    align-items: center;
    padding: 60px 0;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(255, 255, 255, 0.1) 100%);
}

.error-content {
    background: white;
    padding: 3rem 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.1);
    border: 1px solid rgba(220, 53, 69, 0.1);
}

.error-icon {
    animation: shake 2s ease-in-out infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

.error-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
}

.error-message {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto 2rem;
}

.error-details {
    max-width: 600px;
    margin: 0 auto;
}

.error-details summary {
    outline: none;
}

.error-actions .btn {
    padding: 12px 30px;
    border-radius: 50px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.status-check {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 15px;
    border: 1px solid #e9ecef;
}

.support-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    margin-top: 2rem;
}

.support-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    height: 100%;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.support-card:hover {
    border-color: #008c68;
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 140, 104, 0.15);
}

.support-icon {
    font-size: 2.5rem;
    color: #008c68;
    margin-bottom: 1rem;
}

.support-card h6 {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
}

.error-reference {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #dc3545;
}

@media (max-width: 768px) {
    .error-title {
        font-size: 2rem;
    }

    .error-content {
        padding: 2rem 1rem;
    }

    .error-actions .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }

    .error-actions .btn:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection

@section('scripts')
<script>
function checkServerStatus() {
    const button = document.getElementById('statusButton');
    const spinner = document.getElementById('statusSpinner');
    const result = document.getElementById('statusResult');

    button.style.display = 'none';
    spinner.style.display = 'inline-block';

    // Simulate status check
    setTimeout(() => {
        spinner.style.display = 'none';
        button.style.display = 'inline-block';

        // Simple ping to check if server is responding
        fetch('{{ route("home") }}', { method: 'HEAD' })
            .then(response => {
                if (response.ok) {
                    result.innerHTML = '<div class="alert alert-success">{{ __("Server is responding. You can try refreshing the page.") }}</div>';
                } else {
                    result.innerHTML = '<div class="alert alert-warning">{{ __("Server is experiencing issues. Please try again later.") }}</div>';
                }
            })
            .catch(() => {
                result.innerHTML = '<div class="alert alert-danger">{{ __("Unable to connect to server. Please check your internet connection.") }}</div>';
            });
    }, 2000);
}

// Auto-refresh after 30 seconds
setTimeout(() => {
    if (confirm('{{ __("Would you like to try refreshing the page?") }}')) {
        window.location.reload();
    }
}, 30000);
</script>
@endsection
