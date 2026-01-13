@extends('adminlte::page')
@include('adminlte::filepond.file-pond', ['preview' => true])
@section('title', 'About Page - ' . env('APP_NAME'))
@section('plugins.Datatables', true)
@section('plugins.DatatablesPlugins', true)
@section('plugins.Select2', true)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>About Page Management</h1>
        <button class="btn btn-success" onclick="initializeDefaultSections()">
            <i class="fas fa-plus"></i> Initialize Default Sections
        </button>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <!-- Hero Section -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-star"></i> Hero Section
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="heroForm" onsubmit="updateSection(event, 'hero')">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Badge Text</label>
                                    <input type="text" class="form-control" name="content[badge_text]"
                                        value="{{ $sections->where('section_name', 'hero')->first()->content['badge_text'] ?? 'About NatureVirtue.lk' }}">
                                </div>
                                <div class="form-group">
                                    <label>Main Title</label>
                                    <input type="text" class="form-control" name="content[main_title]"
                                        value="{{ $sections->where('section_name', 'hero')->first()->content['main_title'] ?? 'From Nature to Your Home' }}">
                                </div>
                                <div class="form-group">
                                    <label>Highlight Text</label>
                                    <input type="text" class="form-control" name="content[highlight_text]"
                                        value="{{ $sections->where('section_name', 'hero')->first()->content['highlight_text'] ?? 'Pure, Natural, Trusted' }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hero Image</label>
                                    <input type="file" class="form-control" name="content[hero_image]" accept="image/*">
                                    @if (
                                        $sections->where('section_name', 'hero')->first() &&
                                            isset($sections->where('section_name', 'hero')->first()->content['hero_image']))
                                        <img src="{{ asset('storage/' . $sections->where('section_name', 'hero')->first()->content['hero_image']) }}"
                                            class="img-thumbnail mt-2" style="max-width: 200px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Description Paragraphs (One per line)</label>
                            <textarea class="form-control" name="content[description]" rows="8">{{ $sections->where('section_name', 'hero')->first()->content['description'] ??"At NatureVirtue.lk, we specialize in premium dehydrated foods and natural herbal products made in the heart of Sri Lanka. Born in the coastal village of Habaraduwa, our brand began with a simple goal: to make clean, healthy living more accessible to everyone.\n\nToday, we proudly produce our own range of products from sun-dried fruits and vegetables to herbal teas, wellness capsules, and superfoods. Every item is made using gentle dehydration methods that preserve maximum flavor, color, and nutrition with no added chemicals, preservatives, or artificial ingredients.\n\nWe partner with local farmers to source responsibly and support sustainable agriculture. Our facilities are ISO 22000:2018 and HACCP certified, ensuring global standards of food safety and quality control.\n\nWhether you're seeking nutritious snacks, natural remedies, or health-boosting herbs, NatureVirtue.lk delivers freshness, purity, and goodness right to your doorstep." }}</textarea>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="heroActive" name="is_active"
                                    {{ $sections->where('section_name', 'hero')->first()->is_active ?? true ? 'checked' : '' }}>
                                <label class="custom-control-label" for="heroActive">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Hero Section
                        </button>
                    </form>
                </div>
            </div>

            <!-- Certifications Section -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-certificate"></i> Certifications Section
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="certificationsForm" onsubmit="updateSection(event, 'certifications')">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Section Title</label>
                                    <input type="text" class="form-control" name="content[title]"
                                        value="{{ $sections->where('section_name', 'certifications')->first()->content['title'] ?? 'Quality You Can Trust' }}">
                                </div>
                                <div class="form-group">
                                    <label>Section Subtitle</label>
                                    <textarea class="form-control" name="content[subtitle]" rows="3">{{ $sections->where('section_name', 'certifications')->first()->content['subtitle'] ?? 'We maintain the highest standards of quality and safety through internationally recognized certifications' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>ISO Certificate Image</label>
                                    <input type="file" class="form-control" name="content[iso_image]" accept="image/*">
                                    @if (
                                        $sections->where('section_name', 'certifications')->first() &&
                                            isset($sections->where('section_name', 'certifications')->first()->content['iso_image']))
                                        <img src="{{ asset('storage/' . $sections->where('section_name', 'certifications')->first()->content['iso_image']) }}"
                                            class="img-thumbnail mt-2" style="max-width: 150px;">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>HACCP Certificate Image</label>
                                    <input type="file" class="form-control" name="content[haccp_image]" accept="image/*">
                                    @if (
                                        $sections->where('section_name', 'certifications')->first() &&
                                            isset($sections->where('section_name', 'certifications')->first()->content['haccp_image']))
                                        <img src="{{ asset('storage/' . $sections->where('section_name', 'certifications')->first()->content['haccp_image']) }}"
                                            class="img-thumbnail mt-2" style="max-width: 150px;">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="certificationsActive"
                                    name="is_active"
                                    {{ $sections->where('section_name', 'certifications')->first()->is_active ?? true ? 'checked' : '' }}>
                                <label class="custom-control-label" for="certificationsActive">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Update Certifications
                        </button>
                    </form>
                </div>
            </div>

            <!-- Vision & Mission Section -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Vision & Mission Section
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="visionMissionForm" onsubmit="updateSection(event, 'vision_mission')">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Vision</h5>
                                <div class="form-group">
                                    <label>Vision Title</label>
                                    <input type="text" class="form-control" name="content[vision_title]"
                                        value="{{ $sections->where('section_name', 'vision_mission')->first()->content['vision_title'] ?? 'Our Vision' }}">
                                </div>
                                <div class="form-group">
                                    <label>Vision Content</label>
                                    <textarea class="form-control" name="content[vision_content]" rows="4">{{ $sections->where('section_name', 'vision_mission')->first()->content['vision_content'] ?? 'To be a global leader in nourishing communities through innovative, sustainable, and delicious dehydrated food solutions, ensuring a healthier and more resilient world.' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Mission</h5>
                                <div class="form-group">
                                    <label>Mission Title</label>
                                    <input type="text" class="form-control" name="content[mission_title]"
                                        value="{{ $sections->where('section_name', 'vision_mission')->first()->content['mission_title'] ?? 'Our Mission' }}">
                                </div>
                                <div class="form-group">
                                    <label>Mission Content</label>
                                    <textarea class="form-control" name="content[mission_content]" rows="4">{{ $sections->where('section_name', 'vision_mission')->first()->content['mission_content'] ?? 'To provide nutrient-rich, convenient, and sustainable food solutions, using innovative dehydration processes, to nourish and inspire a thriving global community.' }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Section Title</label>
                            <input type="text" class="form-control" name="content[section_title]"
                                value="{{ $sections->where('section_name', 'vision_mission')->first()->content['section_title'] ?? 'Our Vision & Mission' }}">
                        </div>
                        <div class="form-group">
                            <label>Section Subtitle</label>
                            <input type="text" class="form-control" name="content[section_subtitle]"
                                value="{{ $sections->where('section_name', 'vision_mission')->first()->content['section_subtitle'] ?? 'Driving innovation in natural food solutions to create a healthier, more sustainable world' }}">
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="visionMissionActive"
                                    name="is_active"
                                    {{ $sections->where('section_name', 'vision_mission')->first()->is_active ?? true ? 'checked' : '' }}>
                                <label class="custom-control-label" for="visionMissionActive">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Update Vision & Mission
                        </button>
                    </form>
                </div>
            </div>

            <!-- Counter Section -->
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Counter Section
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="counterForm" onsubmit="updateSection(event, 'counter')">
                        <div class="row">
                            <div class="col-md-3">
                                <h6>Years of Experience</h6>
                                <div class="form-group">
                                    <label>Count</label>
                                    <input type="number" class="form-control" name="content[years_count]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['years_count'] ?? 5 }}">
                                </div>
                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" class="form-control" name="content[years_label]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['years_label'] ?? 'Years of Experience' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6>Happy Customers</h6>
                                <div class="form-group">
                                    <label>Count</label>
                                    <input type="number" class="form-control" name="content[customers_count]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['customers_count'] ?? 100 }}">
                                </div>
                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" class="form-control" name="content[customers_label]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['customers_label'] ?? 'Happy Customer' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6>Products</h6>
                                <div class="form-group">
                                    <label>Count</label>
                                    <input type="number" class="form-control" name="content[products_count]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['products_count'] ?? 200 }}">
                                </div>
                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" class="form-control" name="content[products_label]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['products_label'] ?? 'Products' }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <h6>Awards</h6>
                                <div class="form-group">
                                    <label>Count</label>
                                    <input type="number" class="form-control" name="content[awards_count]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['awards_count'] ?? 5 }}">
                                </div>
                                <div class="form-group">
                                    <label>Label</label>
                                    <input type="text" class="form-control" name="content[awards_label]"
                                        value="{{ $sections->where('section_name', 'counter')->first()->content['awards_label'] ?? 'Award Winning' }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Background Image</label>
                            <input type="file" class="form-control" name="content[background_image]"
                                accept="image/*">
                            @if (
                                $sections->where('section_name', 'counter')->first() &&
                                    isset($sections->where('section_name', 'counter')->first()->content['background_image']))
                                <img src="{{ asset('storage/' . $sections->where('section_name', 'counter')->first()->content['background_image']) }}"
                                    class="img-thumbnail mt-2" style="max-width: 200px;">
                            @endif
                        </div>
                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="counterActive" name="is_active"
                                    {{ $sections->where('section_name', 'counter')->first()->is_active ?? true ? 'checked' : '' }}>
                                <label class="custom-control-label" for="counterActive">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save"></i> Update Counter Section
                        </button>
                    </form>
                </div>
            </div>

            <!-- Why Choose Us Section -->
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-thumbs-up"></i> Why Choose Us Section
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form id="whyChooseForm" onsubmit="updateSection(event, 'why_choose')">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Main Title</label>
                                    <input type="text" class="form-control" name="content[main_title]"
                                        value="{{ $sections->where('section_name', 'why_choose')->first()->content['main_title'] ?? '100% Trusted Source for Dehydrated Foods & Herbal Products' }}">
                                </div>
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="content[description]" rows="4">{{ $sections->where('section_name', 'why_choose')->first()->content['description'] ?? 'Experience the pure goodness of nature with our carefully selected range of dehydrated foods and herbal products. Whether you\'re looking for healthy snacks, wellness herbs, or convenient nutrition—Nature\'s Virtue delivers quality you can trust.' }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Main Image</label>
                                    <input type="file" class="form-control" name="content[main_image]"
                                        accept="image/*">
                                    @if (
                                        $sections->where('section_name', 'why_choose')->first() &&
                                            isset($sections->where('section_name', 'why_choose')->first()->content['main_image']))
                                        <img src="{{ asset('storage/' . $sections->where('section_name', 'why_choose')->first()->content['main_image']) }}"
                                            class="img-thumbnail mt-2" style="max-width: 200px;">
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label>Background Image</label>
                                    <input type="file" class="form-control" name="content[background_image]"
                                        accept="image/*">
                                    @if (
                                        $sections->where('section_name', 'why_choose')->first() &&
                                            isset($sections->where('section_name', 'why_choose')->first()->content['background_image']))
                                        <img src="{{ asset('storage/' . $sections->where('section_name', 'why_choose')->first()->content['background_image']) }}"
                                            class="img-thumbnail mt-2" style="max-width: 200px;">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <h5 class="mt-4">Features</h5>
                        <div id="featuresContainer">
                            @php
                                $features = $sections->where('section_name', 'why_choose')->first()->content[
                                    'features'
                                ] ?? [
                                    [
                                        'title' => 'Premium Quality Products',
                                        'description' =>
                                            'Naturally preserved and carefully crafted for long-lasting freshness and nutrition.',
                                        'icon' => 'food.png',
                                    ],
                                    [
                                        'title' => '24/7 Customer Support',
                                        'description' =>
                                            'Always here to help—reach us anytime with your questions or concerns.',
                                        'icon' => 'help.png',
                                    ],
                                    [
                                        'title' => 'Free & Fast Shipping',
                                        'description' => 'Enjoy doorstep delivery with exclusive discounts and offers.',
                                        'icon' => 'feedback.png',
                                    ],
                                    [
                                        'title' => '100% Secure Payments',
                                        'description' =>
                                            'Shop confidently with our safe and encrypted checkout process.',
                                        'icon' => 'secure.png',
                                    ],
                                    [
                                        'title' => 'Free Shipping',
                                        'description' => 'Free shipping with discount',
                                        'icon' => 'lorry.png',
                                    ],
                                    [
                                        'title' => '100% Organic Food',
                                        'description' => '100% healthy & fresh food',
                                        'icon' => 'products.png',
                                    ],
                                ];
                            @endphp

                            @foreach ($features as $index => $feature)
                                <div class="feature-item border p-3 mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Feature Title</label>
                                                <input type="text" class="form-control"
                                                    name="content[features][{{ $index }}][title]"
                                                    value="{{ $feature['title'] }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Feature Description</label>
                                                <textarea class="form-control" name="content[features][{{ $index }}][description]" rows="2">{{ $feature['description'] }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Icon (filename)</label>
                                                <input type="text" class="form-control"
                                                    name="content[features][{{ $index }}][icon]"
                                                    value="{{ $feature['icon'] }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="whyChooseActive"
                                    name="is_active"
                                    {{ $sections->where('section_name', 'why_choose')->first()->is_active ?? true ? 'checked' : '' }}>
                                <label class="custom-control-label" for="whyChooseActive">Active</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-save"></i> Update Why Choose Us
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        function updateSection(event, sectionName) {
            event.preventDefault();

            const form = event.target;
            const formData = new FormData(form);

            // Show loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            submitBtn.disabled = true;

            // Build the final FormData properly
            const finalFormData = new FormData();

            // Add CSRF token
            finalFormData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            // Process form data
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('content[')) {
                    // Handle file uploads
                    if (value instanceof File && value.size > 0) {
                        finalFormData.append(key, value);
                    } else if (!(value instanceof File)) {
                        // Handle text inputs
                        finalFormData.append(key, value);
                    }
                } else {
                    // Handle non-content fields like is_active
                    finalFormData.append(key, value);
                }
            }

            // Use jQuery AJAX for better compatibility
            $.ajax({
                url: `/admin/about-section/${sectionName}`,
                type: 'POST',
                data: finalFormData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message || 'Section updated successfully!',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message || 'Failed to update section'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', xhr.responseText);
                    let errorMessage = 'Failed to update section';

                    try {
                        const response = JSON.parse(xhr.responseText);
                        errorMessage = response.message || errorMessage;
                    } catch (e) {
                        // If response is not JSON, use status text
                        errorMessage = xhr.status === 422 ? 'Validation error' : 'Server error occurred';
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage
                    });
                },
                complete: function() {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        }

        function initializeDefaultSections() {
            Swal.fire({
                title: 'Initialize Default Sections?',
                text: 'This will create default content for all about page sections.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, initialize!'
            }).then((result) => {
                if (result.isConfirmed) {
                    const sections = [{
                            name: 'hero',
                            content: {
                                'content[badge_text]': 'About NatureVirtue.lk',
                                'content[main_title]': 'From Nature to Your Home',
                                'content[highlight_text]': 'Pure, Natural, Trusted',
                                'content[description]': "At NatureVirtue.lk, we specialize in premium dehydrated foods and natural herbal products made in the heart of Sri Lanka. Born in the coastal village of Habaraduwa, our brand began with a simple goal: to make clean, healthy living more accessible to everyone.\n\nToday, we proudly produce our own range of products from sun-dried fruits and vegetables to herbal teas, wellness capsules, and superfoods. Every item is made using gentle dehydration methods that preserve maximum flavor, color, and nutrition with no added chemicals, preservatives, or artificial ingredients.\n\nWe partner with local farmers to source responsibly and support sustainable agriculture. Our facilities are ISO 22000:2018 and HACCP certified, ensuring global standards of food safety and quality control.\n\nWhether you're seeking nutritious snacks, natural remedies, or health-boosting herbs, NatureVirtue.lk delivers freshness, purity, and goodness right to your doorstep."
                            }
                        },
                        {
                            name: 'certifications',
                            content: {
                                'content[title]': 'Quality You Can Trust',
                                'content[subtitle]': 'We maintain the highest standards of quality and safety through internationally recognized certifications'
                            }
                        },
                        {
                            name: 'vision_mission',
                            content: {
                                'content[section_title]': 'Our Vision & Mission',
                                'content[section_subtitle]': 'Driving innovation in natural food solutions to create a healthier, more sustainable world',
                                'content[vision_title]': 'Our Vision',
                                'content[vision_content]': 'To be a global leader in nourishing communities through innovative, sustainable, and delicious dehydrated food solutions, ensuring a healthier and more resilient world.',
                                'content[mission_title]': 'Our Mission',
                                'content[mission_content]': 'To provide nutrient-rich, convenient, and sustainable food solutions, using innovative dehydration processes, to nourish and inspire a thriving global community.'
                            }
                        },
                        {
                            name: 'counter',
                            content: {
                                'content[years_count]': '5',
                                'content[years_label]': 'Years of Experience',
                                'content[customers_count]': '100',
                                'content[customers_label]': 'Happy Customer',
                                'content[products_count]': '200',
                                'content[products_label]': 'Products',
                                'content[awards_count]': '5',
                                'content[awards_label]': 'Award Winning'
                            }
                        },
                        {
                            name: 'why_choose',
                            content: {
                                'content[main_title]': '100% Trusted Source for Dehydrated Foods & Herbal Products',
                                'content[description]': 'Experience the pure goodness of nature with our carefully selected range of dehydrated foods and herbal products. Whether you\'re looking for healthy snacks, wellness herbs, or convenient nutrition—Nature\'s Virtue delivers quality you can trust.',
                                'content[features]': JSON.stringify([{
                                        title: 'Premium Quality Products',
                                        description: 'Naturally preserved and carefully crafted for long-lasting freshness and nutrition.',
                                        icon: 'food.png'
                                    },
                                    {
                                        title: '24/7 Customer Support',
                                        description: 'Always here to help—reach us anytime with your questions or concerns.',
                                        icon: 'help.png'
                                    },
                                    {
                                        title: 'Free & Fast Shipping',
                                        description: 'Enjoy doorstep delivery with exclusive discounts and offers.',
                                        icon: 'feedback.png'
                                    },
                                    {
                                        title: '100% Secure Payments',
                                        description: 'Shop confidently with our safe and encrypted checkout process.',
                                        icon: 'secure.png'
                                    },
                                    {
                                        title: 'Free Shipping',
                                        description: 'Free shipping with discount',
                                        icon: 'lorry.png'
                                    },
                                    {
                                        title: '100% Organic Food',
                                        description: '100% healthy & fresh food',
                                        icon: 'products.png'
                                    }
                                ])
                            }
                        }
                    ];

                    let completedRequests = 0;
                    const totalRequests = sections.length;

                    sections.forEach(section => {
                        $.ajax({
                            url: `/admin/about-section/${section.name}`,
                            type: 'POST',
                            data: {
                                ...section.content,
                                is_active: '1',
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                completedRequests++;
                                if (completedRequests === totalRequests) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: 'Default sections initialized successfully!',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error('Error initializing section:', section.name, xhr
                                    .responseText);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: `Failed to initialize ${section.name} section`
                                });
                            }
                        });
                    });
                }
            });
        }
    </script>
@endsection
