<script>
    $('#add-to-cart').submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        addToCart(formData);
    });

    /*
    * Add to cart
    */
    function addToCart(formData) {
        $.ajax({
            url: '{{ route('cart.add') }}',
            data: formData,
            success: function(response) {
                $('.cart-count').text(response.itemCount);
                $('.footer-cart-count').text('Cart (' + response.itemCount + ')');
                $('#cart-items').html(response.viewCart);

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Added to Cart',
                    text: 'Item has been added to your cart',
                    showConfirmButton: true,
                    showDenyButton: true,
                    confirmButtonText: 'Continue Shopping',
                    denyButtonText: 'View Cart',
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#28a745'
                }).then((result) => {
                    if (result.isDenied) {
                        window.location.href = '{{ route('cart.index') }}';
                    }
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.message || 'Something went wrong!',
                });
            }
        });
    }


    /*
    * Remove from cart
    */
    function removeFromCart(productId, variantId) {
        // Check if cart item exists in cart page and remove it
        $.ajax({
            url: '{{ route('cart.remove') }}',
            type: 'POST',
            data: { product_id: productId, variant_id: variantId },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            success: function(response) {

                $('#cart-count').text(response.itemCount);
                $('.cart-count').text(response.itemCount);
                $('.footer-cart-count').text('Cart (' + response.itemCount + ')');

                $('#total-amount').text(response.totalAmount);
                $('#subtotal-amount').text(response.totalAmount);

                // Remove cart item element
                $('#cart-item-' + productId).remove();

                // Check if cart is empty after removal
                if ($('.cart-items .d-flex').length === 0) {
                    $('#cart-items .offcanvas-body').html(`
                        <div class="text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
                            <i class="las la-box-open display-4 text-muted"></i>
                            <p class="text-muted mt-3">Your cart is empty</p>
                        </div>
                    `);
                }

                const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                if (cartItem) {
                    // Check if there are no more cart items
                    if (document.querySelectorAll('.cart-item').length === 1) {
                        window.location.reload();
                    }

                    cartItem.remove();
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.message || 'Something went wrong!',
                });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const quantityControls = document.querySelectorAll('.quantity-control');

        quantityControls.forEach(item => {
            const minusBtn = item.querySelector('.minus');
            const plusBtn = item.querySelector('.plus');
            const input = item.querySelector('.quantity-input');
            const productId = item.getAttribute('data-productId');
            const variantId = item.getAttribute('data-variantId');

            const updateButtons = () => {
                const value = parseInt(input.value);
                const min = parseInt(input.min);
                const max = parseInt(input.max);

                minusBtn.disabled = value <= min;
                plusBtn.disabled = value >= max;
            };

            minusBtn.addEventListener('click', () => {
                if (parseInt(input.value) > parseInt(input.min)) {
                    input.value--;
                    updateButtons();
                    updateCartItem(productId, variantId, input.value);
                }
            });

            plusBtn.addEventListener('click', () => {
                if (parseInt(input.value) < parseInt(input.max)) {
                    input.value++;
                    updateButtons();
                    updateCartItem(productId, variantId, input.value);
                }
            });

            input.addEventListener('input', () => {
                let value = parseInt(input.value);
                if (value < input.min) input.value = input.min;
                if (value > input.max) input.value = input.max;
                updateButtons();
                updateCartItem(productId, variantId, input.value);
            });

            // Initialize button state on load
            updateButtons();
        });
    });

    function updateCartItem(productId, variantId, quantity) {
        $.ajax({
            url: '{{ route('cart.update') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: { product_id: productId, variant_id: variantId, quantity: quantity },
            success: function(response) {
                $('#subtotal-amount').text(response.subtotalAmount);
                $('#total-amount').text(response.totalAmount);
                $('#cartSidebar #cart-total-amount').text(response.totalAmount);
                $('#subtotal-'+productId+'-'+variantId).text(response.productTotal);
                $('#cart-items').html(response.viewCart);
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.message || 'Something went wrong!',
                });
            }
        });
    }

    function quickAddToCart(productSlug) {
        const productId = productSlug.getAttribute('data-product-id');
        const variant = productSlug.getAttribute('data-variant');
        const quantity = productSlug.getAttribute('data-quantity');


        // Serialize data like a form
        const data = {
            product_id: productId,
            variant: variant,
            quantity: quantity,
            _token: '{{ csrf_token() }}'
        };

        addToCart(data);
    }

    function addToWishlist(slug) {
        $.ajax({
            url: "{{ route('wishlist.add') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                slug: slug,
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Added to wishlist',
                    text: response.message,
                });

                // Add active class to wishlist button
                let wishlistBtn = document.querySelector(`button[onclick="addToWishlist('${slug}')"]`);
                if (wishlistBtn) {
                    $('#wishlistCount').text(response.count);
                    if (wishlistBtn.classList.contains('active')) {
                        wishlistBtn.classList.remove('active');
                    } else {
                        wishlistBtn.classList.add('active');
                    }
                }
            },
            error: function(response) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: response.responseJSON.message,
                    timer: 1500,
                });
            }
        });
    }

    function removeFromWishlist(slug) {
        $.ajax({
            url: "{{ route('wishlist.remove') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: {
                slug: slug,
            },
            success: function(response) {

                // Remove product card from wishlist page
                let productCard = document.querySelector(`div.product-card:has(button[onclick="removeFromWishlist('${slug}')"])`);
                if (productCard) {
                    productCard.closest('.col-md-3').remove();
                }

                // Update wishlist count in header
                $('#wishlistCount').text(response.count);

                // If no items left, show empty state
                if (response.count === 0) {
                    let mainContent = document.querySelector('.main-content');
                    if (mainContent) {
                        mainContent.innerHTML = `
                            <div class="text-center py-5">
                                <h3>Your wishlist is empty</h3>
                                <p>Browse our products and add items to your wishlist</p>
                                <a href="{{ route('sf.products.list') }}" class="btn btn-style-1">Browse Products</a>
                            </div>
                        `;
                    }
                }

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: response.message,
                    timer: 1500,
                    showConfirmButton: false
                });
            },
            error: function(response) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: response.responseJSON.message,
                });
            }
        });
    }

    function clearCart() {
        $.ajax({
            url: '{{ route('cart.clear') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {

                // Update cart sidebar with empty state
                $('#cartSidebar .offcanvas-body').html(`
                    <div class="text-center flex-grow-1 d-flex flex-column justify-content-center align-items-center">
                        <i class="las la-box-open display-4 text-muted"></i>
                        <p class="text-muted mt-3">Your cart is empty</p>
                    </div>
                `);

                // If on cart page, update main content
                let mainContent = document.querySelector('.main-content');
                if (mainContent && window.location.pathname === '/cart') {
                    mainContent.innerHTML = `
                        <div class="text-center py-5">
                            <h3>Your cart is empty</h3>
                            <p>Browse our products and add items to your cart</p>
                            <a href="{{ route('sf.products.list') }}" class="btn btn-style-1">Browse Products</a>
                        </div>
                    `;
                }

                // Update cart count to 0
                $('.cart-count').text('0');

            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON?.message || 'Something went wrong!',
                });
            }
        });
    }

    function updateShippingCost(selectedId) {

        document.querySelectorAll('.selected-address-badge').forEach(function(badge) {
            badge.classList.add('d-none');
        });

        // Show only the selected one
        const selectedBadge = document.getElementById('address-badge-' + selectedId);
        if (selectedBadge) {
            selectedBadge.classList.remove('d-none');
        }

        let paymentMethod = $('#payment-method').val();

        $.ajax({
            url: '{{ route('cart.shipping.cost') }}',
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            data: {
                payment_method: paymentMethod,
            },
            success: function(response) {

                $('#shipping-cost').text(response.shipping_cost).attr('data-cost', response.raw_shipping_cost);
                $('#total').text(response.total);
            },
            error: function(xhr) {
                console.log(xhr)
            }
        });
    }


    function deleteAddress(id){
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this address?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ route('user.delete.address') }}",
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
        });
    }

    function editAddress(id){
        $.ajax({
            url: "{{ route('user.edit.address.view') }}",
            type: "POST",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            data: { id: id },
            success: function(response){
                $('#editAddressModalContent').html(response.view);
                $('#editAddressModal').modal('show');
            },
            error: function(response){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: response.message,
                });
            }
        });
    }

</script>