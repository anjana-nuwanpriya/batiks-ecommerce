@extends('frontend.layouts.app')

@section('content')
<section class="login-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-5">
                <div class="login-card">
                    <h2 class="login-title fw-bold">Reset Password</h2>
                    <p class="login-subtitle text-muted">
                        Enter your email address or phone number and we'll send you a link to reset your password
                    </p>
                        <ul class="nav nav-tabs mb-3" id="resetTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email" type="button" role="tab">Email</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="phone-tab" data-bs-toggle="tab" data-bs-target="#phone" type="button" role="tab">Phone</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="resetTabContent">
                            <div class="tab-pane fade show active" id="email" role="tabpanel">
                                <form method="POST" action="{{ route('user.forgot.password') }}" class="forgot-password-form">
                                    @csrf
                                    <input type="hidden" name="type" value="email">
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" />
                                        <div class="field-notice text-danger" rel="email"></div>
                                    </div>
                                    <button type="submit" class="btn btn-style-1 w-100">Reset Password</button>
                                </form>
                            </div>
                            <div class="tab-pane fade" id="phone" role="tabpanel">
                                <form method="POST" action="{{ route('user.forgot.password') }}" class="forgot-password-form">
                                    @csrf
                                    <input type="hidden" name="type" value="phone">
                                    <div class="form-group mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" name="phone" id="phone" class="form-control" value="{{ old('phone') }}" />
                                        <div class="field-notice text-danger" rel="phone"></div>
                                    </div>
                                    <button type="submit" class="btn btn-style-1 w-100">Reset Password</button>
                                </form>
                            </div>
                        </div>
                    </form>
                    <div class="login-footer mt-4 text-center">
                        <a href="{{ route('user.login')  }}" class="text-muted">Back to Login</a>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $('.forgot-password-form').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: form.serialize(),
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    beforeSend: function() {
                        form.find('button[type="submit"]').prop('disabled', true);
                        form.find('button[type="submit"]').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
                    },
                    success: function(response) {
                        if (response.success) {
                            if (response.type == 'email') {
                                // $('#otpModal').modal('show');
                                // $('#verifyPhone').val(response.phone);
                                // // Start the 5-minute countdown
                                // startTimer(300, document.querySelector('#timer'));
                            }else{
                                $('#otpModal').modal('show');
                                $('#verifyPhone').val(response.phone);
                                // Start the 5-minute countdown
                                startTimer(300, document.querySelector('#timer'));
                            }
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
                        form.find('button[type="submit"]').prop('disabled', false);
                        form.find('button[type="submit"]').html('Reset Password');
                    }
                });
            });

        });

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