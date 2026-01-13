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
                        <h1 class="display-5 fw-bold text-center mb-4 text-dark">Return Policy</h1>
                        <div class="border-bottom mb-4"></div>

                        <div class="accordion" id="returnPolicyAccordion">
                            <!-- Section 1 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button bg-white fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        1. Return Window
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <p class="lead">We offer a <span class="badge bg-success">30-day return policy</span> for all products purchased directly from Nature's Virtue. The return window begins from the date of delivery.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        2. Product Condition
                                    </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>To be eligible for a return, items must be:</p>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Unused and in the same condition that you received them</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>In the original packaging</li>
                                            <li class="list-group-item bg-transparent px-0"><i class="bi bi-check-circle-fill text-success me-2"></i>Accompanied by the original receipt or proof of purchase</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                        3. Non-Returnable Items
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>The following items cannot be returned:</p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>Products that have been opened or used</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>Products with broken seals</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>Perishable goods</li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6">
                                                <ul class="list-group list-group-flush">
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>Personal care items</li>
                                                    <li class="list-group-item bg-transparent px-0"><i class="bi bi-x-circle-fill text-danger me-2"></i>Items marked as final sale</li>
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
                                        4. Return Process
                                    </button>
                                </h2>
                                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>To initiate a return:</p>
                                        <div class="card border-0 bg-light mb-3">
                                            <div class="card-body">
                                                <ol class="list-group list-group-numbered">
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Contact our customer service team</div>
                                                            Through our contact form or email
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Include order information</div>
                                                            Your order number and reason for return
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Wait for authorization</div>
                                                            We'll provide return authorization and shipping instructions
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Package the item</div>
                                                            Securely with all original materials
                                                        </div>
                                                    </li>
                                                    <li class="list-group-item d-flex justify-content-between align-items-start border-0 bg-transparent">
                                                        <div class="ms-2 me-auto">
                                                            <div class="fw-bold">Ship the item</div>
                                                            To the provided return address
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
                                        5. Refunds
                                    </button>
                                </h2>
                                <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>Once we receive and inspect your return, we will notify you about the status of your refund. If approved:</p>
                                        <div class="alert alert-info">
                                            <ul class="mb-0">
                                                <li>The refund will be processed to your original payment method</li>
                                                <li>Credit card refunds typically take 5-10 business days to appear</li>
                                                <li>Shipping costs are non-refundable</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6 -->
                            <div class="accordion-item border-0 mb-3 shadow-sm">
                                <h2 class="accordion-header" id="headingSix">
                                    <button class="accordion-button bg-white fw-bold collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                        6. Damaged Items
                                    </button>
                                </h2>
                                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <strong>If you receive a damaged item</strong>, please contact us immediately with photos of the damage. We will arrange for a replacement or refund at no additional cost to you.
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
                                <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#returnPolicyAccordion">
                                    <div class="accordion-body">
                                        <p>For any questions about our return policy, please contact our customer service team:</p>
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
                            <a href="{{ route('home') }}" class="btn btn-style-1 btn-lg px-4">Back to Shopping</a>
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
