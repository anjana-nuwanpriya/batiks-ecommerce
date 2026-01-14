<footer class="footer"
    style="--footer-1-bg: url('{{ asset('assets/mask-1.png') }}'); --footer-2-bg: url('{{ asset('assets/mask-2.png') }}'); --footer-3-bg: url('{{ asset('assets/mask-3.png') }}'); --footer-4-bg: url('{{ asset('assets/mask-4.png') }}');">
    <div class="container position-relative z-index-1">
        <div class="row">
            <!-- Brand Column -->
            <div class="col-lg-4 mb-4 mb-lg-0">
                <div class="footer-brand">
                    <a href="{{ route('home') }}" class="logo">
                        <img src="{{ asset('assets/logo/nvlogo2.svg') }}" class="img-fluid" width="220"
                            alt="{{ env('APP_NAME') }}">
                    </a>
                    <p class="mt-3 w-75">Your trusted partner for high-quality products. We offer a curated selection of
                        items with excellent customer service and fast shipping to meet all your shopping needs.</p>

                    <div class="footer-social">
                        <ul
                            class="d-flex align-items-center justify-content-center justify-content-lg-start list-unstyled my-4 mt-lg-0 mb-0 gap-3">
                            @if (!empty(get_setting('facebook')))
                                <li><a href="{{ get_setting('facebook') }}" target="_blank"><i
                                            class="lab la-facebook-f"></i></a></li>
                            @endif
                            @if (!empty(get_setting('instagram')))
                                <li><a href="{{ get_setting('instagram') }}" target="_blank"><i
                                            class="lab la-instagram"></i></a></li>
                            @endif
                            @if (!empty(get_setting('linkedin')))
                                <li><a href="{{ get_setting('linkedin') }}" target="_blank"><i
                                            class="lab la-linkedin-in"></i></a></li>
                            @endif
                            @if (!empty(get_setting('tiktok')))
                                <li><a href="{{ get_setting('tiktok') }}" target="_blank"><svg
                                            xmlns="http://www.w3.org/2000/svg" height="1em" viewBox="0 0 448 512"
                                            fill="#999999">
                                            <path
                                                d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z" />
                                        </svg></a></li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>

            <!-- My Account Column -->
            <div class="col-lg-2 col-md-6 mb-4 col-sm-6 mb-lg-0 mt-4 mt-lg-0">
                <div class="footer-menus">
                    <h4>My Account</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('user.dashboard') }}">My Account</a></li>
                        <li><a href="{{ route('user.order-list') }}">Order History</a></li>
                        <li><a href="{{ route('cart.index') }}">Shopping Cart</a></li>
                    </ul>
                </div>
            </div>

            <!-- Helps Column -->
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 col-sm-6 mt-4 mt-lg-0">
                <div class="footer-menus">
                    <h4>Help</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                        <li><a href="{{ route('return-policy') }}">Return Policy</a></li>
                        <li><a href="{{ route('terms') }}">Terms & Condition</a></li>
                        <li><a href="{{ route('privacy-policy') }}">Privacy Policy</a></li>
                    </ul>
                </div>
            </div>

            <!-- Proxy Column -->
            <div class="col-lg-2 col-md-6 mb-4 mb-lg-0 col-sm-6 mt-4 mt-lg-0">
                <div class="footer-menus">
                    <h4>Proxy</h4>
                    <ul class="footer-links">
                        <li><a href="{{ route('about') }}">About</a></li>
                        <li><a href="{{ route('sf.products.list') }}">Shop</a></li>
                        <li><a href="{{ route('sf.products.list') }}">Product</a></li>
                        <li>
                            <a href="{{ route('tracking.index') }}">
                                {{ __('Track Order') }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Categories Column -->
            <div class="col-lg-2 col-md-6 col-sm-6 mt-4 mt-lg-0">
                <div class="footer-menus">
                    <h4>Categories</h4>
                    <ul class="footer-links">
                        @foreach ($parentCategories as $category)
                            <li><a
                                    href="{{ route('sf.products.list', ['category' => $category->slug]) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Footer Bottom -->
        <div class="footer-bottom mt-5">
            <div class="row align-items-center align-items-md-start">
                <div class="col-md-6 mt-4 mt-md-0 order-2 order-md-1">
                    <p class="copyright mb-0">
                        {{ env('APP_NAME') }} © {{ date('Y') }}. All Rights Reserved | Developed ❤️ ZIP SOLUTIONS by VERTEX
                    </p>
                </div>
                <div class="col-md-6 order-1 order-md-2">
                    <div class="payment-methods d-flex justify-content-md-end align-items-center">
                        <img src="{{ asset('assets/payhere_long_banner.png') }}" alt="Apple Pay" class="payment-icon">
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Floating WhatsApp Icon -->
<div class="floating-whatsapp">
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', get_setting('phone')) }}" target="_blank"
        class="whatsapp-icon" title="Chat with us on WhatsApp">
        <i class="lab la-whatsapp"></i>
    </a>
</div>

<!-- Mobile Bottom Navigation -->
<div class="footer-mobile-nav d-block d-lg-none">
    <div class="container-fluid px-0">
        <div class="row">
            <div class="col-12">
                <div class="footer-mobile-nav__wrapper">
                    <a href="{{ route('home') }}" class="footer-mobile-nav__item">
                        <i class="las la-home"></i>
                        <span>Home</span>
                    </a>
                    @auth
                        @if (Auth::user()->isStaff())
                        <a href="{{ route('admin.dashboard') }}" class="footer-mobile-nav__item">
                            <i class="las la-user"></i>
                            <span>Admin</span>
                        </a>
                        @else
                        <a href="{{ route('user.dashboard') }}" class="footer-mobile-nav__item">
                            <i class="las la-user"></i>
                            <span>My Account</span>
                        </a>
                        @endif
                    @else
                        <a href="{{ route('user.login') }}" class="footer-mobile-nav__item">
                            <i class="las la-user"></i>
                            <span>Login</span>
                        </a>
                    @endauth
                    <a href="{{ route('user.wishlist') }}" class="footer-mobile-nav__item">
                        <i class="las la-heart"></i>
                        <span>Wishlist</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="footer-mobile-nav__item" data-bs-toggle="offcanvas"
                        data-bs-target="#cartSidebar">
                        <i class="las la-shopping-cart"></i>
                        <span class="footer-cart-count">Cart
                            ({{ session()->has('cart') ? count(session()->get('cart')) : '0' }})</span>
                    </a>
                    @auth
                        <a href="{{ route('logout') }}" class="footer-mobile-nav__item">
                            <i class="las la-sign-out-alt"></i>
                            <span>Logout</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Get a Quote Modal -->
<div class="modal fade" id="reqQuoteModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="reqQuoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="inquiryForm" class="ajax-form" action="{{ route('inquiry.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h3 class="modal-title fs-5" id="reqQuoteModalLabel">Request a Quote</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="companyName" class="form-label">Company Name <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="companyName" name="company_name">
                        <span class="text-danger field-notice" rel="company_name"></span>
                    </div>
                    <div class="mb-3">
                        <label for="contactPerson" class="form-label">Contact Person <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="contactPerson" name="contact_person">
                        <span class="text-danger field-notice" rel="contact_person"></span>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address <small class="text-danger">*</small></label>
                        <input type="email" class="form-control" id="email" name="contact_email">
                        <span class="text-danger field-notice" rel="contact_email"></span>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number <small class="text-danger">*</small></label>
                        <input type="text" class="form-control" id="phone" name="contact_phone">
                        <span class="text-danger field-notice" rel="contact_phone"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Request Products with Quantity</label>
                        <div id="productsList">
                            <div class="product-item mb-3 p-3 border rounded">
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <label class="form-label small">Product</label>
                                        <select class="form-select product-select" name="products[]" data-index="0">
                                            <option value="">Select Product</option>
                                            @foreach ($products as $product)
                                                <option value="{{ $product->id }}"
                                                    data-product-name="{{ $product->name }}">{{ $product->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">Variant</label>
                                        <select class="form-select variant-select" name="variants[]" data-index="0"
                                            disabled>
                                            <option value="">Select variant first</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2">
                                    <div class="col-md-4">
                                        <label class="form-label small">Quantity</label>
                                        <input type="number" class="form-control product-quantity"
                                            name="quantities[]" min="1" placeholder="Enter quantity">
                                    </div>
                                    <div class="col-md-1">
                                        <label class="form-label small">&nbsp;</label>
                                        <button type="button" class="btn btn-outline-danger remove-product w-100"
                                            style="display: none;">
                                            <i class="las la-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="variant-info mt-2" style="display: none;">
                                    <small class="text-muted">
                                        <i class="las la-info-circle"></i>
                                        <span class="variant-details"></span>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="addMoreProducts">
                            <i class="las la-plus"></i> Add More Products
                        </button>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Additional Comments</label>
                        <textarea class="form-control" id="message" name="additional_comments" rows="4"></textarea>
                        <span class="text-danger field-notice" rel="additional_comments"></span>
                    </div>
                    {!! NoCaptcha::renderJs() !!}
                    {!! NoCaptcha::display() !!}
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-style-1">Submit Inquiry</button>
                </div>
            </form>
        </div>
    </div>
</div>
