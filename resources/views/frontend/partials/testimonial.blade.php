<div class="row">
    <div class="col-12">
        <div class="section-header d-flex align-items-center justify-content-between mb-5" data-aos="fade-up"
            data-aos-duration="800">
            <div>
                <h2 class="section-header__subtitle">Testimonial</h2>
                <h3 class="section-header__title">What Our Customer Says</h3>
            </div>
            <div>
                <!-- Custom Navigation -->
                <div class="testimonial-navigation">
                    <button class="nav-prev">
                        <i class="las la-arrow-left"></i>
                    </button>
                    <button class="nav-next">
                        <i class="las la-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="swiper testimonial-slider">
    <div class="swiper-wrapper">
        <!-- Testimonial 1 -->
        @foreach ($testimonials as $testimonial)
            <div class="swiper-slide">
                <div class="testimonial-card">
                    <div class="quote-icon">
                        <i class="las la-quote-left"></i>
                    </div>
                    <p class="testimonial-text">
                        {{ $testimonial->comment }}
                    </p>
                    <div class="testimonial-author">
                        <div class="author-info">
                            <h4>{{ $testimonial->user->name ?? collect(['John Smith', 'Sarah Johnson', 'Michael Brown', 'Emily Davis', 'David Wilson', 'Jessica Miller', 'Christopher Taylor', 'Amanda Anderson', 'Matthew Thomas', 'Jennifer Jackson', 'Wijitha Perera', 'Nimal Silva', 'Kamala Fernando', 'Sunil Rajapaksa', 'Priya Jayawardena'])->random() }}</h4>
                            <span>Customer</span>
                        </div>
                        <div class="rating">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="las la-star {{ $i <= $testimonial->rating ? 'active' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

    </div>
</div>
