@extends('frontend.layouts.app')

@section('content')
<section class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-5">
                <div class="login-card">
                    <ul class="nav nav-tabs mb-4" id="loginTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ !session('type') || session('type') == 'phone' ? 'active' : '' }}" id="phone-tab" data-bs-toggle="tab" data-bs-target="#phone-login" type="button" role="tab" aria-controls="phone-login" aria-selected="true">Phone</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ session('type') == 'email' ? 'active' : '' }}" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-login" type="button" role="tab" aria-controls="email-login" aria-selected="false">Email</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="loginTabsContent">
                        <div class="tab-pane fade {{ !session('type') || session('type') == 'phone' ? 'show active' : '' }}" id="phone-login" role="tabpanel" aria-labelledby="phone-tab">
                            <h2 class="login-title fw-bold">Log in with Phone</h2>
                            <p class="login-subtitle text-muted">
                                Enter your phone number and password to access your account
                            </p>

                            <form method="POST" action="{{ route('user.login') }}" class="login-form">
                                @csrf
                                <input type="hidden" name="type" value="phone">
                                <div class="form-group mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}"/>
                                    @error('phone')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" id="password" class="form-control" />
                                    @error('password')
                                        <span class="text-danger small">{{ $message }}</span>
                                    @enderror
                                    <div class="text-end mt-2">
                                        <a href="{{ route('user.forgot.password') }}" class="text-muted small">Forgot Password?</a>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-style-1 w-100">Login</button>
                            </form>
                        </div>

                        <div class="tab-pane fade {{ session('type') == 'email' ? 'show active' : '' }}" id="email-login" role="tabpanel" aria-labelledby="email-tab">
                            <h2 class="login-title fw-bold">Log in with Email</h2>
                            <p class="login-subtitle text-muted">
                                Enter your email and password to access your account
                            </p>


                            <form method="POST" action="{{ route('user.login') }}" class="login-form">
                            @csrf
                            <input type="hidden" name="type" value="email">
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}"/>
                                @error('email')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" id="password" class="form-control"  />
                                @error('password')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                                <div class="text-end mt-2">
                                    <a href="{{ route('user.forgot.password') }}" class="text-muted small">Forgot Password?</a>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-style-1 w-100">Continue</button>
                            </form>
                        </div>
                    </div>

                    <div class="login-separator">
                        <span>or</span>
                    </div>

                    <div class="login-social">
                        <a href="{{ route('auth.google') }}" class="btn btn-google w-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                            Continue with Google
                        </a>

                        <a href="{{ route('auth.facebook') }}" class="btn btn-facebook w-100">
                            <i class="lab la-facebook-f"></i> Continue with Facebook
                        </a>
                    </div>

                    <div class="login-separator">
                    </div>

                    <div class="login-footer mt-3">
                        <p class="text-center">Don't have an account? <a href="{{ route('user.register') }}" class="text-success">Sign up</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection