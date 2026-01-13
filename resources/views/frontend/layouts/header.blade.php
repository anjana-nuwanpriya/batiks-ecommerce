<header>
    {{-- <div class="bg-warning">
        <div class="container-fluid">
            <marquee behavior="scroll" direction="left" class="py-1 d-block">
                🎉 Special Offer: Registered users get FREE SHIPPING on their first order! Sign up now and save! 🚚
            </marquee>
        </div>
    </div> --}}

    <!-- Top Header -->
    <div class="top-bar py-2">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="contact-info d-flex align-items-center" itemscope itemtype="https://schema.org/ContactPoint">
                    <a href="mailto:{{ get_setting('email') }}"
                        class="d-none d-md-flex align-items-center me-3 notranslate" itemprop="email">
                        <i class="las la-envelope me-1"></i>
                        <span>{{ get_setting('email') }}</span>
                    </a>
                    <a href="tel:{{ get_setting('phone') }}" class="d-flex align-items-center notranslate"
                        itemprop="telephone">
                        <i class="las la-phone me-1"></i>
                        <span>{{ get_setting('phone') }}</span>
                    </a>
                </div>



                <div class="d-flex align-items-center ">
                    <div class="auth-links-wrapper pe-2 d-none d-lg-block">
                        @guest
                            <div class="auth-links-item">
                                <a href="{{ route('user.login') }}" class="text-white">Login</a>
                                <a href="{{ route('user.register') }}" class=" text-white">Register</a>
                            </div>
                        @else
                            <div class="dropdown d-inline auth-links-item-dropdown">
                                <a href="#" class="text-white dropdown-toggle notranslate" data-bs-toggle="dropdown">
                                    <i class="las la-user"></i> {{ Auth::user()->name }}
                                </a>
                                <ul class="dropdown-menu auth-links-menu">
                                    @if (Auth::user()->isStaff())
                                        <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i
                                                    class="las la-tachometer-alt me-2"></i> Dashboard</a></li>
                                    @else
                                        <li><a class="dropdown-item" href="{{ route('user.dashboard') }}"><i
                                                    class="las la-tachometer-alt me-2"></i> Dashboard</a></li>
                                        <li><a class="dropdown-item" href="{{ route('user.order-list') }}"><i
                                                    class="las la-shopping-bag me-2"></i> My Orders</a></li>
                                    @endif
                                    <li><a class="dropdown-item" href="{{ route('logout') }}"><i
                                                class="las la-sign-out-alt me-2"></i> Logout</a></li>

                                </ul>
                            </div>
                        @endguest
                    </div>
                    <div class="social-icons pe-2">
                        <div class="language-selector d-flex align-items-center">
                            <div class="language-buttons">
                                <button onclick="changeLanguage('en')" class="lang-btn" title="English">
                                    <span class="notranslate">EN</span>
                                </button>
                                <button onclick="changeLanguage('si')" class="lang-btn" title="සිංහල">
                                    <span class="notranslate">සි</span>
                                </button>
                                <button onclick="changeLanguage('ta')" class="lang-btn" title="தமிழ்">
                                    <span class="notranslate">த</span>
                                </button>
                            </div>
                            <select id="language-dropdown" onchange="changeLanguage(this.value)"
                                class="form-select form-select-sm ms-2 d-none d-md-block notranslate">
                                <option value="">More languages</option>
                            </select>

                            <div id="google_translate_element" style="display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <nav class="navbar navbar-expand-lg py-0 shadow-sm main-nav">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/logo/nv_logo.svg') }}" alt="{{ env('APP_NAME') }}" height="70">
            </a>

            <!-- Toggle Button for Mobile -->
            <div class="d-flex align-items-center order-lg-2">
                <div class="nav-right d-flex align-items-center">
                    <div class="search-wrapper me-0 me-lg-3">
                        <a class="search-icon" href="javascript:void(0)">
                            <i class="las la-search"></i>
                        </a>
                        <div class="search-box">
                            <form action="{{ route('sf.products.list') }}" method="GET" class="position-relative">
                                <div class="input-group">
                                    <input type="text" name="q" class="form-control"
                                        placeholder="Search here..." value="{{ request()->input('q') }}">
                                    <button type="submit" class="btn btn-style-1">Search</button>
                                </div>
                            </form>
                            <div class="search-results" id="search-results"></div>
                        </div>
                    </div>

                    <div class="login-link d-none d-lg-block">
                        <a class="p-2 ms-1 d-flex align-items-center position-relative"
                            href="{{ route('user.wishlist') }}" title="Wishlist" class="">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path
                                    d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                                </path>
                            </svg>
                            <span class="cart-badge" id="wishlistCount">
                                @if (auth()->check())
                                    {{ auth()->user()->wishlist()->count() }}
                                @else
                                    0
                                @endif
                            </span>
                        </a>
                    </div>

                    <div class="cart-wrapper ms-2 d-none d-lg-block">
                        <a class="p-2 cart-icon position-relative cart-toggler" href="javascript:void(0)"
                            data-bs-toggle="offcanvas" data-bs-target="#cartSidebar" aria-controls="cartSidebar">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="9" cy="21" r="1"></circle>
                                <circle cx="20" cy="21" r="1"></circle>
                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                            </svg>
                            <span class="cart-badge cart-count" id="cart-count">
                                @if (session()->has('cart'))
                                    {{ count(session()->get('cart')) }}
                                @else
                                    0
                                @endif
                            </span>
                        </a>
                    </div>
                </div>
                <button class="navbar-toggler ms-3" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileNav" aria-controls="mobileNav" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse order-lg-1" id="navbarNav">
                <ul class="navbar-nav navbar__nav ms-auto align-items-center">
                    <li class="navbar__item nav-item">
                        <a class="navbar__link nav-link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}"
                            href="{{ route('home') }}"> {{ __('Home') }}</a>
                    </li>
                    <li class="navbar__item nav-item">
                        <a class="navbar__link nav-link {{ Route::currentRouteName() == 'about' ? 'active' : '' }}"
                            href="{{ route('about') }}">{{ __('Our Story') }}</a>
                    </li>
                    <li class="navbar__item nav-item dropdown">
                        <a class="navbar__link nav-link" href="{{ route('sf.products.list') }}"
                            id="productsDropdown" role="button">
                            {{ __('All Products') }}
                        </a>
                    </li>
                    <li class="navbar__item nav-item">
                        <a class="navbar__link nav-link" href="{{ route('contact') }}">{{ __('Contact Us') }}</a>
                    </li>
                    <li class="navbar__item nav-item d-lg-none">
                        <button type="button" class="navbar__button navbar__qt-modal" data-bs-toggle="modal"
                            data-bs-target="#reqQuoteModal">
                            {{ __('Request a Quote') }}
                        </button>
                    </li>
                </ul>
                <button type="button" class="navbar__button navbar__qt-modal d-none d-lg-block"
                    data-bs-toggle="modal" data-bs-target="#reqQuoteModal">
                    {{ __('Request a Quote') }}
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Navigation Side Panel -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileNav" aria-labelledby="mobileNavLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileNavLabel">Menu</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <ul class="mobile-nav">
                <li class="mobile-nav__item">
                    <a class="mobile-nav__link {{ Route::currentRouteName() == 'home' ? 'active' : '' }}"
                        href="{{ route('home') }}">
                        <i class="las la-home me-2"></i>{{ __('Home') }}
                    </a>
                </li>
                <li class="mobile-nav__item">
                    <a class="mobile-nav__link {{ Route::currentRouteName() == 'about' ? 'active' : '' }}"
                        href="{{ route('about') }}">
                        <i class="las la-info-circle me-2"></i>{{ __('Our Story') }}
                    </a>
                </li>
                <li class="mobile-nav__item">
                    <a class="mobile-nav__link" href="{{ route('sf.products.list') }}">
                        <i class="las la-box me-2"></i>{{ __('All Products') }}
                    </a>
                </li>
                <li class="mobile-nav__item">
                    <a class="mobile-nav__link" href="{{ route('contact') }}">
                        <i class="las la-envelope me-2"></i>{{ __('Contact Us') }}
                    </a>
                </li>
                <li class="mobile-nav__item">
                    <a class="mobile-nav__link {{ Route::currentRouteName() == 'tracking.index' ? 'active' : '' }}"
                        href="{{ route('tracking.index') }}">
                        <i class="las la-shipping-fast me-2"></i>{{ __('Track Order') }}
                    </a>
                </li>
                <li class="mobile-nav__item">
                    <button type="button" class="mobile-nav__link w-100 btn btn-style-1 text-start text-white"
                        data-bs-toggle="modal" data-bs-target="#reqQuoteModal">
                        <i class="las la-quote-right me-2"></i>{{ __('Request a Quote') }}
                    </button>
                </li>
            </ul>
        </div>
    </div>
</header>
