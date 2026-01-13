<script>

    $(document).on('submit', '.ajax-form', function(e){
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var data = new FormData(this);
        $.ajax({
            url: url,
            type: method,
            contentType: false,
            processData: false,
            data: data,
            beforeSend: function() {
                // Show loading state
                form.find('button[type="submit"]').addClass('btn-loading').prop('disabled', true);
            },
            success: function(response){
                // Handle ProductStock image updates for product forms
                if (form.attr('action').includes('/product/')) {
                    handleProductStockImageUpdate(form, response);
                }

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'Successfully',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr, status, error){
                // Clear previous error notices
                form.find('.field-notice').text('');

                // Show error message in top-end position
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.message,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true
                    });
                }

                // Handle validation errors
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        form.find('.field-notice[rel="' + field + '"]').text(messages[0]);
                    });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: xhr.responseJSON.message,
                    });
                }
            },
            complete: function() {
                // Reset form state
                form.find('button[type="submit"]').removeClass('btn-loading').prop('disabled', false);
            }
        });
    });

    //get info ajax function
    function getInfo(url,method='GET'){
        return new Promise((resolve, reject) => {
            $.ajax({
                type: method,
                url: url,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                beforeSend: function() {
                    // $('#spinner').removeClass('d-none').addClass('spinner');
                },
                success: function (response) {
                    if (response.status) {
                        resolve(response.data);
                    }else {
                        reject('Something error');
                    }
                },error: function (xhr, textStatus, errorThrown) {
                    if (xhr.status == 422) {
                        reject(xhr.responseText);
                    } else {
                        reject('Something went wrong: ' + errorThrown);
                    }
                }, complete: function() {
                        // $('#spinner').addClass('d-none').removeClass('spinner');
                },
            });
        });
    }

    //status switch
    $(document).on('click', '.status-switch input[type="checkbox"]', function() {
        var $this = $(this);
        var url = $this.data('url');
        var data = {
            id: $this.data('id'),
            _token: csrfToken
        };
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            beforeSend: function() {
                // $('#spinner').removeClass('d-none').addClass('spinner');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Successfully',
                    text: response.message,
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: xhr.responseJSON.message,
                });
            },
            complete: function() {
                // $('#spinner').addClass('d-none').removeClass('spinner');
            },
        });
    });

    $(document).on('click', '.delete-record', function () {
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                var $this = $(this);
                var url = $this.data('url');
                var data = {
                    id: $this.data('id'),
                    _token: csrfToken
                };

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    data: data,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    beforeSend: function () {
                        // Show spinner if needed
                    },
                    success: function (response) {
                        $this.closest('tr').remove();
                        $('#cart-count').text(response.itemCount);

                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully',
                            text: response.message,
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: xhr.responseJSON?.message || 'Something went wrong!',
                        });
                    },
                    complete: function () {
                        // Hide spinner if used
                    },
                });
            }
        });
    });

    // Function to handle ProductStock image updates after form submission
    function handleProductStockImageUpdate(form, response) {
        // Clear any deletion indicators
        form.find('.alert-warning, .alert-success').remove();

        // Reset all delete flags
        form.find('input[name*="delete_image"]').val('0');
        form.find('input[name="delete_standard_image"]').val('0');

        // If we have detailed stock image information from the backend
        if (response.stock_images && response.stock_images.length > 0) {
            updateProductStockImages(form, response.stock_images);
            return; // Exit early as updateProductStockImages handles everything
        }

        // Clear any images that were marked for deletion
        form.find('input[name*="delete_image"][value="1"]').each(function() {
            const input = $(this);
            const index = input.attr('name').match(/\[(\d+)\]/)?.[1] || 'standard';
            const container = $(`#imageContainer_${index}`);

            if (container.length) {
                container.html(`
                    <div class="alert alert-success alert-sm mb-2" role="alert">
                        <i class="fas fa-check-circle"></i>
                        Image successfully deleted
                    </div>
                `);

                // Remove success message after 3 seconds
                setTimeout(() => {
                    container.find('.alert-success').fadeOut();
                }, 3000);
            }
        });

        // Handle new image uploads - show success indicators
        form.find('input[type="file"]').each(function() {
            const fileInput = $(this);
            if (fileInput[0].files && fileInput[0].files.length > 0) {
                const inputId = fileInput.attr('id');
                const index = inputId.includes('standard') ? 'standard' : inputId.match(/\d+/)?.[0];
                const container = $(`#imageContainer_${index}`);

                if (container.length) {
                    // Show upload success message
                    container.prepend(`
                        <div class="alert alert-success alert-sm mb-2 upload-success" role="alert">
                            <i class="fas fa-check-circle"></i>
                            Image uploaded successfully
                        </div>
                    `);

                    // Remove success message after 3 seconds
                    setTimeout(() => {
                        container.find('.upload-success').fadeOut();
                    }, 3000);
                }
            }
        });

        // Show general success message for image operations
        if (form.find('input[name*="delete_image"][value="1"]').length > 0 ||
            form.find('input[type="file"]').filter(function() { return this.files.length > 0; }).length > 0) {

            // Show toast for image operations
            if (typeof toastr !== 'undefined') {
                toastr.success('Product images updated successfully!');
            }
        }
    }

    // Enhanced function to update ProductStock images based on backend response
    function updateProductStockImages(form, stockImages) {
        stockImages.forEach(function(stock) {
            const index = stock.is_standard ? 'standard' : stock.id;
            const container = $(`#imageContainer_${index}`);
            const preview = $(`#previewImage_${index}`);
            const wrapper = preview.closest('.image-preview-wrapper');

            if (container.length && preview.length) {
                if (stock.has_image && stock.thumbnail) {
                    // Update the preview image with the new thumbnail
                    preview.attr('src', stock.thumbnail + '?t=' + new Date().getTime()); // Add timestamp to prevent caching
                    wrapper.show();

                    // Show update success indicator
                    showImageOperationSuccess(container, `${stock.variant} image updated`, 'update');
                } else {
                    // Hide preview if no image
                    wrapper.hide();
                    preview.attr('src', '');

                    // Show deletion success if image was removed
                    showImageOperationSuccess(container, `${stock.variant} image removed`, 'delete');
                }
            }
        });
    }

    // Function to show image operation success messages
    function showImageOperationSuccess(container, message, type) {
        const iconClass = type === 'delete' ? 'fa-trash' : type === 'upload' ? 'fa-upload' : 'fa-sync';
        const alertClass = type === 'delete' ? 'alert-info' : 'alert-success';

        const successAlert = $(`
            <div class="alert ${alertClass} alert-sm mb-2 image-operation-success" role="alert">
                <i class="fas ${iconClass}"></i>
                ${message}
            </div>
        `);

        container.prepend(successAlert);

        // Remove success message after 4 seconds with fade effect
        setTimeout(() => {
            successAlert.fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }

    // Function to refresh ProductStock images after successful update
    function refreshProductStockImages(response) {
        if (response.stock_images && response.stock_images.length > 0) {
            response.stock_images.forEach(function(stock) {
                const index = stock.is_standard ? 'standard' : stock.id;
                const preview = $(`#previewImage_${index}`);
                const wrapper = preview.closest('.image-preview-wrapper');
                const container = $(`#imageContainer_${index}`);

                if (preview.length) {
                    if (stock.has_image && stock.thumbnail) {
                        // Update image source with cache-busting timestamp
                        preview.attr('src', stock.thumbnail + '?t=' + new Date().getTime());
                        wrapper.show();

                        // Clear any deletion indicators
                        container.find('.alert-warning').remove();
                    } else {
                        // Hide image if deleted
                        wrapper.hide();
                        preview.attr('src', '');
                    }
                }
            });
        }
    }

</script>

<script>


}
</script>
