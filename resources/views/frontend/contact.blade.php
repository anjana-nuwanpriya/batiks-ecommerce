@extends('frontend.layouts.app')
@section('content')
    @php
        $breadcrumbs = [['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Contact Us']];
    @endphp
    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="$breadcrumbs" />

    @php
        $address = get_setting('address');
        $address_one = get_setting('other_address_one');
        $address_two = get_setting('other_address_two');

    @endphp

    <div class="container py-5">
        <div class="row g-4">
            <!-- Store Information Column -->
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-4">
                        <h2 class="card-title fw-bold mb-4 contact-title">Store Information</h2>

                        <div class="store-info">
                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="las la-map-marker-alt"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Address</h6>
                                    <p>{{ $address }}</p>
                                    @if (!empty($address_one))
                                        <hr>
                                        <p>{{ $address_one }}</p>
                                    @endif
                                    @if (!empty($address_two))
                                        <hr>
                                        <p>{{ $address_two }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="las la-phone"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Contact</h6>
                                    <p>
                                        <a href="tel:{{ get_setting('phone') }}" class="contact-link">
                                            <i class="las la-phone me-1"></i> {{ get_setting('phone') }}
                                        </a>
                                        <a href="tel:{{ get_setting('secondary_phone') }}" class="contact-link">
                                            <i class="las la-phone me-1"></i> {{ get_setting('secondary_phone') }}
                                        </a>
                                    </p>
                                    @if (!empty(get_setting('whatsapp')))
                                        @php
                                            $raw_number = get_setting('whatsapp');
                                            $sanitized_number = preg_replace('/\D+/', '', $raw_number);
                                            $message = urlencode(
                                                "Hello, I'm interested in a product from Nature's Virtue. Could you please assist me?",
                                            );

                                            $whatsapp_url = "https://wa.me/{$sanitized_number}?text={$message}";
                                        @endphp
                                        <p>
                                            <a href="{{ $whatsapp_url }}" class="contact-link" target="_blank">
                                                <i class="lab la-whatsapp me-1"></i> {{ get_setting('whatsapp') }}
                                            </a>
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-icon">
                                    <i class="las la-envelope"></i>
                                </div>
                                <div class="info-content">
                                    <h6>Email</h6>
                                    <p>
                                        <a href="mailto:{{ get_setting('email') }}" class="contact-link">
                                            <i class="las la-envelope me-1"></i> {{ get_setting('email') }}
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="follow-us mt-5">
                            <h3 class="fw-bold contact-title">Follow Us</h3>
                            <div class="d-flex gap-3 mt-3">
                                @if (get_setting('facebook'))
                                    <a href="{{ get_setting('facebook') }}" class="social-icon" aria-label="Facebook">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M13.397 20.997v-8.196h2.765l.411-3.209h-3.176V7.548c0-.926.258-1.56 1.587-1.56h1.684V3.127A22.336 22.336 0 0 0 14.201 3c-2.444 0-4.122 1.492-4.122 4.231v2.355H7.332v3.209h2.753v8.202h3.312z" />
                                        </svg>
                                    </a>
                                @endif
                                @if (get_setting('instagram'))
                                    <a href="{{ get_setting('instagram') }}" class="social-icon" aria-label="Instagram">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M12 2c2.717 0 3.056.01 4.122.06 1.065.05 1.79.217 2.428.465.66.254 1.216.598 1.772 1.153a4.908 4.908 0 0 1 1.153 1.772c.247.637.415 1.363.465 2.428.047 1.066.06 1.405.06 4.122 0 2.717-.01 3.056-.06 4.122-.05 1.065-.218 1.79-.465 2.428a4.883 4.883 0 0 1-1.153 1.772 4.915 4.915 0 0 1-1.772 1.153c-.637.247-1.363.415-2.428.465-1.066.047-1.405.06-4.122.06-2.717 0-3.056-.01-4.122-.06-1.065-.05-1.79-.218-2.428-.465a4.89 4.89 0 0 1-1.772-1.153 4.904 4.904 0 0 1-1.153-1.772c-.248-.637-.415-1.363-.465-2.428C2.013 15.056 2 14.717 2 12c0-2.717.01-3.056.06-4.122.05-1.066.217-1.79.465-2.428a4.88 4.88 0 0 1 1.153-1.772A4.897 4.897 0 0 1 5.45 2.525c.638-.248 1.362-.415 2.428-.465C8.944 2.013 9.283 2 12 2zm0 5a5 5 0 1 0 0 10 5 5 0 0 0 0-10zm6.5-.25a1.25 1.25 0 0 0-2.5 0 1.25 1.25 0 0 0 2.5 0zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z" />
                                        </svg>
                                    </a>
                                @endif
                                @if (get_setting('linkedin'))
                                    <a href="{{ get_setting('linkedin') }}" class="social-icon" aria-label="Linkedin">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z" />
                                        </svg>
                                    </a>
                                @endif
                                @if (get_setting('tiktok'))
                                    <a href="{{ get_setting('tiktok') }}" class="social-icon" aria-label="Tiktok">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="currentColor">
                                            <path
                                                d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.298-.002.595.042.88.13V9.4a6.37 6.37 0 0 0-1-.05A6.3 6.3 0 0 0 3 15.65a6.32 6.32 0 0 0 10.87 4.33 6.41 6.41 0 0 0 1.83-4.48l-.01-7.66a8.42 8.42 0 0 0 3.91.91V5.29c-.298.016-.596-.015-.89-.09l.88.09v1.4z" />
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form and Map -->
            <div class="col-lg-7">
                <div class="row g-4 h-100">
                    <!-- Map Section -->
                    <div class="col-12">
                        <div class="map-container shadow-sm">
                            <iframe src="{!! get_setting('map_preview') !!}" width="100%" height="100%" style="border:0;"
                                allowfullscreen="" loading="lazy" title="Store Location Map">
                            </iframe>
                        </div>
                    </div>

                    <!-- Form Section -->
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-body p-4">
                                <h3 class="mb-4 contact-title">Get in Touch</h3>
                                <form class="contact-form needs-validation" novalidate id="contactForm" method="POST"
                                    action="{{ route('contact.store') }}">
                                    @csrf

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="text" name="name"
                                                    class="form-control @error('name') is-invalid @enderror" id="nameInput"
                                                    placeholder="Your Name" value="{{ old('name') }}" required>
                                                <label for="nameInput">Your Name</label>
                                                @error('name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please enter your name</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="emailInput" placeholder="Your Email" value="{{ old('email') }}"
                                                    required>
                                                <label for="emailInput">Your Email</label>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please enter a valid email address</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-floating mb-3">
                                                <input type="tel" name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    id="phoneInput" placeholder="Your Mobile Number"
                                                    value="{{ old('phone') }}" required>
                                                <label for="phoneInput">Your Mobile Number</label>
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please enter your mobile number</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <div class="form-floating mb-3">
                                                <select class="form-select @error('subject') is-invalid @enderror"
                                                    name="subject" id="subjectInput" required>
                                                    <option value="" {{ old('subject') == '' ? 'selected' : '' }}>
                                                        Choose a subject</option>
                                                    <option value="general"
                                                        {{ old('subject') == 'general' ? 'selected' : '' }}>General Inquiry
                                                    </option>
                                                    <option value="order"
                                                        {{ old('subject') == 'order' ? 'selected' : '' }}>Order Related
                                                    </option>
                                                    <option value="product"
                                                        {{ old('subject') == 'product' ? 'selected' : '' }}>Product
                                                        Information</option>
                                                    <option value="shipping"
                                                        {{ old('subject') == 'shipping' ? 'selected' : '' }}>Shipping &
                                                        Delivery</option>
                                                    <option value="feedback"
                                                        {{ old('subject') == 'feedback' ? 'selected' : '' }}>Feedback
                                                    </option>
                                                    <option value="other"
                                                        {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                                <label for="subjectInput">Subject</label>
                                                @error('subject')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @else
                                                    <div class="invalid-feedback">Please select a subject</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('message') is-invalid @enderror" name="message" id="messageInput"
                                            placeholder="Your Message" style="height: 120px" required>{{ old('message') }}</textarea>
                                        <label for="messageInput">Your Message</label>
                                        @error('message')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @else
                                            <div class="invalid-feedback">Please enter your message</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        {!! NoCaptcha::renderJs() !!}
                                        {!! NoCaptcha::display() !!}
                                        @error('g-recaptcha-response')
                                            <div class="text-danger small mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-style-1 btn-lg">
                                            <i class="las la-paper-plane me-2"></i>Send Message
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="row mt-5">
            <div class="col-12 text-center mb-4">
                <h2 class="fw-bold contact-title">Frequently Asked Questions</h2>
                <div class="mx-auto contact-underline border-bottom border-3 w-25 mb-4"></div>
            </div>

            <div class="col-lg-8 mx-auto">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item mb-3 faq-item shadow-sm">
                        <h2 class="accordion-header" id="faqOne">
                            <button class="accordion-button collapsed faq-button" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false"
                                aria-controls="collapseOne">
                                <i class="las la-shipping-fast me-2"></i> Do you offer shipping across Sri Lanka?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="faqOne"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Yes, we offer nationwide shipping across Sri Lanka. Delivery times typically range from 1-3
                                business days depending on your location.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item mb-3 faq-item shadow-sm">
                        <h2 class="accordion-header" id="faqTwo">
                            <button class="accordion-button collapsed faq-button" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false"
                                aria-controls="collapseTwo">
                                <i class="las la-credit-card me-2"></i> What payment methods do you accept in-store?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="faqTwo"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                We accept cash, credit/debit cards (Visa, Mastercard, Amex), and mobile payment options
                                including Apple Pay and Google Pay.
                            </div>
                        </div>
                    </div>

                    <div class="accordion-item faq-item shadow-sm">
                        <h2 class="accordion-header" id="faqThree">
                            <button class="accordion-button collapsed faq-button" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false"
                                aria-controls="collapseThree">
                                <i class="las la-store me-2"></i> Do you offer store pickup for online orders?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="faqThree"
                            data-bs-parent="#faqAccordion">
                            <div class="accordion-body faq-body">
                                Yes, you can select "Store Pickup" during checkout. We'll notify you when your order is
                                ready, typically within 2 hours of placing your order during store hours.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
@endpush
