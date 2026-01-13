@extends('frontend.layouts.app')

@section('content')
<section>
    <div class="container">
        <div class="login-card">

        </div>
    </div>
</section>

@extends('frontend.layouts.app')

@section('content')
<section class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-5">
                <div class="login-card">
                    <h2 class="login-title fw-bold">Enter code</h2>
                    <p class="login-subtitle text-muted">
                        Sent to {{ session('email') }}
                    </p>

                    <form method="POST" action="{{ route('user.verifyOtp') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('email') }}">
                        <div class="form-group mb-3">
                            <input type="text" class="form-control" name="otp" placeholder="Enter 6-digit code">
                            @error('otp')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-style-1 w-100">{{ __('continue') }}</button>
                    </form>

                    <div class="login-footer mt-4">
                        <a href="{{ route('login') }}">{{ __('Log in with a different email') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@endsection
