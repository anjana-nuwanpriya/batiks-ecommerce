@extends('frontend.layouts.app')

@section('content')
<section class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-5">
                <div class="login-card">
                    <h2 class="login-title fw-bold">Create Account</h2>
                    <p class="login-subtitle text-muted">
                        Enter your details to create an account
                    </p>

                    <form method="POST" action="{{ route('user.register') }}" id="registerForm">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" />
                            <div class="field-notice text-danger fs-13" rel="name"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" />
                            <div class="field-notice text-danger fs-13" rel="phone"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" />
                            <div class="field-notice text-danger fs-13" rel="email"></div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" class="form-control" />
                            <div class="field-notice text-danger fs-13" rel="password"></div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" />
                            <div class="field-notice text-danger fs-13" rel="password_confirmation"></div>
                        </div>

                        <div class="form-group mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="terms" id="terms" class="form-check-input" />
                                <label class="form-check-label" for="terms">
                                    I agree to the <a href="#" class="text-success">Terms & Conditions</a> and <a href="#" class="text-success">Privacy Policy</a>
                                </label>
                                <div class="field-notice text-danger fs-13" rel="terms"></div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-style-1 w-100">Create Account</button>
                    </form>

                    <div class="login-separator">
                        <span>or</span>
                    </div>

                    <div class="login-social">
                        <a href="{{ route('auth.google') }}" class="btn btn-google">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48"><path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/><path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/><path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/><path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/></svg>
                            Continue with Google
                        </a>

                        <a href="{{ route('auth.facebook') }}" class="btn btn-facebook">
                            <i class="lab la-facebook-f"></i> Continue with Facebook
                        </a>
                    </div>

                    <div class="login-footer mt-4 text-center">
                        <p>Already have an account? <a href="{{ route('user.login') }}" class="text-success">Log in</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- OTP Verification Modal -->
<div class="modal fade" id="otpModal" tabindex="-1" aria-labelledby="otpModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="otpModalLabel">Verify Your Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">We've sent a verification code to your contact information. Please enter it below.</p>
                <p class="text-muted">Time remaining: <span id="timer" class="text-danger fw-bold">05:00</span></p>
                <form id="otpForm" action="{{ route('user.verify.registration') }}" method="POST">
                    @csrf
                    <input type="hidden" name="phone" id="verifyPhone">
                    <div class="form-group mb-3">
                        <label for="otp" class="form-label">Verification Code</label>
                        <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter 6-digit code" required>
                        <div class="field-notice text-danger fs-13" rel="otp"></div>
                    </div>
                    <button type="submit" class="btn btn-style-1 w-100">Verify</button>
                </form>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script>
    let timerInterval;

    // Start Timer
    function startTimer(duration, display) {
        let timer = duration;
        clearInterval(timerInterval);

        timerInterval = setInterval(function () {
            let minutes = parseInt(timer / 60, 10);
            let seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            display.textContent = minutes + ":" + seconds;

            if (--timer < 0) {
                clearInterval(timerInterval);
                $('#otpModal').modal('hide');
                Swal.fire({
                    icon: 'error',
                    title: 'OTP Expired',
                    text: 'The verification code has expired. Please try again.',
                });
                $('#registerForm')[0].reset();
            }
        }, 1000);
    }

    // Register Form Submit
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#registerForm button[type="submit"]').prop('disabled', true);
                $('#registerForm button[type="submit"]').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function(response) {
                if (response.success) {
                    $('#otpModal').modal('show');
                    $('#verifyPhone').val(response.phone);
                    // Start the 5-minute countdown
                    startTimer(300, document.querySelector('#timer'));
                }
            },
            error: function(xhr, status, error) {
                form.find('.field-notice').text('');
                // Handle validation errors
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        form.find('.field-notice[rel="' + field + '"]').text(messages[0]);
                    });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.message,
                    });
                }
            },
            complete: function() {
                $('#registerForm button[type="submit"]').prop('disabled', false);
                $('#registerForm button[type="submit"]').html('Create Account');
            }
        });
    });

    // Clear timer when modal is hidden
    $('#otpModal').on('hidden.bs.modal', function () {
        clearInterval(timerInterval);
    });

    // Verify OTP
    $('#otpForm').on('submit', function(e) {
        e.preventDefault();

        var form = $(this);

        $.ajax({
            url: $(this).attr('action'),
            type: $(this).attr('method'),
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            beforeSend: function() {
                $('#otpForm button[type="submit"]').prop('disabled', true);
                $('#otpForm button[type="submit"]').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                });
                if (response.success) {
                    setTimeout(() => {
                        window.location.href = response.redirect;
                    }, 500);
                }
            },
            error: function(xhr, status, error) {
                form.find('.field-notice').text('');
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        form.find('.field-notice[rel="' + field + '"]').text(messages[0]);
                    });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.message,
                    });
                }
            },
            complete: function() {
                $('#otpForm button[type="submit"]').prop('disabled', false);
                $('#otpForm button[type="submit"]').html('Verify');
            }
        });
    });
</script>
@endsection
@endsection
