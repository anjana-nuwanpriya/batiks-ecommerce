@extends('frontend.layouts.app')
@section('title', 'Checkout | ' . config('app.name'))
@section('content')

    @php
        $breadcrumbs = [['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Checkout']];
    @endphp

    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="$breadcrumbs" />
    <section class="checkout-section py-5">
        <div class="container">
            <form action="{{ route('checkout.process') }}" method="POST" id="checkoutForm">
                @csrf
                <div class="row">
                    <!-- Billing Information Form -->
                    <div class="col-lg-8">
                        @if (!Auth::check())
                            <div class="card mb-4">
                                <div class="card-body">
                                    @guest
                                        <h5 class="mb-4 border-bottom pb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="#006400" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                                <circle cx="12" cy="7" r="4"></circle>
                                            </svg>
                                            Account Details
                                        </h5>
                                        <div class="row mb-4">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="name" class="form-label"> <i class="la la-user"></i> Name
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="name" name="name"
                                                        placeholder="Eg: John Smith" value="{{ old('name') }}">
                                                    @error('name')
                                                        <span
                                                            class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="phone" class="form-label"> <i class="la la-phone"></i> Phone
                                                        <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="tel" class="form-control" id="phone" name="phone"
                                                            placeholder="7XXXXXXX" value="{{ old('phone') }}">
                                                        <button type="button" class="btn btn-outline-success"
                                                            id="verifyPhoneBtn" onclick="initiatePhoneVerification()">
                                                            <i class="las la-shield-alt me-1"></i> Verify
                                                        </button>
                                                    </div>
                                                    <div id="phoneVerificationStatus" class="mt-2" style="display: none;">
                                                        <small class="text-success">
                                                            <i class="las la-check-circle me-1"></i> Phone number verified
                                                        </small>
                                                    </div>
                                                    @error('phone')
                                                        <span
                                                            class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label"> <i class="la la-envelope"></i>
                                                        Email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="email" name="email"
                                                        placeholder="Eg: johnsmith@gmail.com" value="{{ old('email') }}">
                                                    @error('email')
                                                        <span
                                                            class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-5">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="createAccount"
                                                    name="create_account" {{ old('create_account') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="createAccount">
                                                    Create an account for faster checkout next time
                                                </label>
                                            </div>
                                            <div class="create-account-fields mt-3" style="display: none;">
                                                <small class="mb-2 d-block text-muted"><i>
                                                        Create an account by entering the information below. If you are a
                                                        returning customer please login from the top of the page.
                                                    </i></small>
                                                <div class="row">
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label for="password" class="form-label">Password <span
                                                                    class="text-danger">*</span></label>
                                                            <input type="password" class="form-control" id="password"
                                                                name="password">
                                                            @error('password')
                                                                <span
                                                                    class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-md-6">
                                                        <div class="mb-3">
                                                            <label for="password_confirmation" class="form-label">Confirm
                                                                Password <span class="text-danger">*</span></label>
                                                            <input type="password" class="form-control"
                                                                id="password_confirmation" name="password_confirmation">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endguest
                                </div>
                            </div>
                        @else
                            @if (empty(Auth::user()->phone_verified_at))
                                <!-- Phone Verification for Logged-in Users -->
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="mb-4 border-bottom pb-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="#006400" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round" class="me-2">
                                                <path
                                                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z">
                                                </path>
                                            </svg>
                                            Phone Verification Required
                                        </h5>
                                        <div class="alert alert-info">
                                            <i class="las la-info-circle me-2"></i>
                                            Please verify your phone number to proceed with checkout.
                                        </div>
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <label for="userPhone" class="form-label"> <i
                                                            class="la la-phone"></i>
                                                        Phone Number <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <input type="tel" class="form-control" id="userPhone"
                                                            name="user_phone" placeholder="7XXXXXXX"
                                                            value="{{ Auth::user()->phone }}">
                                                        <button type="button" class="btn btn-outline-success"
                                                            id="verifyUserPhoneBtn"
                                                            onclick="initiateUserPhoneVerification()">
                                                            <i class="las la-shield-alt me-1"></i> Verify
                                                        </button>
                                                    </div>
                                                    <div id="userPhoneVerificationStatus" class="mt-2"
                                                        style="display: none;">
                                                        <small class="text-success">
                                                            <i class="las la-check-circle me-1"></i> Phone number verified
                                                        </small>
                                                    </div>
                                                    <small class="text-muted">We'll send you a verification code via
                                                        SMS</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Hidden input for verified phone number -->
                                <input type="hidden" name="user_phone" value="{{ Auth::user()->phone }}">
                            @endif
                        @endif

                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4 border-bottom pb-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="darkgreen" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                        <circle cx="12" cy="10" r="3"></circle>
                                    </svg>
                                    Shipping Information
                                </h5>
                                <div class="row">
                                    @guest
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="address" class="form-label">Address <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter your full address">{{ old('address') }}</textarea>
                                                @error('address')
                                                    <span
                                                        class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="city" class="form-label">City <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control city-autocomplete" id="city" name="city"
                                                    value="{{ old('city') }}" autocomplete="off" placeholder="Start typing city name...">
                                                <div class="city-suggestions" id="citySuggestions" style="display: none;"></div>
                                                @error('city')
                                                    <span
                                                        class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="state" class="form-label">Province / State <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="state" name="state"
                                                    value="{{ old('state') }}">
                                                @error('state')
                                                    <span
                                                        class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="zip_code" class="form-label">Zip/Postal Code <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="zip_code" name="zip_code"
                                                    value="{{ old('zip_code') }}">
                                                @error('zip_code')
                                                    <span
                                                        class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="country" class="form-label">Country <span
                                                        class="text-danger">*</span></label>
                                                <select class="form-select" id="country" name="country">
                                                    <option value="">Select Country</option>
                                                    <option value="Sri Lanka" selected>Sri Lanka</option>
                                                </select>
                                                @error('country')
                                                    <span
                                                        class="text-danger field-notice font-12 py-2 d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @else
                                        @foreach ($addresses as $key => $address)
                                            <div class="col-12">
                                                <div class="address-card mb-3">
                                                    <input type="radio" name="address_id" value="{{ $address->id }}"
                                                        id="address-{{ $address->id }}"
                                                        onchange="updateShippingCost({{ $address->id }})"
                                                        {{ $address->is_default || $key == 0 ? 'checked' : '' }}>
                                                    <label for="address-{{ $address->id }}">
                                                        <div class="card">
                                                            <div class="card-body">
                                                                <div class="d-flex  align-items-center flex-wrap gap-2">
                                                                    <h6 class="card-title fw-bold">{{ Auth::user()->name }}
                                                                    </h6>
                                                                    <span id="address-badge-{{ $address->id }}"
                                                                        class="selected-address-badge {{ $address->is_default || $key == 0 ? 'd-block' : 'd-none' }}"><i
                                                                            class="la la-check me-1"></i> Selected</span>
                                                                </div>
                                                                <p class="mb-0">{{ $address->phone }}</p>
                                                                <p class="mb-0">{{ $address->address }}</p>
                                                                <p class="mb-0">{{ $address->city }}, {{ $address->state }}
                                                                    {{ $address->zip_code }}, {{ $address->country }}</p>
                                                            </div>

                                                            <div class="card-action">
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-secondary"
                                                                    onclick="editAddress({{ $address->id }})">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z" />
                                                                    </svg>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger"
                                                                    onclick="deleteAddress({{ $address->id }})">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                                        height="16" fill="currentColor"
                                                                        viewBox="0 0 16 16">
                                                                        <path
                                                                            d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                                                        <path fill-rule="evenodd"
                                                                            d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                        @error('address_id')
                                            <span class="text-danger field-notice mt-3 fs-12 bg-danger text-white p-2 rounded"
                                                rel="address_id">{{ $message }}</span>
                                        @enderror
                                        <div class="col-12 mb-3 mt-3">
                                            <button class="btn btn-outline-secondary w-100 text-dark" type="button"
                                                data-bs-toggle="modal" data-bs-target="#addAddressModal"> <i
                                                    class="la la-plus"></i> Add New Address</button>
                                        </div>
                                    @endguest
                                </div>
                            </div>
                        </div>

                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-3 border-bottom pb-3">Additional Info</h5>
                                <label for="orderNotes" class="form-label">Order Notes (Optional)</label>
                                <textarea class="form-control" id="orderNotes" name="order_notes" rows="4"
                                    placeholder="Notes about your order, e.g. special notes for delivery"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="mb-4">Order Summary</h5>

                                <!-- Order Items -->
                                <div class="order-items">
                                    @foreach ($cartItems as $cartItem)
                                        <div class="order-item d-flex align-items-center mb-3">
                                            @if ($cartItem['product']->thumbnail)
                                                <img src="{{ asset($cartItem['product']->thumbnail) }}"
                                                    alt="{{ $cartItem['product']->name }}" class="img-fluid rounded"
                                                    width="60">
                                            @else
                                                <img src="{{ asset('assets/default.jpg') }}"
                                                    alt="{{ $cartItem['product']->name }}" class="img-fluid rounded"
                                                    width="60">
                                            @endif
                                            <div class="ms-3 flex-grow-1">
                                                <h6 class="mb-2">{{ $cartItem['product']->name }}
                                                    @if ($cartItem['variant'] != 'Standard')
                                                        <small> - {{ $cartItem['variant'] }}</small>
                                                    @endif
                                                </h6>
                                                <div class="d-flex align-items-center">
                                                    <span class="text-muted">{{ $cartItem['quantity'] }}x </span>
                                                    <span class="text-muted item-total ms-1">
                                                        {{ formatCurrency($cartItem['price']) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Order Totals -->
                                <div class="order-totals mt-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal:</span>
                                        <span id="subtotal"> {{ formatCurrency(getCartTotalAmount()) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping:
                                            @if ($shippingCost > 0 && $shippingWeightKg > 0)
                                                <small class="text-muted">({{ number_format($shippingWeightKg, 2) }}
                                                    kg)</small>
                                            @endif
                                        </span>
                                        <span class="text-success" id="shipping-cost" data-cost="{{ $shippingCost }}">
                                            @if ($shippingCost == 0)
                                                <span class="badge bg-success">Free</span>
                                            @else
                                                {{ formatCurrency($shippingCost) }}
                                            @endif
                                        </span>
                                    </div>
                                    <div id="payhereFee" style="display: none;">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Convenience Fee:</span>
                                            <span class="text-success">Rs. <span id="payhereFeeAmount">0.00</span></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-4">
                                        <strong>Total:</strong>
                                        <strong id="total">{{ formatCurrency(getCartTotalAmount()) }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods -->
                        <div class="card">
                            <div class="card-body">
                                <div class="payment-methods mb-4">
                                    <h5 class="mb-3"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="me-2">
                                            <rect x="1" y="4" width="22" height="16" rx="2"
                                                ry="2"></rect>
                                            <line x1="1" y1="10" x2="23" y2="10"></line>
                                        </svg> Payment Method</h5>
                                    <div class="form-check mb-3 border-bottom pb-2">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="cod" value="COD"
                                            {{ old('payment_method') == 'COD' ? 'checked' : '' }}
                                            {{ !$codAvailable ? 'disabled' : '' }}>
                                        <label class="form-check-label {{ !$codAvailable ? 'text-muted' : '' }}"
                                            for="cod">
                                            <i class="las la-truck me-1"></i> Cash on Delivery
                                            @if ($codAvailable)
                                                <small class="text-muted d-block">Pay when you receive your order</small>
                                            @else
                                                <small class="text-danger d-block">
                                                    <i class="las la-exclamation-triangle me-1"></i>
                                                    Not available due to order amount or weight restrictions
                                                </small>
                                            @endif
                                        </label>
                                    </div>
                                    <div class="form-check mb-3 border-bottom pb-2">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="payhere" value="payhere"
                                            {{ old('payment_method') == 'payhere' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="payhere">
                                            <i class="las la-credit-card me-1"></i> Payhere (Online Payment)
                                            <small class="text-muted d-block">Pay with your credit/debit card</small>
                                            <div class="mt-2">
                                                <img src="{{ asset('assets/payhere_long_banner.png') }}"
                                                    alt="Payhere Payment Methods" class="img-fluid"
                                                    style="max-width: 300px;">
                                            </div>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method"
                                            id="bank_transfer" value="bank_transfer"
                                            {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="bank_transfer">
                                            <i class="las la-money-bill me-1"></i> Bank Transfer

                                            <div class="bank-info mt-2 ps-4" style="font-size: 0.9em;">
                                                <p class="mb-1"><strong>Bank:</strong> {{ env('BANK_NAME') }}</p>
                                                <p class="mb-1"><strong>Account Name:</strong>
                                                    {{ env('BANK_ACCOUNT_NAME') }}</p>
                                                <p class="mb-1"><strong>Account Number:</strong>
                                                    {{ env('BANK_ACCOUNT_NUMBER') }}</p>
                                                <p class="mb-1"><strong>Branch:</strong> {{ env('BANK_BRANCH_NAME') }}
                                                </p>
                                                <p class="mb-1"><strong>Swift Code:</strong>
                                                    {{ env('BANK_SWIFT_CODE') }}</p>
                                                <p class="mb-1 text-muted small">{{ env('BANK_NOTE') }}</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                @error('payment_method')
                                    <span class="text-danger field-notice my-3 d-block fs-12 bg-danger text-white p-2 rounded"
                                        rel="payment_method">{{ $message }}</span>
                                @enderror

                                <!-- Terms and Conditions -->
                                <div class="terms-and-conditions mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="terms_accepted"
                                            id="termsAccepted" {{ old('terms_accepted') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="termsAccepted">
                                            I have read and agree to the <a href="{{ route('terms') }}" target="_blank">
                                                <u>Terms and Conditions</u></a> & <a href="{{ route('return-policy') }}"
                                                target="_blank"> <u>Return Policy</u></a>
                                        </label>
                                    </div>
                                </div>

                                @error('terms_accepted')
                                    <span class="text-danger field-notice mt-3 fs-12 bg-danger text-white p-2 rounded"
                                        rel="terms_accepted">{{ $message }}</span>
                                @enderror

                                <div class="mt-4">
                                    <button type="submit" class="btn btn-success w-100" id="checkoutFormBtn">Place
                                        Order</button>
                                </div>

                                <small class="text-muted text-center d-block mt-3">Your order will be processed securely.
                                    You will receive a confirmation email shortly.</small>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>

@endsection

@section('modals')
    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addAddressModalLabel">Add New Address</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store.address') }}" method="post" class="ajax-form" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="">Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                            <span class="text-danger field-notice" rel="address"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control city-autocomplete" name="city" id="modalCity" autocomplete="off" placeholder="Start typing city name...">
                            <div class="city-suggestions" id="modalCitySuggestions" style="display: none;"></div>
                            <span class="text-danger field-notice" rel="city"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Zip/Postal Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="zip_code">
                            <span class="text-danger field-notice" rel="zip_code"></span>
                        </div>

                        <div class="mb-3">
                            <label for="state">Province / State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="state" id="state"
                                list="provinceList">

                            <datalist id="provinceList">
                                <option value="Central">
                                <option value="Eastern">
                                <option value="Northern">
                                <option value="North Central">
                                <option value="North Western">
                                <option value="Sabaragamuwa">
                                <option value="Southern">
                                <option value="Uva">
                                <option value="Western">
                            </datalist>

                            <span class="text-danger field-notice" rel="state"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Country <span class="text-danger">*</span></label>
                            <select class="form-select" name="country">
                                <option value="Sri Lanka">Sri Lanka</option>
                            </select>
                            <span class="text-danger field-notice" rel="country"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" placeholder="947XXXXXX">
                            <span class="text-danger field-notice" rel="phone"></span>
                        </div>


                        <div class="mb-3">
                            <button type="submit" class="btn btn-success w-100">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" id="editAddressModalContent">
        </div>
    </div>

    <!-- Phone Verification Modal -->
    <div class="modal fade" id="phoneVerificationModal" tabindex="-1" aria-labelledby="phoneVerificationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="phoneVerificationModalLabel">
                        <i class="las la-shield-alt me-2"></i>Verify Phone Number
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="otpSendStep">
                        <p class="mb-3">We'll send a verification code to your phone number to ensure it's valid.</p>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="verificationPhoneInput" readonly>
                        </div>
                        <button type="button" class="btn btn-success w-100" id="sendOtpBtn" onclick="sendOTP()">
                            <i class="las la-paper-plane me-1"></i> Send Verification Code
                        </button>
                    </div>

                    <div id="otpVerifyStep" style="display: none;">
                        <div class="text-center mb-4">
                            <i class="las la-mobile-alt text-success" style="font-size: 3rem;"></i>
                            <h6 class="mt-2">Enter Verification Code</h6>
                            <p class="text-muted small">We've sent a 6-digit code to <span
                                    id="verificationPhoneDisplay"></span></p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Verification Code</label>
                            <input type="text" class="form-control text-center" id="otpCodeInput"
                                placeholder="000000" maxlength="6" style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Code expires in: <span id="otpTimer"
                                        class="text-danger fw-bold">2:00</span></small>
                                <button type="button" class="btn btn-link btn-sm p-0" id="resendOtpBtn"
                                    onclick="sendOTP()" disabled>
                                    Resend Code
                                </button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-success w-100" id="verifyOtpBtn" onclick="verifyOTP()">
                            <i class="las la-check me-1"></i> Verify Code
                        </button>

                        <button type="button" class="btn btn-outline-secondary w-100 mt-2" onclick="backToPhoneStep()">
                            <i class="las la-arrow-left me-1"></i> Change Phone Number
                        </button>
                    </div>

                    <div id="verificationSuccessStep" style="display: none;">
                        <div class="text-center">
                            <i class="las la-check-circle text-success" style="font-size: 4rem;"></i>
                            <h5 class="text-success mt-3">Phone Verified Successfully!</h5>
                            <p class="text-muted">Your phone number has been verified and is ready for checkout.</p>
                            <button type="button" class="btn btn-success" data-bs-dismiss="modal">
                                Continue to Checkout
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
<style>
.city-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 4px 4px;
    max-height: 200px;
    overflow-y: auto;
    z-index: 1000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.city-suggestion-item {
    padding: 10px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s;
}

.city-suggestion-item:hover,
.city-suggestion-item.active {
    background-color: #f8f9fa;
}

.city-suggestion-item:last-child {
    border-bottom: none;
}

.city-autocomplete {
    position: relative;
}

.mb-3 {
    position: relative;
}
</style>
@endsection

@section('scripts')
    <script>
        // Phone Verification Variables
        let otpTimer;
        let isPhoneVerified = false; // For guest users
        let isUserPhoneVerified =
            {{ Auth::check() && !empty(Auth::user()->phone_verified_at) ? 'true' : 'false' }}; // For authenticated users

        // Function to check phone verification status on page load
        function checkPhoneVerificationOnLoad(phone, isUserPhone = false) {
            if (!phone) return;

            // Check localStorage first for quick response
            const storageKey = isUserPhone ? 'verified_user_phone' : 'verified_phone';
            const verifiedPhone = localStorage.getItem(storageKey);

            if (verifiedPhone === phone) {
                // Phone is verified in localStorage, update UI immediately
                if (isUserPhone) {
                    isUserPhoneVerified = true;
                    updateUserPhoneVerificationStatus(true);
                } else {
                    isPhoneVerified = true;
                    updatePhoneVerificationStatus(true);
                }
                return;
            }

            // Check with server for verification status
            fetch('{{ route('phone.check.verification') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone: phone
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.verified) {
                        // Store in localStorage and update UI
                        localStorage.setItem(storageKey, phone);

                        if (isUserPhone) {
                            isUserPhoneVerified = true;
                            updateUserPhoneVerificationStatus(true);
                        } else {
                            isPhoneVerified = true;
                            updatePhoneVerificationStatus(true);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error checking phone verification:', error);
                });
        }

        // Function to clear verification status when phone number changes
        function clearPhoneVerificationStatus(isUserPhone = false) {
            const storageKey = isUserPhone ? 'verified_user_phone' : 'verified_phone';
            localStorage.removeItem(storageKey);

            if (isUserPhone) {
                isUserPhoneVerified = false;
                updateUserPhoneVerificationStatus(false);
            } else {
                isPhoneVerified = false;
                updatePhoneVerificationStatus(false);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize phone verification status on page load
            setTimeout(function() {
                    @guest
                    const guestPhone = document.getElementById('phone');
                    if (guestPhone && guestPhone.value) {
                        checkPhoneVerificationOnLoad(guestPhone.value, false);

                        // Add event listener to clear verification when phone changes
                        guestPhone.addEventListener('input', function() {
                            const currentPhone = this.value.trim();
                            const verifiedPhone = localStorage.getItem('verified_phone');
                            if (verifiedPhone && verifiedPhone !== currentPhone) {
                                clearPhoneVerificationStatus(false);
                            }
                        });
                    }
                @endguest

                @auth
                @if (empty(Auth::user()->phone_verified_at))
                    const userPhone = document.getElementById('userPhone');
                    if (userPhone && userPhone.value) {
                        checkPhoneVerificationOnLoad(userPhone.value, true);

                        // Add event listener to clear verification when phone changes
                        userPhone.addEventListener('input', function() {
                            const currentPhone = this.value.trim();
                            const verifiedPhone = localStorage.getItem('verified_user_phone');
                            if (verifiedPhone && verifiedPhone !== currentPhone) {
                                clearPhoneVerificationStatus(true);
                            }
                        });
                    }
                @endif
            @endauth
        }, 500);

        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        const payhereFee = document.getElementById('payhereFee');
        const payhereFeeAmount = document.getElementById('payhereFeeAmount');
        const totalElement = document.getElementById('total');
        const subtotal = {{ getCartTotalAmount() }};
        const shippingCost = document.getElementById('shipping-cost');
        const shippingCostValue = parseFloat(shippingCost.getAttribute('data-cost'));
        const convenienceFee = {{ env('PAYHERE_CONVENIENCE_FEE', 0) }};

        function updateTotal() {
            let total = subtotal + shippingCostValue;
            let feeAmount = 0;
            payhereFee.style.display = 'none';
            if (document.getElementById('payhere').checked) {
                feeAmount = (total * convenienceFee) / 100;
                payhereFee.style.display = 'block';
                payhereFeeAmount.textContent = feeAmount.toFixed(2);
                total += feeAmount;
            }

            totalElement.textContent = 'Rs ' + total.toFixed(2);
        }

        paymentMethods.forEach(method => {
            method.addEventListener('change', updateTotal);
        });

        updateTotal();



        @guest
        const createAccountCheckbox = document.getElementById('createAccount');
        const fields = document.querySelector('.create-account-fields');

        // Show/hide fields on change
        createAccountCheckbox.addEventListener('change', function() {
            fields.style.display = this.checked ? 'block' : 'none';
        });

        // Check initial state on load
        if (createAccountCheckbox.checked) {
            fields.style.display = 'block';
        }
        @endguest
        });

        document.getElementById('checkoutFormBtn').addEventListener('click', function(e) {
            e.preventDefault();

            const form = document.getElementById('checkoutForm');
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;

            // Check phone verification based on user authentication status
            @auth
            // For authenticated users, check if phone verification is required
            @if (empty(Auth::user()->phone_verified_at))
                if (!isUserPhoneVerified) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Phone Verification Required',
                        text: 'Please verify your phone number before proceeding with checkout.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    });
                    return;
                }
            @endif
            // If user already has verified phone, no need to check verification
        @else
            // For guest users, always require phone verification
            if (!isPhoneVerified) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Phone Verification Required',
                    text: 'Please verify your phone number before proceeding with checkout.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
                return;
            }
        @endauth

        submitBtn.disabled = true; submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';

        form.submit();
        });

        // Phone Verification Functions

        // Check verification status from localStorage on page load
        function initializePhoneVerificationStatus() {
            @guest
            const guestPhone = document.getElementById('phone')?.value;
            if (guestPhone) {
                const verifiedPhone = localStorage.getItem('verified_phone');
                if (verifiedPhone === guestPhone) {
                    isPhoneVerified = true;
                    updatePhoneVerificationStatus(true);
                } else {
                    // Check with server
                    checkPhoneVerificationOnLoad(guestPhone, false);
                }
            }
        @endguest

        @auth
        // If user already has verified phone in database, no need to verify again
        @if (!empty(Auth::user()->phone_verified_at))
            isUserPhoneVerified = true;
            // No need to show verification UI for already verified users
        @else
            const userPhone = document.getElementById('userPhone')?.value;
            if (userPhone) {
                const verifiedUserPhone = localStorage.getItem('verified_user_phone');
                if (verifiedUserPhone === userPhone) {
                    isUserPhoneVerified = true;
                    updateUserPhoneVerificationStatus(true);
                } else {
                    // Check with server
                    checkPhoneVerificationOnLoad(userPhone, true);
                }
            }
        @endif
        @endauth
        }

        // Initialize verification status on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(initializePhoneVerificationStatus, 100);
            initializeCityAutocomplete();
        });

        // City Autocomplete Functionality
        function initializeCityAutocomplete() {
            // Initialize main city input
            setupCityAutocomplete('city', 'citySuggestions');

            // Initialize modal city input
            setupCityAutocomplete('modalCity', 'modalCitySuggestions');
        }

        function setupCityAutocomplete(inputId, suggestionsId) {
            const cityInput = document.getElementById(inputId);
            const suggestionsDiv = document.getElementById(suggestionsId);
            let debounceTimer;
            let currentFocus = -1;
            let suggestions = [];

            if (!cityInput || !suggestionsDiv) return;

            cityInput.addEventListener('input', function() {
                const query = this.value.trim();

                clearTimeout(debounceTimer);

                if (query.length < 2) {
                    hideSuggestions();
                    return;
                }

                debounceTimer = setTimeout(() => {
                    searchCities(query);
                }, 300);
            });

            cityInput.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    currentFocus++;
                    addActive();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    currentFocus--;
                    addActive();
                } else if (e.key === 'Enter') {
                    e.preventDefault();
                    if (currentFocus > -1 && suggestions[currentFocus]) {
                        selectCity(suggestions[currentFocus]);
                    }
                } else if (e.key === 'Escape') {
                    hideSuggestions();
                }
            });

            cityInput.addEventListener('blur', function() {
                // Delay hiding to allow click on suggestions
                setTimeout(() => {
                    hideSuggestions();
                }, 150);
            });

            function searchCities(query) {
                fetch(`{{ route('api.cities.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions = data;
                        displaySuggestions(data);
                    })
                    .catch(error => {
                        console.error('City search error:', error);
                        hideSuggestions();
                    });
            }

            function displaySuggestions(cities) {
                if (!cities || cities.length === 0) {
                    hideSuggestions();
                    return;
                }

                let html = '';
                cities.forEach((city, index) => {
                    html += `<div class="city-suggestion-item" data-index="${index}" onclick="${`selectCityByIndex_${inputId}`}(${index})">${city.label}</div>`;
                });

                suggestionsDiv.innerHTML = html;
                suggestionsDiv.style.display = 'block';
                currentFocus = -1;
            }

            function selectCity(city) {
                cityInput.value = city.value;
                hideSuggestions();
                currentFocus = -1;
            }

            function hideSuggestions() {
                suggestionsDiv.style.display = 'none';
                suggestionsDiv.innerHTML = '';
                currentFocus = -1;
            }

            function addActive() {
                const items = suggestionsDiv.querySelectorAll('.city-suggestion-item');

                // Remove active class from all items
                items.forEach(item => item.classList.remove('active'));

                // Handle bounds
                if (currentFocus >= items.length) currentFocus = 0;
                if (currentFocus < 0) currentFocus = items.length - 1;

                // Add active class to current item
                if (items[currentFocus]) {
                    items[currentFocus].classList.add('active');
                }
            }

            // Create unique global function for this input
            window[`selectCityByIndex_${inputId}`] = function(index) {
                if (suggestions[index]) {
                    selectCity(suggestions[index]);
                }
            };
        }

        function initiatePhoneVerification() {
            const phoneInput = document.getElementById('phone');
            const phone = phoneInput.value.trim();

            if (!phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Phone Number Required',
                    text: 'Please enter your phone number first.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    phoneInput.focus();
                });
                return;
            }

            // Set phone number in modal
            document.getElementById('verificationPhoneInput').value = phone;
            document.getElementById('verificationPhoneDisplay').textContent = phone;

            // Reset modal to first step
            resetVerificationModal();

            // Show modal using jQuery (more compatible)
            $('#phoneVerificationModal').modal('show');
        }

        // Phone verification for logged-in users
        function initiateUserPhoneVerification() {
            const phoneInput = document.getElementById('userPhone');
            const phone = phoneInput.value.trim();

            if (!phone) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Phone Number Required',
                    text: 'Please enter your phone number first.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    phoneInput.focus();
                });
                return;
            }

            // Set phone number in modal
            document.getElementById('verificationPhoneInput').value = phone;
            document.getElementById('verificationPhoneDisplay').textContent = phone;

            // Reset modal to first step
            resetVerificationModal();

            // Show modal using jQuery (more compatible)
            $('#phoneVerificationModal').modal('show');
        }

        function resetVerificationModal() {
            document.getElementById('otpSendStep').style.display = 'block';
            document.getElementById('otpVerifyStep').style.display = 'none';
            document.getElementById('verificationSuccessStep').style.display = 'none';
            document.getElementById('otpCodeInput').value = '';
            clearInterval(otpTimer);
        }

        function sendOTP() {
            const phone = document.getElementById('verificationPhoneInput').value;
            const sendBtn = document.getElementById('sendOtpBtn');
            const resendBtn = document.getElementById('resendOtpBtn');

            // Disable buttons
            sendBtn.disabled = true;
            resendBtn.disabled = true;
            sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

            fetch('{{ route('phone.send.otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone: phone
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Switch to OTP verification step
                        document.getElementById('otpSendStep').style.display = 'none';
                        document.getElementById('otpVerifyStep').style.display = 'block';

                        // Start countdown timer
                        startOTPTimer(120); // 2 minutes

                        // Focus on OTP input
                        document.getElementById('otpCodeInput').focus();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Failed to Send OTP',
                            text: data.message || 'Failed to send OTP. Please try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    });
                })
                .finally(() => {
                    // Re-enable buttons
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="las la-paper-plane me-1"></i> Send Verification Code';
                });
        }

        function verifyOTP() {
            const phone = document.getElementById('verificationPhoneInput').value;
            const otpCode = document.getElementById('otpCodeInput').value.trim();
            const verifyBtn = document.getElementById('verifyOtpBtn');

            if (!otpCode || otpCode.length !== 6) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Invalid Code',
                    text: 'Please enter the 6-digit verification code.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#28a745'
                });
                return;
            }

            // Disable button
            verifyBtn.disabled = true;
            verifyBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';

            fetch('{{ route('phone.verify.otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone: phone,
                        otp_code: otpCode
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Clear timer
                        clearInterval(otpTimer);

                        // Show success step
                        document.getElementById('otpVerifyStep').style.display = 'none';
                        document.getElementById('verificationSuccessStep').style.display = 'block';

                        // Mark phone as verified
                        isPhoneVerified = true;

                        // Check if this is for guest or logged-in user
                        const phoneInput = document.getElementById('phone');
                        const userPhoneInput = document.getElementById('userPhone');

                        if (phoneInput && phoneInput.value === phone) {
                            updatePhoneVerificationStatus(true);
                            localStorage.setItem('verified_phone', phone);
                        } else if (userPhoneInput && userPhoneInput.value === phone) {
                            updateUserPhoneVerificationStatus(true);
                            localStorage.setItem('verified_user_phone', phone);
                        }

                        // Auto close modal after 2 seconds
                        setTimeout(() => {
                            $('#phoneVerificationModal').modal('hide');
                        }, 2000);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Code',
                            text: data.message || 'Invalid verification code. Please try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            document.getElementById('otpCodeInput').value = '';
                            document.getElementById('otpCodeInput').focus();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred. Please try again.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#28a745'
                    });
                })
                .finally(() => {
                    // Re-enable button
                    verifyBtn.disabled = false;
                    verifyBtn.innerHTML = '<i class="las la-check me-1"></i> Verify Code';
                });
        }

        function backToPhoneStep() {
            clearInterval(otpTimer);
            document.getElementById('otpVerifyStep').style.display = 'none';
            document.getElementById('otpSendStep').style.display = 'block';
            document.getElementById('otpCodeInput').value = '';
        }

        function startOTPTimer(seconds) {
            const timerElement = document.getElementById('otpTimer');
            const resendBtn = document.getElementById('resendOtpBtn');

            let timeLeft = seconds;

            otpTimer = setInterval(() => {
                const minutes = Math.floor(timeLeft / 60);
                const secs = timeLeft % 60;
                timerElement.textContent = `${minutes}:${secs.toString().padStart(2, '0')}`;

                if (timeLeft <= 0) {
                    clearInterval(otpTimer);
                    timerElement.textContent = 'Expired';
                    timerElement.classList.remove('text-danger');
                    timerElement.classList.add('text-muted');
                    resendBtn.disabled = false;
                    resendBtn.textContent = 'Resend Code';
                }

                timeLeft--;
            }, 1000);
        }

        function updatePhoneVerificationStatus(verified) {
            const statusDiv = document.getElementById('phoneVerificationStatus');
            const verifyBtn = document.getElementById('verifyPhoneBtn');

            if (verified) {
                statusDiv.style.display = 'block';
                verifyBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Verified';
                verifyBtn.classList.remove('btn-outline-success');
                verifyBtn.classList.add('btn-success');
                verifyBtn.disabled = true;
            } else {
                statusDiv.style.display = 'none';
                verifyBtn.innerHTML = '<i class="las la-shield-alt me-1"></i> Verify';
                verifyBtn.classList.remove('btn-success');
                verifyBtn.classList.add('btn-outline-success');
                verifyBtn.disabled = false;
            }
        }

        // Update phone verification status for logged-in users
        function updateUserPhoneVerificationStatus(verified) {
            const statusDiv = document.getElementById('userPhoneVerificationStatus');
            const verifyBtn = document.getElementById('verifyUserPhoneBtn');

            if (verified) {
                statusDiv.style.display = 'block';
                verifyBtn.innerHTML = '<i class="las la-check-circle me-1"></i> Verified';
                verifyBtn.classList.remove('btn-outline-success');
                verifyBtn.classList.add('btn-success');
                verifyBtn.disabled = true;
                isUserPhoneVerified = true;
            } else {
                statusDiv.style.display = 'none';
                verifyBtn.innerHTML = '<i class="las la-shield-alt me-1"></i> Verify';
                verifyBtn.classList.remove('btn-success');
                verifyBtn.classList.add('btn-outline-success');
                verifyBtn.disabled = false;
                isUserPhoneVerified = false;
            }
        }



        // Auto-format OTP input
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otpCodeInput');
            if (otpInput) {
                otpInput.addEventListener('input', function(e) {
                    // Only allow numbers
                    this.value = this.value.replace(/[^0-9]/g, '');

                    // Auto-verify when 6 digits are entered
                    if (this.value.length === 6) {
                        setTimeout(() => verifyOTP(), 500);
                    }
                });
            }
        });
    </script>
@endsection
