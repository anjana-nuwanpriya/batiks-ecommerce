@extends('frontend.layouts.app')

@section('title', 'Manage Account')

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Add intl-tel-input CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- Add intl-tel-input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script>
        $(document).ready(function() {
            $("select").select2({
                theme: "bootstrap-5",
            });

            // Initialize intl-tel-input
            var phoneInput = document.querySelector("input[name='phone']");
            var iti = window.intlTelInput(phoneInput, {
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                separateDialCode: true,
                initialCountry: "auto",
                geoIpLookup: function(callback) {
                    fetch("https://ipapi.co/json")
                        .then(function(res) { return res.json(); })
                        .then(function(data) { callback(data.country_code); })
                        .catch(function() { callback("us"); });
                }
            });

            // Phone validation
            phoneInput.addEventListener("keyup", function() {
                if (iti.isValidNumber()) {
                    phoneInput.classList.remove("is-invalid");
                    phoneInput.classList.add("is-valid");
                } else {
                    phoneInput.classList.remove("is-valid");
                    phoneInput.classList.add("is-invalid");
                }
            });

            // Get full number with country code when submitting
            document.querySelector("form").addEventListener("submit", function(e) {
                var fullNumber = iti.getNumber();
                if(!iti.isValidNumber()) {
                    e.preventDefault();
                    phoneInput.classList.add("is-invalid");
                } else {
                    // Add a hidden input with the full number including country code
                    var hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'full_phone';
                    hiddenInput.value = fullNumber;
                    this.appendChild(hiddenInput);
                }
            });
        });


        // Re-initialize select2 when modal opens
        $(document).on('shown.bs.modal', '.modal', function() {
            $("select").select2({
                theme: "bootstrap-5",
                dropdownParent: $(this)
            });
        });

        function setDefaultAddress(id){
            $.ajax({
                url: "{{ route('user.set.default.address') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: { id: id },
                success: function(response){
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success'
                    }).then(() => {
                        location.reload();
                    });

                },
                error: function(response){
                    Swal.fire({
                        title: 'Error',
                        text: response.message,
                        icon: 'error'
                    });
                }
            });
        }
    </script>

@endsection

@section('content')

<x-page-banner
    :backgroundImage="asset('assets/page_banner.jpg')"
    :breadcrumbs="[['url' => route('home'), 'label' => 'Home'], ['url' => '', 'label' => 'Manage Account']]" />

    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Sidebar -->
                <div class="col-md-3">
                    @include('components.sidebar') <!-- Include the sidebar component -->
                </div>

                <!-- Main Content -->
                <div class="col-md-9">
                    <!-- Account Settings Card -->
                    <div class="card mb-3">
                        <div class="card-header">
                            Account Settings
                        </div>
                        <div class="card-body">
                            <form action="{{ route('user.update.profile') }}" method="post" class="ajax-form" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="">Name</label>
                                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Email</label>
                                        <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Phone</label>
                                        <div class="input-group">
                                            <input type="tel" class="form-control" name="user_phone" value="{{ auth()->user()->phone }}">
                                            <button type="button" id="verify-phone" class="btn btn-success">Verify</button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Password</label>
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="">Confirm Password</label>
                                        <input type="password" class="form-control" name="password_confirmation">
                                    </div>
                                    <div class="col-md-12 text-end">
                                        <button type="submit" class="btn btn-success">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Address Card -->
                    <div class="card">
                        <div class="card-header">
                            Address
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if ($address->count() > 0)
                                    @foreach ($address as $item)
                                        <div class="col-12 col-md-6 mb-3">
                                            <div class="card">
                                                <div class="card-body position-relative">
                                                    <div class="options position-absolute top-0 end-0">
                                                        <button type="button" class="btn btn-light rounded-circle" data-bs-toggle="dropdown"><i class="la la-ellipsis-v"></i></button>
                                                        <ul class="dropdown-menu">
                                                            @if (!$item->is_default)
                                                                <li><a href="#" class="dropdown-item font-14" onclick="setDefaultAddress({{ $item->id }})">Set as Default</a></li>
                                                            @endif
                                                            <li><a href="#" class="dropdown-item font-14" onclick="editAddress({{ $item->id }})">Edit</a></li>
                                                            <li><a href="#" class="dropdown-item font-14" onclick="deleteAddress({{ $item->id }})">Delete</a></li>
                                                        </ul>
                                                    </div>
                                                    <ul class="list-unstyled mb-0">
                                                        <li> <span class="fw-bold ">Address:</span> {{ $item->address }}</li>
                                                        <li> <span class="fw-bold">Zip/Postal Code:</span> {{ $item->zip_code }}</li>
                                                        <li> <span class="fw-bold">City:</span> {{ $item->city }}</li>
                                                        <li> <span class="fw-bold">Province / State:</span> {{ $item->state }}</li>
                                                        <li> <span class="fw-bold">Country:</span> {{ $item->country }}</li>
                                                        <li> <span class="fw-bold">Phone:</span> {{ $item->phone }}</li>
                                                    </ul>

                                                    @if ($item->is_default)
                                                        <span class="badge bg-success position-absolute" style="bottom: 10px; right: 10px;">Default</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <div class="col-md-6 mb-3">
                                    <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#addAddressModal"> <i class="la la-plus"></i> Add New Address</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
@endsection

@section('modals')
    <!-- Add Address Modal -->
    <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addAddressModalLabel">Add New Address</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('user.store.address') }}" method="post" class="ajax-form" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="">Address <span class="text-danger">*</span></label>
                            <textarea name="address" id="address" class="form-control" rows="2"></textarea>
                            <span class="text-danger field-notice" rel="address"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Zip/Postal Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="zip_code">
                            <span class="text-danger field-notice" rel="zip_code"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">City <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="city">
                            <span class="text-danger field-notice" rel="city"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Province / State <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="state">
                            <span class="text-danger field-notice" rel="state"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Country <span class="text-danger">*</span></label>
                            <select name="country" class="form-select">
                                <option value="">Select Country</option>
                                <option value="Sri Lanka">Sri Lanka</option>
                            </select>
                            <span class="text-danger field-notice" rel="country"></span>
                        </div>

                        <div class="mb-3">
                            <label for="">Phone <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" name="phone" placeholder="Enter phone number">
                            <span class="text-danger field-notice" rel="phone"></span>
                        </div>


                        <div class="mb-3">
                            <button type="submit" class="btn btn-success w-100">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Address Modal -->
    <div class="modal fade" id="editAddressModal" tabindex="-1" aria-labelledby="editAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog" id="editAddressModalContent">
        </div>
    </div>

@endsection
