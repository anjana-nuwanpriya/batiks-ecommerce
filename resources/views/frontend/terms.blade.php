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
                        <h1 class="display-5 fw-bold text-center mb-4 text-dark">Terms & Conditions</h1>
                        <div class="border-bottom mb-4"></div>

                        <div class="accordion" id="termsAccordion">
                            <!-- Section 1 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        1. Acceptance of Terms
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <p class="lead">By accessing and using Nature's Virtue website and services, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, please do not use our services.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        2. User Account
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <p>When creating an account with us, you must provide accurate and complete information. You are responsible for:</p>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Maintaining the security of your account</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>All activities that occur under your account</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Notifying us immediately of any unauthorized use</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        3. Intellectual Property
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <p>All content on this website is the property of Nature's Virtue and is protected by intellectual property laws:</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>No copying or reproduction without permission</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>No commercial use of our content</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>No modification of our materials</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>No distribution of our content</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>No removal of copyright notices</li>
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
                                        4. Product Information
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <p>We strive to provide accurate product information:</p>
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body">
                                                <ol class="list-group list-group-numbered">
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Product Descriptions</div>
                                                            While we aim for accuracy, we cannot guarantee all details
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Pricing</div>
                                                            Prices are subject to change without notice
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Availability</div>
                                                            Products may be out of stock or discontinued
                                                        </div>
                                                    </li>
                                                </ol>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingFive">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                        5. Limitation of Liability
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-info">
                                            <p>Nature's Virtue shall not be liable for:</p>
                                            <ul class="mb-0">
                                                <li>Any indirect, incidental, or consequential damages</li>
                                                <li>Loss of profits, revenue, or data</li>
                                                <li>Service interruptions or technical issues</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingSix">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                        6. Privacy Policy
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-warning">
                                            <i class="bi bi-shield-lock-fill me-2"></i>
                                            <strong>Your privacy is important to us.</strong> Please review our Privacy Policy to understand how we collect, use, and protect your personal information.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 7 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingSeven">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                        7. Contact Information
                                    </button>
                                </h2>
                                <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#termsAccordion">
                                    <div class="accordion-body">
                                        <p>For any questions about these Terms & Conditions, please contact us:</p>
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