@extends('frontend.layouts.app')
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div id="mainSlider" class="carousel slide carousel-fade"  data-bs-ride="carousel" data-bs-interval="10000">
            <div class="carousel-inner">
                @foreach ($mainBanner as $banner)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }} {{ $banner->apply_shade ? 'shade' : '' }}">
                        <picture>
                            <source media="(max-width: 768px)" srcset="{{ $banner->mobile_banner ?? $banner->thumbnail }}">
                            <img src="{{ $banner->thumbnail }}" class="d-block zoom-image w-100" alt="{{ $banner->title }}">
                        </picture>
                        <div class="carousel-caption">
                            @if ($banner->subtitle)
                                <span class="d-none d-md-block">{!! $banner->subtitle !!}</span>
                            @endif
                            @if ($banner->title)
                                <h5 class="carousel-title d-none d-md-block">{!! $banner->title !!}</h5>
                            @endif
                            @if ($banner->description)
                                <p class="d-none d-md-block">{!! $banner->description !!}</p>
                            @endif
                            @if ($banner->link && $banner->link_text)
                                <a href="{{ $banner->link }}" class="btn btn-slider mt-2">{{ $banner->link_text }}</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#mainSlider" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#mainSlider" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Shipping Features Section -->
    <!-- <section class="shipping-features-section">
        <div class="container">
            <div class="shipping-features" data-aos="fade-up">
                <div class="row g-3">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <img src="{{ asset('assets/icons/delivery.png') }}" class="img-fluid" alt="Free Shipping">
                            </div>
                            <div class="feature-content">
                                <h3>Cash on delivery available</h3>
                                <p>Pay easily when your order arrives at your doorstep</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <img src="{{ asset('assets/icons/headphones.png') }}" class="img-fluid"
                                    alt="Customer Support">
                            </div>
                            <div class="feature-content">
                                <h3>24/7 Support</h3>
                                <p>Instant access to Support Customer Care</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <img src="{{ asset('assets/icons/shopping-bag.png') }}" class="img-fluid"
                                    alt="Secure Payment">
                            </div>
                            <div class="feature-content">
                                <h3>Secure Payment</h3>
                                <p>We ensure your money is safe</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <img src="{{ asset('assets/icons/package.png') }}" class="img-fluid" alt="Money-Back">
                            </div>
                            <div class="feature-content">
                                <h3>Quality Guarantee</h3>
                                <p>Premium quality products guaranteed</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

    <!-- About Company -->
    <section class="hb-section section-p-tb">
        <div class="container">
            <div class="row justify-content-center" data-aos="fade-up" data-aos-duration="900">
                <div class="col-md-6">
                    <div class="hb-box text-center">
                        <div class="hb-box-2">
                            <img src="{{ asset('assets/ab1.jpg') }}" class="img-fluid" alt="">
                        </div>
                    </div>
                </div>
                <div class="col-md-5 mt-5 mt-md-0">
                    <div class="hb-content">
                        <div class="hb-content__about">
                            <div class="hero-badge">
                                <i class="las la-award"></i>
                                <span>{!! get_setting('home_hero_subtitle') !!}</span>
                            </div>
                            <h1 class="hero-main-title">{!! get_setting('home_hero_title') !!}</h1>
                            <div class="hero-description">
                                {!! get_setting('home_hero_description') !!}

                                <!-- <div class="hero-action mt-4">
                                    <a href="{{ route('about') }}" class="btn btn-style-1 rounded-pill">Read More Our Story <i class="las la-arrow-right"></i></a>
                                </div> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if (0)
          <!-- Popular Categories Rotating Section -->
    <section class="popular-categories-rotating">
        <div class="container">
            <div class="section-header text-center mb-5" data-aos="fade-up" data-aos-duration="800">
                <div class="section-badge">
                    <i class="las la-leaf"></i>
                    <span>Our Collection</span>
                </div>
                <h2 class="section-main-title">Popular Categories</h2>
                <p class="section-subtitle">Discover our carefully curated collection of natural products from Sri Lanka.
                </p>
            </div>

            <div class="rotating-categories-wrapper">
                <div class="row">
                    <!-- Left Sidebar Categories -->
                    <div class="col-lg-3 col-md-4">
                        <div class="categories-sidebar">
                            @foreach ($popularCategories->take(4) as $index => $category)
                                <div class="category-item {{ $index === 0 ? 'active' : '' }}"
                                    data-category="{{ $index }}">
                                    <div class="category-content">
                                        <h4>{!! $category->name !!}</h4>
                                    </div>
                                    <div class="category-arrow">
                                        <i class="las la-angle-right"></i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Main Content Area -->
                    <div class="col-lg-5 col-md-8">
                        <div class="rotating-content">
                            @foreach ($popularCategories->take(4) as $index => $category)
                                <div class="content-slide {{ $index === 0 ? 'active' : '' }}"
                                    data-slide="{{ $index }}">
                                    <div class="content-body">
                                        <h4 class="wellness-title">{!! $category->name !!}</h4>
                                        <div class="wellness-desc">
                                            {!! $category->description !!}
                                        </div>
                                        <a href="{{ route('sf.products.list', ['category' => $category->slug]) }}"
                                            class="btn btn-find-products d-inline-flex align-items-center">
                                            Explore Products <i class="las la-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Right Image Area -->
                    <div class="col-lg-4 d-none d-lg-block">
                        <div class="rotating-images">
                            @foreach ($popularCategories->take(4) as $index => $category)
                                <div class="image-slide {{ $index === 0 ? 'active' : '' }}"
                                    data-image="{{ $index }}">
                                    <div class="image-container">
                                        {{-- <img src="{{ $category->thumbnail }}" alt="{{ $category->name }}" class="category-image"> --}}
                                        <img src="https://placehold.co/400x600.png" alt="{{ $category->name }}"
                                            class="category-image">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    @endif


    <!-- Original Popular Category Section -->
    <section class="popular-categories">
        <div class="container">
            <div class="section-header text-center mb-4" data-aos="fade-up" data-aos-duration="800">
                <h3 class="section-header__title">{{ __('Popular Collection') }}</h3>
                <p class="section-header__subdesc m-auto">
                    {{ __('Discover our range of natural products to enhance your wellness journey') }}</p>
            </div>

            <div class="row g-4">
                <!-- Row 1 -->
                @foreach ($popularCategories as $category)
                    <div class="col-lg-3 col-md-4 col-6" data-aos="fade-up" data-aos-duration="900">
                        <a href="{{ route('sf.products.list', ['category' => $category->slug]) }}">
                            <div class="category-card h-100">
                                <div class="category-image">
                                    <img src="{{ $category->thumbnail }}" title="{{ $category->name }}"
                                        alt="{{ $category->name }}" class="img-fluid">
                                </div>
                                <h3 class="category-title">{{ $category->name }}</h3>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>



    @if ($featuredProducts->isNotEmpty())
        <!-- Featured Products -->
        <section class="featured-products section-p-tb">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="section-header d-flex align-items-center justify-content-between flex-wrap gap-3 mb-5"
                            data-aos="fade-up" data-aos-duration="800">
                            <div class="section-header__content">
                                <h3 class="section-header__title">Featured Products</h3>
                                <p class="section-header__subdesc">Discover our range of natural and organic products for
                                    your
                                    wellness journey</p>
                            </div>
                            <div class="section-header__action">
                                <a href="{{ route('sf.products.list') }}" class="btn btn-style-1">View All <i
                                        class="las la-arrow-right"></i></a>
                            </div>
                        </div>
                    </div>
                    @foreach ($featuredProducts as $product)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4" data-aos="fade-up" data-aos-duration="900">
                            @include('frontend.partials.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- <div class="featured-collections">
        <div class="container">
            <div class="section-header mb-5">
                <div class="section-header__content">
                    <h2 class="section-header__subtitle">LATEST BLOGS & ARTICLES</h2>
                    <h3 class="section-header__title">Insights & Wellness Tips</h3>
                    <p class="section-header__subdesc">Stay informed with our latest articles on health, nutrition and
                        natural living for a balanced lifestyle</p>
                </div>
            </div>

            <div class="row g-4">
               
                @foreach ($blogPosts as $blogPost)
                    <div class="col-md-6">
                        <div class="collection-card">
                            <img src="{{ $blogPost->thumbnail }}" alt="{{ $blogPost->title }}"
                                class="collection-image">
                            <div class="collection-overlay">
                                <h3 class="collection-title">{!! $blogPost->title !!}</h3>
                                <p class="collection-description">{!! $blogPost->description !!}</p>
                                <a href="{{ route('blog', $blogPost->slug) }}" class="explore-link">Explore
                                    Collection</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div> -->

    <!-- Testimonials -->
    <section class="testimonial-section">
        <div class="container">
            @include('frontend.partials.testimonial')
        </div>
    </section>

    <!-- Contact Info -->
    <section class="contact-section">
        <div class="container">
            @include('frontend.partials.footer-contact')
        </div>
    </section>

    <!-- Luxury Batik Theme Styles -->
    <style>
        :root {
            --color-charcoal: #1C1C1C;
            --color-ivory: #FAF7F2;
            --color-gold: #D4AF37;
            --color-maroon: #5A1A1A;
            --color-dark-maroon: #3B0F0F;
        }

        /* Hero Section */
        .hero-section .carousel-caption span::before {
            background-color: var(--color-gold) !important;
        }

        .hero-section .btn-slider {
            background-color: var(--color-gold) !important;
            color: white !important;
        }

        .hero-section .btn-slider:hover {
            background-color: #C4A227 !important;
        }

        /* Hero Badge */
        .hb-content__about .hero-badge {
            background: rgba(212, 175, 55, 0.1) !important;
            border: 1px solid rgba(212, 175, 55, 0.2) !important;
        }

        .hb-content__about .hero-badge i,
        .hb-content__about .hero-badge span {
            color: var(--color-gold) !important;
        }

        .hb-content__about .hero-badge:hover {
            background: rgba(212, 175, 55, 0.15) !important;
            border-color: rgba(212, 175, 55, 0.3) !important;
        }

        /* Hero Main Title */
        .hb-content__about .hero-main-title {
            color: var(--color-maroon) !important;
        }

        /* Hero Description Links */
        .hb-content__about span {
            color: var(--color-gold) !important;
        }

        /* Buttons */
        .btn-style-1 {
            background-color: var(--color-gold) !important;
            color: white !important;
        }

        .btn-style-1:hover {
            background-color: #C4A227 !important;
        }

        /* Featured Products Section */
        .featured-products {
            background-color: #f5f5f5 !important;
        }

        /* Popular Categories */
        .popular-categories .category-card:hover {
            border-color: var(--color-gold) !important;
        }

        .popular-categories .category-card:hover .category-title {
            color: var(--color-gold) !important;
        }

        /* Shipping Features Icons */
        .shipping-features .feature-icon {
            background: rgba(212, 175, 55, 0.1) !important;
        }

        .shipping-features .feature-item:hover .feature-icon {
            background: rgba(212, 175, 55, 0.15) !important;
        }

        /* Testimonials */
        .testimonial-section .testimonial-card .quote-icon {
            color: var(--color-gold) !important;
        }

        .testimonial-section .testimonial-navigation .nav-prev,
        .testimonial-section .testimonial-navigation .nav-next {
            border: 1px solid rgba(212, 175, 55, 0.2) !important;
        }

        .testimonial-section .testimonial-navigation .nav-prev i,
        .testimonial-section .testimonial-navigation .nav-next i {
            color: var(--color-gold) !important;
        }

        .testimonial-section .testimonial-navigation .nav-prev:hover,
        .testimonial-section .testimonial-navigation .nav-next:hover {
            background-color: var(--color-gold) !important;
            border-color: var(--color-gold) !important;
        }

        .testimonial-section .testimonial-navigation .nav-prev:hover i,
        .testimonial-section .testimonial-navigation .nav-next:hover i {
            color: white !important;
        }

        /* Collections/Blog Cards */
        .explore-link {
            color: white !important;
        }

        .explore-link:hover {
            color: var(--color-gold) !important;
        }

        /* Footer Contact */
        .footer-contact .contact-item {
            border: 1px solid rgba(212, 175, 55, 0.2) !important;
        }

        .footer-contact .contact-item:hover {
            border-color: var(--color-gold) !important;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.1) !important;
        }

        .footer-contact .contact-item .icon-wrapper {
            background-color: rgba(212, 175, 55, 0.1) !important;
        }

        .footer-contact .contact-item .icon-wrapper i {
            color: var(--color-gold) !important;
        }

        .footer-contact .contact-item .contact-link {
            color: var(--color-gold) !important;
        }

        .footer-contact .contact-item .contact-link:hover {
            color: #C4A227 !important;
        }

        .footer-contact .newsletter-form .btn-subscribe {
            background-color: var(--color-gold) !important;
            color: white !important;
        }

        .footer-contact .newsletter-form .btn-subscribe:hover {
            background-color: #C4A227 !important;
        }

        /* Rotating Categories */
        .popular-categories-rotating .section-badge {
            background: rgba(212, 175, 55, 0.1) !important;
            border: 1px solid rgba(212, 175, 55, 0.2) !important;
        }

        .popular-categories-rotating .section-badge i,
        .popular-categories-rotating .section-badge span {
            color: var(--color-gold) !important;
        }

        .popular-categories-rotating .categories-sidebar .category-item::before {
            background: var(--color-gold) !important;
        }

        .popular-categories-rotating .categories-sidebar .category-item.active .category-content h4,
        .popular-categories-rotating .categories-sidebar .category-item:hover .category-content h4 {
            color: var(--color-gold) !important;
        }

        .popular-categories-rotating .categories-sidebar .category-item.active .category-arrow i,
        .popular-categories-rotating .categories-sidebar .category-item:hover .category-arrow i {
            color: var(--color-gold) !important;
        }

        .popular-categories-rotating .rotating-content .category-badge {
            background: rgba(212, 175, 55, 0.1) !important;
            color: var(--color-gold) !important;
        }

        .popular-categories-rotating .rotating-content .content-subtitle {
            color: var(--color-gold) !important;
        }

        .popular-categories-rotating .rotating-content .btn-find-products {
            background: var(--color-gold) !important;
            color: white !important;
        }

        .popular-categories-rotating .rotating-content .btn-find-products:hover {
            background: #C4A227 !important;
        }

        .popular-categories-rotating .rotating-content .content-body .wellness-title i {
            color: var(--color-gold) !important;
        }

        .popular-categories-rotating &::before {
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, transparent 70%) !important;
        }

        /* Links */
    

        /* Utility Classes */
        .text-gold {
            color: var(--color-gold) !important;
        }

        .bg-gold-light {
            background-color: rgba(212, 175, 55, 0.1) !important;
        }

        .border-gold {
            border-color: var(--color-gold) !important;
        }






        /* Force Hero Section Text to White - Add this to your existing <style> tag */
.hero-section .carousel-caption {
    color: white !important;
}

.hero-section .carousel-caption .carousel-title {
    color: white !important;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7) !important;
}

.hero-section .carousel-caption span {
    color: white !important;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7) !important;
}

.hero-section .carousel-caption p {
    color: white !important;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.7) !important;
}
    </style>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const categoryItems = document.querySelectorAll('.category-item');
            const contentSlides = document.querySelectorAll('.content-slide');
            const imageSlides = document.querySelectorAll('.image-slide');
            let currentIndex = 0;
            let autoRotateInterval;
            let isTransitioning = false;

            // Function to show specific slide with pure fade transition
            function showSlide(index, direction = 'next') {
                if (isTransitioning || index === currentIndex) return;

                isTransitioning = true;
                const prevIndex = currentIndex;

                // Update category items
                categoryItems.forEach(item => item.classList.remove('active'));
                if (categoryItems[index]) categoryItems[index].classList.add('active');

                // Handle content slides with pure fade effect
                if (contentSlides[prevIndex] && contentSlides[index]) {
                    // Fade out current slide
                    contentSlides[prevIndex].classList.remove('active');

                    // After fade out completes, show new slide
                    setTimeout(() => {
                        // Fade in new slide
                        contentSlides[index].classList.add('active');

                        // Allow new transitions after animation completes
                        setTimeout(() => {
                            isTransitioning = false;
                        }, 100);
                    }, 250);
                }

                // Handle image slides with fade effect
                imageSlides.forEach(slide => slide.classList.remove('active'));
                if (imageSlides[index]) {
                    setTimeout(() => {
                        imageSlides[index].classList.add('active');
                    }, 125);
                }

                currentIndex = index;
            }

            // Function to go to next slide
            function nextSlide() {
                if (isTransitioning) return;
                const nextIndex = (currentIndex + 1) % categoryItems.length;
                showSlide(nextIndex, 'next');
            }

            // Function to go to previous slide
            function prevSlide() {
                if (isTransitioning) return;
                const prevIndex = (currentIndex - 1 + categoryItems.length) % categoryItems.length;
                showSlide(prevIndex, 'prev');
            }

            // Function to start auto rotation
            function startAutoRotate() {
                if (autoRotateInterval) return; // Prevent multiple intervals
                autoRotateInterval = setInterval(nextSlide, 10000); // Change every 5 seconds
            }

            // Function to stop auto rotation
            function stopAutoRotate() {
                if (autoRotateInterval) {
                    clearInterval(autoRotateInterval);
                    autoRotateInterval = null;
                }
            }

            // Add click event listeners to category items
            categoryItems.forEach((item, index) => {
                item.addEventListener('click', function() {
                    if (isTransitioning) return;
                    stopAutoRotate();
                    showSlide(index);
                    // Restart auto rotation after 6 seconds of inactivity
                    setTimeout(() => {
                        if (!autoRotateInterval) {
                            startAutoRotate();
                        }
                    }, 6000);
                });
            });

            // Pause auto rotation when user hovers over the entire rotating section
            const rotatingWrapper = document.querySelector('.rotating-categories-wrapper');
            if (rotatingWrapper) {
                rotatingWrapper.addEventListener('mouseenter', stopAutoRotate);
                rotatingWrapper.addEventListener('mouseleave', () => {
                    // Restart after a short delay when mouse leaves
                    setTimeout(() => {
                        if (!autoRotateInterval) {
                            startAutoRotate();
                        }
                    }, 1000);
                });
            }

            // Handle visibility change (pause when tab is not active)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    stopAutoRotate();
                } else {
                    // Restart when tab becomes active again
                    setTimeout(() => {
                        if (!autoRotateInterval) {
                            startAutoRotate();
                        }
                    }, 1000);
                }
            });

            // Initialize - ensure first slide is active
            if (categoryItems.length > 0) {
                showSlide(0);
                // Start auto rotation after initial load
                setTimeout(startAutoRotate, 2000);
            }

            // Optional: Add keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowLeft') {
                    stopAutoRotate();
                    prevSlide();
                } else if (e.key === 'ArrowRight') {
                    stopAutoRotate();
                    nextSlide();
                }
            });
        });
    </script>
@endsection