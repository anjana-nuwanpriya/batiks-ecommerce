@extends('frontend.layouts.app')
@section('title', $title)
@section('description', $description)

@section('content')

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="$breadcrumbs"
/>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-md-5">
                        <h1 class="display-5 fw-bold text-center mb-4 text-dark">Privacy Policy</h1>
                        <div class="border-bottom mb-4"></div>

                        <div class="accordion" id="privacyPolicyAccordion">
                            <!-- Section 1 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        1. Information We Collect
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <p class="lead">We collect information that you provide directly to us, including:</p>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Name and contact information</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Billing and shipping address</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Payment information</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Order history and preferences</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        2. How We Use Your Information
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>We use the collected information for:</p>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Processing your orders and payments</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Communicating about your orders</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Sending marketing communications (with your consent)</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Improving our services and website</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        3. Information Sharing
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>We may share your information with:</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-shield-check text-primary me-2"></i>Service providers and partners</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-shield-check text-primary me-2"></i>Payment processors</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-shield-check text-primary me-2"></i>Shipping companies</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-shield-check text-primary me-2"></i>Legal authorities when required</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-shield-check text-primary me-2"></i>Business transfers (in case of merger/acquisition)</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingFour">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        4. Your Rights
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>You have the right to:</p>
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Access your data</div>
                                                            Request a copy of your personal information
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Correct your data</div>
                                                            Update or modify your information
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Delete your data</div>
                                                            Request deletion of your information
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Opt-out of marketing</div>
                                                            Unsubscribe from marketing communications
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        5. Data Security
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>We implement appropriate security measures to protect your information:</p>
                                        <div class="alert alert-info">
                                            <ul class="mb-0">
                                                <li>Encryption of sensitive data</li>
                                                <li>Regular security assessments</li>
                                                <li>Secure payment processing</li>
                                                <li>Limited access to personal information</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingSix">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                        6. Cookies and Tracking
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-warning">
                                            <i class="bi bi-info-circle-fill me-2"></i>
                                            <strong>We use cookies and similar technologies</strong> to improve your browsing experience, analyze site traffic, and personalize content. You can control cookie preferences through your browser settings.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 7 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingSeven">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                        7. Contact Us
                                    </button>
                                </h2>
                                <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#privacyPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>For privacy-related questions or concerns, please contact us:</p>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="card h-100 border-0 bg-light">
                                                    <div class="card-body text-center">
                                                        <i class="bi bi-envelope-fill display-6 text-primary mb-3"></i>
                                                        <h5 class="card-title">Email</h5>
                                                        <p class="card-text">{{ get_setting('email') }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card h-100 border-0 bg-light">
                                                    <div class="card-body text-center">
                                                        <i class="bi bi-telephone-fill display-6 text-primary mb-3"></i>
                                                        <h5 class="card-title">Phone</h5>
                                                        <p class="card-text">{{ get_setting('phone') }}<br><small class="text-muted">Monday to Friday, 9 AM - 5 PM</small></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5">
                            <a href="{{ route('home') }}" class="btn btn-style-1 btn-lg px-4">Back to Home</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-5">
            <div class="col-12 text-center">
                <p class="text-muted">
                    <small>Last updated: May 17, 2025</small>
                </p>
            </div>
        </div>
    </div>
</section>

@endsection