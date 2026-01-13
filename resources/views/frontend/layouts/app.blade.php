<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {!! SEO::generate() !!}

    <link rel="icon" href="{{ asset('favicon.ico') }}" />

    {{-- Line Awesome --}}
    <link rel="stylesheet" href="{{ asset('vendor/line-awesome-1.3.0/css/line-awesome.min.css') }}">

    {{-- Select2 --}}
    <link rel="stylesheet" href="{{ asset('vendor/select2/css/select2.min.css') }}">

    @yield('styles')

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

</head>

<body>
    <script>
        const csrfToken = '{{ csrf_token() }}';
    </script>
    @include('sweetalert::alert')

    @stack('file_pond')

    <!-- Header Section -->
    @include('frontend.layouts.header')

    <!-- Main Content Section -->
    <main>
        @yield('content')
    </main>

    <!-- Footer Section -->
    @include('frontend.layouts.footer')

    <!-- Cart -->
    @include('frontend.partials.cart')

    {{-- jQuery --}}
    <script src="{{ asset('vendor/jquery/jquery.min.js') }}"></script>

    {{-- Select2 --}}
    <script src="{{ asset('vendor/select2/js/select2.min.js') }}"></script>

    @yield('modals')



    <!-- Currency Conversion -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const searchIcon = document.querySelector('.search-icon');
            const searchWrapper = document.querySelector('.search-wrapper');
            const searchBox = document.querySelector('.search-box');

            // Toggle search box visibility
            searchIcon.addEventListener('click', () => {
                searchWrapper.classList.toggle('active');
                // Toggle body scroll
                document.body.style.overflow = searchWrapper.classList.contains('active') ? 'hidden' : '';
            });

            // Close search box when clicking outside
            document.addEventListener('click', (event) => {
                if (!searchBox.contains(event.target) && !searchIcon.contains(event.target)) {
                    searchWrapper.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const productsList = document.getElementById('productsList');
            const addMoreBtn = document.getElementById('addMoreProducts');
            let productIndex = 1;

            // Function to update remove buttons visibility
            function updateRemoveButtons() {
                const removeButtons = productsList.querySelectorAll('.remove-product');
                removeButtons.forEach((btn, index) => {
                    btn.style.display = index === 0 && removeButtons.length === 1 ? 'none' : 'block';
                });
            }

            // Function to reset variant fields
            function resetVariantFields(productItem) {
                const variantSelect = productItem.querySelector('.variant-select');
                const variantInfo = productItem.querySelector('.variant-info');

                variantSelect.innerHTML = '<option value="">Select variant first</option>';
                variantSelect.disabled = true;
                variantInfo.style.display = 'none';
            }

            // Function to load product variants
            function loadProductVariants(productId, variantSelect, productItem) {
                if (!productId) {
                    resetVariantFields(productItem);
                    return;
                }

                // Show loading state
                variantSelect.innerHTML = '<option value="">Loading variants...</option>';
                variantSelect.disabled = true;

                fetch(`/product/${productId}/variants`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.variants.length > 0) {
                            variantSelect.innerHTML = '<option value="">Select variant</option>';

                            data.variants.forEach(variant => {
                                const option = document.createElement('option');
                                option.value = variant.id;
                                option.textContent = `${variant.name}`;
                                option.dataset.price = variant.price;
                                option.dataset.priceRaw = variant.price_raw;
                                option.dataset.stock = variant.stock;
                                option.dataset.sku = variant.sku;
                                option.dataset.inStock = variant.in_stock;

                                if (!variant.in_stock) {
                                    option.disabled = true;
                                    option.style.color = '#999';
                                }

                                variantSelect.appendChild(option);
                            });

                            variantSelect.disabled = false;
                        } else {
                            variantSelect.innerHTML = '<option value="">No variants available</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading variants:', error);
                        variantSelect.innerHTML = '<option value="">Error loading variants</option>';
                    });
            }

            // Function to update variant info
            function updateVariantInfo(variantSelect, productItem) {
                const selectedOption = variantSelect.options[variantSelect.selectedIndex];
                const variantPrice = productItem.querySelector('.variant-price');
                const variantInfo = productItem.querySelector('.variant-info');
                const variantDetails = productItem.querySelector('.variant-details');
                const quantityInput = productItem.querySelector('.product-quantity');

                if (selectedOption.value) {
                    const price = selectedOption.dataset.price;
                    const stock = selectedOption.dataset.stock;
                    const sku = selectedOption.dataset.sku;
                    const inStock = selectedOption.dataset.inStock === 'true';

                    // Show variant details
                    variantDetails.textContent = `SKU: ${sku} | ${inStock ? 'Available' : 'Out of stock'}`;
                    variantInfo.style.display = 'block';

                    // Style based on stock availability
                    if (!inStock) {
                        variantStock.style.color = '#dc3545';
                        quantityInput.disabled = true;
                    } else {
                        variantStock.style.color = '#198754';
                        quantityInput.disabled = false;
                    }
                } else {
                    variantPrice.value = '';
                    variantStock.value = '';
                    variantInfo.style.display = 'none';
                    quantityInput.disabled = false;
                    quantityInput.removeAttribute('max');
                }
            }

            // Add more products
            addMoreBtn.addEventListener('click', function() {
                const productItem = productsList.querySelector('.product-item').cloneNode(true);

                // Reset all fields
                productItem.querySelector('.product-select').value = '';
                productItem.querySelector('.product-quantity').value = '';
                resetVariantFields(productItem);

                // Update data attributes with new index
                const productSelect = productItem.querySelector('.product-select');
                const variantSelect = productItem.querySelector('.variant-select');

                productSelect.dataset.index = productIndex;
                variantSelect.dataset.index = productIndex;

                productsList.appendChild(productItem);
                updateRemoveButtons();
                productIndex++;
            });

            // Handle product selection change
            productsList.addEventListener('change', function(e) {
                if (e.target.classList.contains('product-select')) {
                    const productItem = e.target.closest('.product-item');
                    const variantSelect = productItem.querySelector('.variant-select');
                    const productId = e.target.value;

                    loadProductVariants(productId, variantSelect, productItem);
                }

                if (e.target.classList.contains('variant-select')) {
                    const productItem = e.target.closest('.product-item');
                    updateVariantInfo(e.target, productItem);
                }
            });

            // Handle quantity input validation
            productsList.addEventListener('input', function(e) {
                if (e.target.classList.contains('product-quantity')) {
                    const max = parseInt(e.target.getAttribute('max'));
                    const value = parseInt(e.target.value);

                    if (max && value > max) {
                        e.target.value = max;
                        // Show warning message
                        const productItem = e.target.closest('.product-item');
                        const variantDetails = productItem.querySelector('.variant-details');
                        const originalText = variantDetails.textContent;
                        variantDetails.textContent = `Maximum available quantity is ${max}`;
                        variantDetails.style.color = '#dc3545';

                        setTimeout(() => {
                            variantDetails.textContent = originalText;
                            variantDetails.style.color = '';
                        }, 3000);
                    }
                }
            });

            // Remove product
            productsList.addEventListener('click', function(e) {
                if (e.target.closest('.remove-product')) {
                    e.target.closest('.product-item').remove();
                    updateRemoveButtons();
                }
            });

            // Initialize first product item
            updateRemoveButtons();
        });


        // Search
        $('input[name="q"]').on('keyup', function() {
            const query = $(this).val();
            $('#search-results').html('');
            if (query.length > 2) {
                $.ajax({
                    url: "{{ route('sf.product.suggestions') }}",
                    type: "GET",
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    data: {
                        query: query
                    },
                    success: function(response) {
                        $('#search-results').html(response.products);
                    }
                });
            }
        });
    </script>

    {{-- Hide Navbar on Scroll --}}
    <script>
        const nav = document.querySelector('.main-nav');
        let lastScrollY = window.scrollY;

        window.addEventListener('scroll', () => {
            const currentScrollY = Math.max(window.scrollY, 0); // Prevent negative scroll values

            if (currentScrollY < lastScrollY) {
                // Scrolling up
                nav.classList.remove('hide');
            } else if (currentScrollY > lastScrollY) {
                // Scrolling down
                nav.classList.add('hide');
            }

            lastScrollY = currentScrollY;
        }, {
            passive: true
        });
    </script>

    <script type="text/javascript">
        function googleTranslateElementInit() {
            try {
                new google.translate.TranslateElement({
                    pageLanguage: 'en'
                }, 'google_translate_element');


                // Check if element was created
                setTimeout(function() {
                    var element = document.getElementById('google_translate_element');

                    populateLanguageDropdown();
                }, 2000);

            } catch (error) {
                console.error('Error initializing Google Translate:', error);
            }
        }

        function changeLanguage(langCode) {

            if (!langCode) {
                console.error('No language code provided');
                return;
            }

            // Update active state of language buttons
            updateLanguageButtonState(langCode);

            var maxAttempts = 20;
            var attempts = 0;

            var checkAndChange = setInterval(function() {
                attempts++;
                var selectField = document.querySelector('.goog-te-combo');

                if (selectField) {
                    clearInterval(checkAndChange);
                    // Check if the language code exists in options
                    var optionExists = Array.from(selectField.options).some(option => option.value === langCode);

                    if (optionExists) {
                        selectField.value = langCode;
                        selectField.dispatchEvent(new Event('change'));

                        // Store selected language in localStorage
                        localStorage.setItem('selectedLanguage', langCode);
                    } else {
                        localStorage.setItem('selectedLanguage', langCode)
                    }
                } else if (attempts >= maxAttempts) {
                    clearInterval(checkAndChange);
                }
            }, 200);
        }

        function updateLanguageButtonState(activeLanguage) {
            // Remove active class from all language buttons
            document.querySelectorAll('.lang-btn').forEach(function(btn) {
                btn.classList.remove('active');
            });

            // Add active class to the selected language button
            var activeBtn = document.querySelector('.lang-btn[onclick*="' + activeLanguage + '"]');
            if (activeBtn) {
                activeBtn.classList.add('active');
            }
        }

        // Initialize language button state on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check for stored language preference
            var storedLanguage = localStorage.getItem('selectedLanguage');
            if (storedLanguage) {
                updateLanguageButtonState(storedLanguage);
            } else {
                // Default to English if no preference stored
                updateLanguageButtonState('en');
            }
        });

        function populateLanguageDropdown() {
            var select = document.querySelector('.goog-te-combo');
            var dropdown = document.getElementById('language-dropdown');

            if (select && dropdown) {
                dropdown.innerHTML = '<option value="">More languages</option>';

                Array.from(select.options).forEach(function(option) {
                    if (['en', 'si', 'ta'].indexOf(option.value) === -1 && option.value) {
                        var newOption = document.createElement('option');
                        newOption.value = option.value;
                        newOption.text = option.text;
                        dropdown.appendChild(newOption);
                    }
                });

            } else {
                setTimeout(populateLanguageDropdown, 1000);
            }
        }
    </script>
    <script type="text/javascript" src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit&hl=en#en"></script>

    @include('common.cart-scripts')
    @include('common.scripts')

    @yield('scripts')

    {{-- @include('cookie-consent::index') --}}
</body>

</html>
