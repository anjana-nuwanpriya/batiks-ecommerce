@extends('frontend.layouts.app')

@section('content')
    <div class="error-page-wrapper">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-md-10">
                    <div class="error-content text-center">

                        <!-- Error Title -->
                        <h1 class="error-title mb-3">{{ __('Page Not Found') }}</h1>

                        <!-- Error Message -->
                        <p class="error-message mb-4">
                            {{ __('Sorry, the page you are looking for could not be found.') }}
                        </p>

                        <!-- Action Buttons -->
                        <div class="error-actions">
                            <a href="{{ route('home') }}" class="btn btn-style-1 me-3">
                                <i class="las la-home"></i> {{ __('Go Home') }}
                            </a>
                            <a href="javascript:history.back()" class="btn btn-outline-success">
                                <i class="las la-arrow-left"></i> {{ __('Go Back') }}
                            </a>
                        </div>

                        <!-- Helpful Links -->
                        <div class="helpful-links mt-5">
                            <h5 class="mb-3">{{ __('You might be looking for:') }}</h5>
                            <div class="row">
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="{{ route('sf.products.list') }}" class="helpful-link">
                                        <i class="las la-box"></i>
                                        <span>{{ __('All Products') }}</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="{{ route('about') }}" class="helpful-link">
                                        <i class="las la-info-circle"></i>
                                        <span>{{ __('About Us') }}</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="{{ route('contact') }}" class="helpful-link">
                                        <i class="las la-envelope"></i>
                                        <span>{{ __('Contact Us') }}</span>
                                    </a>
                                </div>
                                <div class="col-md-3 col-6 mb-3">
                                    <a href="{{ route('tracking.index') }}" class="helpful-link">
                                        <i class="las la-shipping-fast"></i>
                                        <span>{{ __('Track Order') }}</span>
                                    </a>
                                </div>
                            </div>
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
            background: linear-gradient(135deg, rgba(0, 140, 104, 0.05) 0%, rgba(255, 255, 255, 0.1) 100%);
        }

        .error-content {
            background: white;
            padding: 3rem 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 140, 104, 0.1);
            border: 1px solid rgba(0, 140, 104, 0.1);
        }

        .error-icon {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
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

        .error-search .input-group {
            box-shadow: 0 5px 15px rgba(0, 140, 104, 0.1);
            border-radius: 50px;
            overflow: hidden;
        }

        .error-search .form-control {
            border: 2px solid #008c68;
            border-right: none;
            padding: 12px 20px;
        }

        .error-search .form-control:focus {
            box-shadow: none;
            border-color: #008c68;
        }

        .error-actions .btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .helpful-links {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin-top: 2rem;
        }

        .helpful-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 1rem;
            background: white;
            border-radius: 10px;
            text-decoration: none;
            color: #333;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }

        .helpful-link:hover {
            color: #008c68;
            border-color: #008c68;
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 140, 104, 0.15);
        }

        .helpful-link i {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #008c68;
        }

        .helpful-link span {
            font-weight: 600;
            font-size: 0.9rem;
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
