@extends('frontend.layouts.app')

@section('content')
<div class="maintenance-page-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="maintenance-content text-center">

                    <!-- Maintenance Title -->
                    <h1 class="maintenance-title mb-3">{{ __("We'll Be Back Soon!") }}</h1>

                    <!-- Maintenance Message -->
                    <div class="maintenance-message mb-4">
                        @if(isset($exception) && method_exists($exception, 'getMessage') && $exception->getMessage())
                            <p>{{ $exception->getMessage() }}</p>
                        @else
                            <p>{{ __('We are currently performing scheduled maintenance to improve your experience. Thank you for your patience!') }}</p>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    <div class="progress-bar mb-4">
                        <div class="progress-fill"></div>
                    </div>

                    <!-- Countdown Timer -->
                    @if(isset($exception) && method_exists($exception, 'getRetryAfter') && $exception->getRetryAfter())
                    <div class="countdown-section mb-4">
                        <h5 class="mb-3">{{ __('Estimated Time Remaining') }}</h5>
                        <div class="timer" id="countdown">
                            <div class="time-unit">
                                <span class="time-number" id="hours">00</span>
                                <span class="time-label">{{ __('Hours') }}</span>
                            </div>
                            <div class="time-unit">
                                <span class="time-number" id="minutes">00</span>
                                <span class="time-label">{{ __('Minutes') }}</span>
                            </div>
                            <div class="time-unit">
                                <span class="time-number" id="seconds">00</span>
                                <span class="time-label">{{ __('Seconds') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Status Check -->
                    <div class="status-check mb-4">
                        <button onclick="checkStatus()" class="btn btn-style-1">
                            <i class="las la-redo-alt"></i> {{ __('Check Status') }}
                        </button>
                    </div>

                    <!-- Contact Information -->
                    <div class="contact-section">
                        <h5 class="mb-3">{{ __('Need Immediate Assistance?') }}</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="contact-card">
                                    <i class="las la-envelope contact-icon"></i>
                                    <h6>{{ __('Email Support') }}</h6>
                                    <a href="mailto:{{ get_setting('email') }}" class="btn btn-sm btn-outline-primary">
                                        {{ get_setting('email') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="contact-card">
                                    <i class="las la-phone contact-icon"></i>
                                    <h6>{{ __('Phone Support') }}</h6>
                                    <a href="tel:{{ get_setting('phone') }}" class="btn btn-sm btn-outline-primary">
                                        {{ get_setting('phone') }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="contact-card">
                                    <i class="lab la-whatsapp contact-icon"></i>
                                    <h6>{{ __('WhatsApp') }}</h6>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('phone')) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        {{ __('Chat Now') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Auto-refresh Notice -->
                    <div class="auto-refresh-notice mt-4">
                        <small class="text-muted">
                            <i class="las la-info-circle"></i>
                            {{ __('This page will automatically refresh every 30 seconds') }}
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
.maintenance-page-wrapper {
    min-height: 60vh;
    display: flex;
    align-items: center;
    padding: 60px 0;
    background: linear-gradient(135deg, rgba(0, 140, 104, 0.05) 0%, rgba(255, 255, 255, 0.1) 100%);
}

.maintenance-content {
    background: white;
    padding: 3rem 2rem;
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(0, 140, 104, 0.1);
    border: 1px solid rgba(0, 140, 104, 0.1);
}

.maintenance-icon {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.maintenance-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 1rem;
}

.maintenance-message {
    font-size: 1.1rem;
    color: #666;
    line-height: 1.6;
    max-width: 600px;
    margin: 0 auto 2rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #f0f0f0;
    border-radius: 4px;
    overflow: hidden;
    margin: 2rem 0;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #008c68, #33a882);
    border-radius: 4px;
    animation: progress 3s ease-in-out infinite;
}

@keyframes progress {
    0% { width: 0%; }
    50% { width: 70%; }
    100% { width: 100%; }
}

.countdown-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    border-left: 4px solid #008c68;
}

.timer {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.time-unit {
    background: #008c68;
    color: white;
    padding: 1.5rem 1rem;
    border-radius: 10px;
    min-width: 80px;
    box-shadow: 0 5px 15px rgba(0, 140, 104, 0.2);
}

.time-number {
    font-size: 1.8rem;
    font-weight: bold;
    display: block;
    line-height: 1;
}

.time-label {
    font-size: 0.8rem;
    text-transform: uppercase;
    margin-top: 0.5rem;
    opacity: 0.9;
}

.status-check {
    margin: 2rem 0;
}

.contact-section {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 15px;
    margin-top: 2rem;
}

.contact-card {
    background: white;
    padding: 1.5rem;
    border-radius: 10px;
    text-align: center;
    height: 100%;
    border: 2px solid transparent;
    transition: all 0.3s ease;
}

.contact-card:hover {
    border-color: #008c68;
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 140, 104, 0.15);
}

.contact-icon {
    font-size: 2.5rem;
    color: #008c68;
    margin-bottom: 1rem;
}

.contact-card h6 {
    font-weight: 600;
    margin-bottom: 1rem;
    color: #333;
}

.auto-refresh-notice {
    background: #e3f2fd;
    padding: 1rem;
    border-radius: 10px;
    border-left: 4px solid #2196f3;
}

@media (max-width: 768px) {
    .maintenance-title {
        font-size: 2rem;
    }

    .maintenance-content {
        padding: 2rem 1rem;
    }

    .timer {
        gap: 0.5rem;
    }

    .time-unit {
        min-width: 70px;
        padding: 1rem 0.5rem;
    }

    .time-number {
        font-size: 1.5rem;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Auto-refresh every 30 seconds
setTimeout(() => {
    window.location.reload();
}, 30000);

// Countdown timer (if retry-after is set)
@if(isset($exception) && method_exists($exception, 'getRetryAfter') && $exception->getRetryAfter())
let retryAfter = {{ $exception->getRetryAfter() }};
let countdownDate = new Date().getTime() + (retryAfter * 1000);

let countdownTimer = setInterval(function() {
    let now = new Date().getTime();
    let distance = countdownDate - now;

    if (distance < 0) {
        clearInterval(countdownTimer);
        window.location.reload();
        return;
    }

    let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    let seconds = Math.floor((distance % (1000 * 60)) / 1000);

    document.getElementById("hours").innerHTML = hours.toString().padStart(2, '0');
    document.getElementById("minutes").innerHTML = minutes.toString().padStart(2, '0');
    document.getElementById("seconds").innerHTML = seconds.toString().padStart(2, '0');
}, 1000);
@endif

function checkStatus() {
    window.location.reload();
}
</script>
@endsection
