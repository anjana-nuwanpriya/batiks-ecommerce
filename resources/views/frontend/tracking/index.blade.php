@extends('frontend.layouts.app')

@section('title', 'Track Your Order')

@section('content')
    @push('styles')
        <style>
            body {
                --bs-primary: #00664b;
                --bs-primary-rgb: 0, 102, 75;
            }
        </style>
    @endpush
    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="[['url' => route('home'), 'label' => 'Home'], ['label' => 'Track Order']]" />

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <!-- Header -->
                    <div class="text-center mb-5">
                        <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                            style="width: 80px; height: 80px;">
                            <i class="las la-search text-success" style="font-size: 2.5rem;"></i>
                        </div>
                        <h2 class="fw-bold mb-3">Track Your Order</h2>
                        <p class="text-muted">Enter your waybill number to get real-time tracking information</p>
                    </div>

                    <!-- Tracking Form -->
                    <div class="card shadow-sm border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="las la-exclamation-triangle me-2"></i>{{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('tracking.track') }}" method="GET" class="tracking-form">
                                <div class="row align-items-end">
                                    <div class="col-md-8 mb-3 mb-md-0">
                                        <label for="waybill_no" class="form-label fw-medium">Tracking No</label>
                                        <input type="text"
                                            class="form-control form-control-lg @error('waybill_no') is-invalid @enderror"
                                            id="waybill_no" name="waybill_no" placeholder=""
                                            value="{{ old('waybill_no') }}" required>
                                            @error('waybill_no')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary btn-lg w-100">
                                            <i class="las la-search me-2"></i>Track Order
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
