@extends('frontend.layouts.app')
@section('title', $title)
@section('description', $description)
@section('content')

    @php
        use App\Models\AboutPage;
        $heroSection = AboutPage::getSection('hero');
        $certificationsSection = AboutPage::getSection('certifications');
        $visionMissionSection = AboutPage::getSection('vision_mission');
        $counterSection = AboutPage::getSection('counter');
        $whyChooseSection = AboutPage::getSection('why_choose');
    @endphp

    <x-page-banner :backgroundImage="asset('assets/page_banner.jpg')" :breadcrumbs="$breadcrumbs" />

    @if ($heroSection && $heroSection->is_active)
        <!-- Hero About Section -->
        <section class="about-hero-section">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="800">
                        <div class="about-content">
                            <div class="section-badge" data-aos="fade-up" data-aos-delay="200">
                                <i class="las la-leaf"></i>
                                <span>{{ $heroSection->content['badge_text'] ?? 'About NatureVirtue.lk' }}</span>
                            </div>
                            <h1 class="about-title h3" data-aos="fade-up" data-aos-delay="300">
                                {{ $heroSection->content['main_title'] ?? 'From Nature to Your Home' }}
                                <span
                                    class="text-highlight">{{ $heroSection->content['highlight_text'] ?? 'Pure, Natural, Trusted' }}</span>
                            </h1>
                            <div class="about-description" data-aos="fade-up" data-aos-delay="400">
                                @if (isset($heroSection->content['description']))
                                    @foreach (explode("\n\n", $heroSection->content['description']) as $paragraph)
                                        @if (trim($paragraph))
                                            <p{!! $loop->first ? ' class="lead"' : '' !!}>
                                                {{ trim($paragraph) }}
                                                </p>
                                        @endif
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="800">
                        <div class="about-image-wrapper">
                            <div class="about-image-main">
                                <img src="{{ isset($heroSection->content['hero_image']) ? asset('storage/' . $heroSection->content['hero_image']) : asset('assets/ab1.jpg') }}"
                                    class="img-fluid" alt="batiks.lk Products">
                            </div>
                            <div class="about-image-floating">
                                <div class="floating-card">
                                    <i class="las la-award"></i>
                                    <div>
                                        <h6>Quality Certified</h6>
                                        <p>ISO 22000:2018 & HACCP</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($certificationsSection && $certificationsSection->is_active)
        <!-- Certifications Section -->
        <section class="certifications-section">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center" data-aos="fade-up">
                        <div class="section-badge">
                            <i class="las la-certificate"></i>
                            <span>Our Certifications</span>
                        </div>
                        <h2 class="section-title">{{ $certificationsSection->content['title'] ?? 'Quality You Can Trust' }}
                        </h2>
                        <p class="section-subtitle">
                            {{ $certificationsSection->content['subtitle'] ?? 'We maintain the highest standards of quality and safety through internationally recognized certifications' }}
                        </p>
                    </div>
                </div>
                <div class="row justify-content-center g-4">
                    <div class="col-lg-5 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="certificate-preview" data-bs-toggle="modal" data-bs-target="#isoModal">
                            <div class="cert-preview-image">
                                <img src="{{ isset($certificationsSection->content['iso_image']) ? asset('storage/' . $certificationsSection->content['iso_image']) : asset('assets/iso22000.jpeg') }}"
                                    alt="ISO 22000:2018 Certificate" class="img-fluid">
                                <div class="cert-overlay">
                                    <i class="las la-search-plus"></i>
                                    <span>View Certificate</span>
                                </div>
                            </div>
                            <div class="cert-preview-info">
                                <div class="cert-badge iso">ISO 22000:2018</div>
                                <h5>Food Safety Management System</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-6" data-aos="fade-up" data-aos-delay="400">
                        <div class="certificate-preview" data-bs-toggle="modal" data-bs-target="#haccpModal">
                            <div class="cert-preview-image">
                                <img src="{{ isset($certificationsSection->content['haccp_image']) ? asset('storage/' . $certificationsSection->content['haccp_image']) : asset('assets/haccp.jpeg') }}"
                                    alt="HACCP Certificate" class="img-fluid">
                                <div class="cert-overlay">
                                    <i class="las la-search-plus"></i>
                                    <span>View Certificate</span>
                                </div>
                            </div>
                            <div class="cert-preview-info">
                                <div class="cert-badge haccp">HACCP</div>
                                <h5>Hazard Analysis Critical Control Points</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certificate Modals -->
        <!-- ISO Certificate Modal -->
        <div class="modal fade" id="isoModal" tabindex="-1" aria-labelledby="isoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="isoModalLabel">
                            <span class="cert-badge iso me-2">ISO 22000:2018</span>
                            Food Safety Management System Certificate
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="certificate-full-view">
                            <img src="{{ isset($certificationsSection->content['iso_image']) ? asset('storage/' . $certificationsSection->content['iso_image']) : asset('assets/iso22000.jpeg') }}"
                                alt="ISO 22000:2018 Certificate" class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- HACCP Certificate Modal -->
        <div class="modal fade" id="haccpModal" tabindex="-1" aria-labelledby="haccpModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="haccpModalLabel">
                            <span class="cert-badge haccp me-2">HACCP</span>
                            Hazard Analysis Critical Control Points Certificate
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="certificate-full-view">
                            <img src="{{ isset($certificationsSection->content['haccp_image']) ? asset('storage/' . $certificationsSection->content['haccp_image']) : asset('assets/haccp.jpeg') }}"
                                alt="HACCP Certificate" class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($visionMissionSection && $visionMissionSection->is_active)
        <!-- Vision & Mission Section -->
        <section class="vision-mission-section py-5">
            <div class="container">
                <div class="row justify-content-center mb-5">
                    <div class="col-lg-8 text-center" data-aos="fade-up" data-aos-duration="800" data-aos-once="true">
                        <h2 class="section-title mb-3" data-aos="zoom-in" data-aos-duration="600" data-aos-delay="200"
                            data-aos-once="true">
                            {{ $visionMissionSection->content['section_title'] ?? 'Our Vision & Mission' }}</h2>
                        <p class="section-subtitle" data-aos="fade-up" data-aos-duration="700" data-aos-delay="400"
                            data-aos-once="true">
                            {{ $visionMissionSection->content['section_subtitle'] ?? 'Driving innovation in natural food solutions to create a healthier, more sustainable world' }}
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Vision Card -->
                    <div class="col-lg-6" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100"
                        data-aos-once="true" data-aos-easing="ease-out-cubic">
                        <div class="vision-mission-card vision-card">
                            <div class="card-header" data-aos="fade-down" data-aos-duration="600" data-aos-delay="300"
                                data-aos-once="true">
                                <div class="icon-wrapper vision-icon" data-aos="zoom-in" data-aos-duration="500"
                                    data-aos-delay="500" data-aos-once="true">
                                    <i class="las la-eye"></i>
                                </div>
                                <h3 data-aos="fade-up" data-aos-duration="600" data-aos-delay="600"
                                    data-aos-once="true">
                                    {{ $visionMissionSection->content['vision_title'] ?? 'Our Vision' }}</h3>
                            </div>
                            <div class="card-content">
                                <p data-aos="fade-up" data-aos-duration="700" data-aos-delay="700" data-aos-once="true">
                                    {{ $visionMissionSection->content['vision_content'] ?? 'To be a global leader in nourishing communities through innovative, sustainable, and delicious dehydrated food solutions, ensuring a healthier and more resilient world.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Mission Card -->
                    <div class="col-lg-6" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200"
                        data-aos-once="true" data-aos-easing="ease-out-cubic">
                        <div class="vision-mission-card mission-card">
                            <div class="card-header" data-aos="fade-down" data-aos-duration="600" data-aos-delay="400"
                                data-aos-once="true">
                                <div class="icon-wrapper mission-icon" data-aos="zoom-in" data-aos-duration="500"
                                    data-aos-delay="600" data-aos-once="true">
                                    <i class="las la-bullseye"></i>
                                </div>
                                <h3 data-aos="fade-up" data-aos-duration="600" data-aos-delay="700"
                                    data-aos-once="true">
                                    {{ $visionMissionSection->content['mission_title'] ?? 'Our Mission' }}</h3>
                            </div>
                            <div class="card-content">
                                <p data-aos="fade-up" data-aos-duration="700" data-aos-delay="800" data-aos-once="true">
                                    {{ $visionMissionSection->content['mission_content'] ?? 'To provide nutrient-rich, convenient, and sustainable food solutions, using innovative dehydration processes, to nourish and inspire a thriving global community.' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($counterSection && $counterSection->is_active)
        <!-- Company Values -->
        <section class="counter-section"
            style="background-image: url({{ isset($counterSection->content['background_image']) ? asset('storage/' . $counterSection->content['background_image']) : asset('assets/BG.png') }})">
            <div class="container">
                <div class="row text-center">
                    <div class="col-6 col-sm-3" data-aos="fade-up" data-aos-duration="800">
                        <div class="counter-item">
                            <h2><span class="counter"
                                    data-target="{{ $counterSection->content['years_count'] ?? 5 }}">0</span>+</h2>
                            <p>{{ $counterSection->content['years_label'] ?? 'Years of Experience' }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3" data-aos="fade-up" data-aos-duration="800">
                        <div class="counter-item">
                            <h2><span class="counter"
                                    data-target="{{ $counterSection->content['customers_count'] ?? 100 }}">0</span>+</h2>
                            <p>{{ $counterSection->content['customers_label'] ?? 'Happy Customer' }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3" data-aos="fade-up" data-aos-duration="800">
                        <div class="counter-item">
                            <h2><span class="counter"
                                    data-target="{{ $counterSection->content['products_count'] ?? 200 }}">0</span>+</h2>
                            <p>{{ $counterSection->content['products_label'] ?? 'Products' }}</p>
                        </div>
                    </div>
                    <div class="col-6 col-sm-3" data-aos="fade-up" data-aos-duration="800">
                        <div class="counter-item">
                            <h2><span class="counter"
                                    data-target="{{ $counterSection->content['awards_count'] ?? 5 }}">0</span>+</h2>
                            <p>{{ $counterSection->content['awards_label'] ?? 'Award Winning' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    @if ($whyChooseSection && $whyChooseSection->is_active)
        <!-- Why Choose Us Section -->
        <section class="values-section"
            style="--choose-bg: url('{{ isset($whyChooseSection->content['background_image']) ? asset('storage/' . $whyChooseSection->content['background_image']) : asset('assets/choose-bg.jpg') }}');"
            data-aos="fade-up" data-aos-duration="800">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 d-none d-lg-block">
                        <div class="store-image">
                            <img src="{{ isset($whyChooseSection->content['main_image']) ? asset('storage/' . $whyChooseSection->content['main_image']) : asset('assets/choose-per.png') }}"
                                alt="Farmer with organic vegetables" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="store-content py-5">
                            <div class="section-header mb-4">
                                <h3 class="section-header__title mb-2">
                                    {{ $whyChooseSection->content['main_title'] ?? '100% Trusted Source for Dehydrated Foods & Herbal Products' }}
                                </h3>
                                <p class="section-header__subdesc">
                                    {{ $whyChooseSection->content['description'] ?? 'Experience the pure goodness of nature with our carefully selected range of dehydrated foods and herbal products. Whether you\'re looking for healthy snacks, wellness herbs, or convenient nutrition—Nature\'s Virtue delivers quality you can trust.' }}
                                </p>
                            </div>
                            <div class="features-grid">
                                @if (isset($whyChooseSection->content['features']) && is_array($whyChooseSection->content['features']))
                                    @foreach ($whyChooseSection->content['features'] as $feature)
                                        <div class="feature-item">
                                            <div class="feature-icon">
                                                <img src="{{ asset('assets/icons/' . ($feature['icon'] ?? 'food.png')) }}"
                                                    alt="{{ $feature['title'] ?? 'Feature' }} icon">
                                            </div>
                                            <div class="feature-text">
                                                <h4>{{ $feature['title'] ?? 'Feature Title' }}</h4>
                                                <p>{{ $feature['description'] ?? 'Feature description' }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <!-- Default features if none in database -->
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <img src="{{ asset('assets/icons/food.png') }}" alt="Organic food icon">
                                        </div>
                                        <div class="feature-text">
                                            <h4>Premium Quality Products</h4>
                                            <p>Naturally preserved and carefully crafted for long-lasting freshness and
                                                nutrition.</p>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <img src="{{ asset('assets/icons/help.png') }}" alt="Support icon">
                                        </div>
                                        <div class="feature-text">
                                            <h4>24/7 Customer Support</h4>
                                            <p>Always here to help—reach us anytime with your questions or concerns.</p>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <img src="{{ asset('assets/icons/feedback.png') }}" alt="Feedback icon">
                                        </div>
                                        <div class="feature-text">
                                            <h4>Cash on delivery available</h4>
                                            <p>Enjoy doorstep delivery with exclusive discounts and offers.</p>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <img src="{{ asset('assets/icons/secure.png') }}" alt="Secure payment icon">
                                        </div>
                                        <div class="feature-text">
                                            <h4>100% Secure Payments</h4>
                                            <p>Shop confidently with our safe and encrypted checkout process.</p>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <img src="{{ asset('assets/icons/lorry.png') }}" alt="Shipping icon">
                                        </div>
                                        <div class="feature-text">
                                            <h4>Free Shipping</h4>
                                            <p>Free shipping with discount</p>
                                        </div>
                                    </div>
                                    <div class="feature-item">
                                        <div class="feature-icon">
                                            <img src="{{ asset('assets/icons/products.png') }}" alt="Organic food icon">
                                        </div>
                                        <div class="feature-text">
                                            <h4>100% Organic Food</h4>
                                            <p>100% healthy & fresh food</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif


    <!-- Awards & Certifications Section -->
    <section class="awards-certifications-section py-5">
        <div class="container">
            <div class="row justify-content-center mb-5">
                <div class="col-lg-8 text-center" data-aos="fade-up">
                    <div class="section-badge">
                        <i class="las la-trophy"></i>
                        <span>Awards & Recognition</span>
                    </div>
                    <h2 class="section-title">Excellence in Quality & Innovation</h2>
                    <p class="section-subtitle">
                        Our commitment to quality has been recognized through prestigious awards and international
                        certifications
                    </p>
                </div>
            </div>

            <!-- Awards Section -->
            <div class="row justify-content-center mb-5">
                <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="200">
                    <div class="award-card featured-award">
                        <div class="award-icon">
                            <i class="las la-trophy"></i>
                        </div>
                        <div class="award-content">
                            <h4>Best Natural Food Product 2023</h4>
                            <p class="award-issuer">Sri Lanka Food & Beverage Awards</p>
                            <p class="award-description">Recognized for excellence in natural food processing and
                                innovation in dehydrated products</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certifications Carousel -->
            <div class="row">
                <div class="col-12">
                    <h3 class="text-center mb-4" data-aos="fade-up">Our Certifications</h3>
                    <div class="certifications-carousel" data-aos="fade-up" data-aos-delay="300">
                        <div class="cert-item">
                            <div class="cert-card">
                                <div class="cert-image">
                                    <img src="{{ asset('assets/iso22000.jpeg') }}" alt="ISO 22000:2018">
                                </div>
                                <div class="cert-info">
                                    <h5>ISO 22000:2018</h5>
                                    <p>Food Safety Management</p>
                                </div>
                            </div>
                        </div>
                        <div class="cert-item">
                            <div class="cert-card">
                                <div class="cert-image">
                                    <img src="{{ asset('assets/haccp.jpeg') }}" alt="HACCP">
                                </div>
                                <div class="cert-info">
                                    <h5>HACCP</h5>
                                    <p>Hazard Analysis Control</p>
                                </div>
                            </div>
                        </div>
                        <div class="cert-item">
                            <div class="cert-card">
                                <div class="cert-image">
                                    <img src="{{ asset('assets/gmp.jpg') }}" alt="GMP">
                                </div>
                                <div class="cert-info">
                                    <h5>GMP</h5>
                                    <p>Good Manufacturing Practice</p>
                                </div>
                            </div>
                        </div>
                        <div class="cert-item">
                            <div class="cert-card">
                                <div class="cert-image">
                                    <img src="{{ asset('assets/organic.jpg') }}" alt="Organic Certification">
                                </div>
                                <div class="cert-info">
                                    <h5>Organic Certified</h5>
                                    <p>100% Organic Products</p>
                                </div>
                            </div>
                        </div>
                        <div class="cert-item">
                            <div class="cert-card">
                                <div class="cert-image">
                                    <img src="{{ asset('assets/halal.jpg') }}" alt="Halal Certification">
                                </div>
                                <div class="cert-info">
                                    <h5>Halal Certified</h5>
                                    <p>Islamic Dietary Standards</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .awards-certifications-section {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .award-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .featured-award {
            border: 2px solid #28a745;
            transform: scale(1.05);
        }

        .award-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .award-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #ffd700, #ffed4e);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
            color: #b8860b;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .award-content h4 {
            color: #2c3e50;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .award-issuer {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .award-description {
            color: #6c757d;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .award-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .certifications-carousel {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding: 1rem 0;
            scroll-behavior: smooth;
        }

        .certifications-carousel::-webkit-scrollbar {
            height: 8px;
        }

        .certifications-carousel::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .certifications-carousel::-webkit-scrollbar-thumb {
            background: #28a745;
            border-radius: 10px;
        }

        .cert-item {
            flex: 0 0 250px;
        }

        .cert-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            filter: grayscale(100%);
            opacity: 0.7;
        }

        .cert-card:hover {
            filter: grayscale(0%);
            opacity: 1;
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            border: 2px solid #28a745;
        }

        .cert-image {
            width: 120px;
            height: 120px;
            margin: 0 auto 1rem;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .cert-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .cert-card:hover .cert-image img {
            transform: scale(1.1);
        }

        .cert-info h5 {
            color: #2c3e50;
            font-weight: 600;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }

        .cert-info p {
            color: #6c757d;
            font-size: 0.85rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .certifications-carousel {
                gap: 1rem;
            }

            .cert-item {
                flex: 0 0 200px;
            }

            .cert-image {
                width: 100px;
                height: 100px;
            }

            .featured-award {
                transform: none;
            }
        }
    </style>


    <!-- Contact Info -->
    <section class="contact-section">
        <div class="container">
            @include('frontend.partials.footer-contact')
        </div>
    </section>
@stop

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const counters = document.querySelectorAll('.counter');
            const speed = 1500;

            const animateCounter = (counter) => {
                const target = parseInt(counter.dataset.target);
                let count = 0;

                const updateCount = () => {
                    const increment = target / speed;

                    if (count < target) {
                        count += increment;
                        counter.innerText = Math.ceil(count);
                        setTimeout(updateCount, 1);
                    } else {
                        counter.innerText = target;
                    }
                };

                updateCount();
            };

            // Start animation when element is in viewport
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounter(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });

            counters.forEach(counter => observer.observe(counter));
        });
    </script>
@stop
