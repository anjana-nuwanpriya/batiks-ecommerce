<div class="footer-contact">
    <div class="row g-4">
        <!-- Location -->
        <div class="col-lg-4 col-md-12">
            <a href="{{ get_setting('google_map_link') }}" target="_blank" class="text-decoration-none">
                <div class="contact-item">
                    <div class="icon-wrapper">
                        <i class="las la-map-marker"></i>
                    </div>
                    <div class="contact-info">
                        <h5>OUR LOCATION</h5>
                        <p>{{ get_setting('address') }}</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Phone -->
        <div class="col-lg-2 col-md-6 col-sm-12">
            <div class="contact-item">
                <div class="icon-wrapper">
                    <i class="las la-phone"></i>
                </div>
                <div class="contact-info">
                    <h5>CALL US 24/7</h5>
                    <a href="tel:{{ get_setting('phone') }}"  class="contact-link">{{ get_setting('phone') }}</a>
                    <a href="tel:{{ get_setting('secondary_phone') }}"  class="contact-link">{{ get_setting('secondary_phone') }}</a>
                </div>
            </div>
        </div>

        @if(!empty(get_setting('whatsapp')))
        @php
            $raw_number = get_setting('whatsapp');
            $sanitized_number = preg_replace('/\D+/', '', $raw_number);
            $message = urlencode("Hello, I'm interested in a product from batiks.lk. Could you please assist me?");
            $whatsapp_url = "https://wa.me/{$sanitized_number}?text={$message}";

        @endphp
        <!-- whatsapp -->
        <div class="col-lg-2 col-md-6 col-sm-12">
            <div class="contact-item">
                <div class="icon-wrapper">
                    <i class="lab la-whatsapp"></i>
                </div>
                <div class="contact-info">
                    <h5>WHATSAPP</h5>
                    <a href="{{ $whatsapp_url }}" target="_blank" class="contact-link">{{ get_setting('whatsapp') }}</a>
                </div>
            </div>
        </div>
        @endif
        <!-- Newsletter -->
        <div class="col-lg-4 col-md-12">
            <div class="contact-item">
                <div class="icon-wrapper">
                    <i class="las la-envelope"></i>
                </div>
                <div class="contact-info">
                    <h5>SUBSCRIBE NEWSLETTER</h5>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control" placeholder="Enter your email">
                            <button class="btn btn-subscribe" type="submit">
                                <span class="d-none d-sm-inline">Subscribe</span>
                                <span class="d-inline d-sm-none">
                                    <i class="las la-paper-plane"></i>
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>