/**
 * Enhanced Variant Image Upload JavaScript
 * Provides drag & drop, validation, improved UX, and proper image deletion
 */

class VariantImageUploader {
    constructor() {
        this.initializeDragAndDrop();
        this.initializeFileValidation();
        this.bindEvents();
    }

    initializeDragAndDrop() {
        // Add drag and drop functionality to all custom-file-label elements
        document.querySelectorAll('.custom-file-label').forEach(label => {
            const input = label.previousElementSibling;

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                label.addEventListener(eventName, this.preventDefaults, false);
            });

            ['dragenter', 'dragover'].forEach(eventName => {
                label.addEventListener(eventName, () => this.highlight(label), false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                label.addEventListener(eventName, () => this.unhighlight(label), false);
            });

            label.addEventListener('drop', (e) => this.handleDrop(e, input), false);
        });
    }

    preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    highlight(element) {
        element.classList.add('drag-over');
    }

    unhighlight(element) {
        element.classList.remove('drag-over');
    }

    handleDrop(e, input) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            input.files = files;
            input.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    initializeFileValidation() {
        document.querySelectorAll('input[type="file"][accept*="image"]').forEach(input => {
            input.addEventListener('change', (e) => this.validateFile(e.target));
        });
    }

    validateFile(input) {
        const file = input.files[0];
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];

        if (!file) return;

        // Reset validation classes
        input.classList.remove('is-invalid', 'is-valid');

        // Validate file size
        if (file.size > maxSize) {
            this.showValidationError(input, 'File size must be less than 5MB');
            return false;
        }

        // Validate file type
        if (!allowedTypes.includes(file.type)) {
            this.showValidationError(input, 'Please select a valid image file (JPEG, PNG, GIF, WebP)');
            return false;
        }

        // Show success state
        input.classList.add('is-valid');
        this.clearValidationError(input);
        return true;
    }

    showValidationError(input, message) {
        input.classList.add('is-invalid');
        const errorElement = input.closest('.form-group').querySelector('.field-notice');
        if (errorElement) {
            errorElement.textContent = message;
        }
    }

    clearValidationError(input) {
        const errorElement = input.closest('.form-group').querySelector('.field-notice');
        if (errorElement) {
            errorElement.textContent = '';
        }
    }

    bindEvents() {
        // Update file labels when files are selected
        document.querySelectorAll('.custom-file-input').forEach(input => {
            input.addEventListener('change', function() {
                const label = this.nextElementSibling;
                const fileName = this.files[0]?.name || 'Choose image...';
                label.textContent = fileName;
            });
        });
    }

    // Static method to delete variant image via AJAX
    static async deleteVariantImageAjax(stockId) {
        try {
            const response = await fetch('/admin/product/variant-image', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ stock_id: stockId })
            });

            const result = await response.json();

            if (result.success) {
                toastr.success(result.message || 'Image deleted successfully');
                return true;
            } else {
                toastr.error(result.message || 'Failed to delete image');
                return false;
            }
        } catch (error) {
            console.error('Error deleting image:', error);
            toastr.error('An error occurred while deleting the image');
            return false;
        }
    }

    // Static method to show loading state
    static showLoadingState(imageWrapper) {
        imageWrapper.classList.add('loading');
    }

    // Static method to hide loading state
    static hideLoadingState(imageWrapper) {
        imageWrapper.classList.remove('loading');
    }

    // Static method to show success state
    static showSuccessState(imageWrapper) {
        imageWrapper.classList.add('success');
        setTimeout(() => {
            imageWrapper.classList.remove('success');
        }, 2000);
    }

    // Static method to show error state
    static showErrorState(imageWrapper) {
        imageWrapper.classList.add('error');
        setTimeout(() => {
            imageWrapper.classList.remove('error');
        }, 3000);
    }
}

// Initialize the uploader when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    new VariantImageUploader();
});

// Enhanced preview function with loading states
function previewVariantImageEnhanced(input, index) {
    const preview = document.getElementById(`previewImage_${index}`);
    const wrapper = preview.closest('.image-preview-wrapper');
    const fileLabel = input.nextElementSibling;

    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Show loading state
        VariantImageUploader.showLoadingState(wrapper);

        // Update file label
        fileLabel.textContent = file.name;

        // Show preview
        const reader = new FileReader();
        reader.onload = function (e) {
            preview.src = e.target.result;
            wrapper.style.display = 'inline-block';

            // Hide loading and show success
            VariantImageUploader.hideLoadingState(wrapper);
            VariantImageUploader.showSuccessState(wrapper);
        };

        reader.onerror = function() {
            VariantImageUploader.hideLoadingState(wrapper);
            VariantImageUploader.showErrorState(wrapper);
            toastr.error('Failed to load image preview');
        };

        reader.readAsDataURL(file);

        // Reset delete flag
        const deleteInput = document.getElementById(`deleteImage_${index}`);
        if (deleteInput) {
            deleteInput.value = '0';
        }
    } else {
        clearVariantImagePreview(index);
    }
}

// Enhanced delete function with AJAX option
async function deleteVariantImageEnhanced(variantIndex, variantId, useAjax = false) {
    if (!confirm('Are you sure you want to delete this image?')) {
        return;
    }

    const preview = document.getElementById(`previewImage_${variantIndex}`);
    const wrapper = preview.closest('.image-preview-wrapper');
    const container = document.getElementById(`imageContainer_${variantIndex}`);

    if (useAjax && variantId) {
        // Show loading state
        VariantImageUploader.showLoadingState(wrapper);

        // Try AJAX deletion
        const success = await VariantImageUploader.deleteVariantImageAjax(variantId);

        VariantImageUploader.hideLoadingState(wrapper);

        if (success) {
            // Add fade out animation
            wrapper.style.transition = 'opacity 0.3s ease';
            wrapper.style.opacity = '0';

            setTimeout(() => {
                // Hide preview completely
                wrapper.style.display = 'none';
                wrapper.style.opacity = '1';

                // Reset file input
                const fileInput = document.getElementById(`variantImage_${variantIndex}`);
                const fileLabel = fileInput.nextElementSibling;
                fileInput.value = '';
                fileLabel.textContent = 'Choose image...';

                // Show success indicator
                showDeletionSuccess(container);
            }, 300);

            return;
        }
    }

    // Fallback to form-based deletion
    const deleteInput = document.getElementById(`deleteImage_${variantIndex}`);
    if (deleteInput) {
        deleteInput.value = '1';
    }

    // Add fade out animation
    wrapper.style.transition = 'opacity 0.3s ease';
    wrapper.style.opacity = '0';

    setTimeout(() => {
        // Hide preview
        wrapper.style.display = 'none';
        wrapper.style.opacity = '1';

        // Reset file input
        const fileInput = document.getElementById(`variantImage_${variantIndex}`);
        const fileLabel = fileInput.nextElementSibling;
        fileInput.value = '';
        fileLabel.textContent = 'Choose image...';

        // Show deletion pending indicator
        showDeletionPending(container);
    }, 300);

    // Show success message
    if (typeof toastr !== 'undefined') {
        toastr.warning('Image will be deleted when you save the product');
    }
}

// Function to show deletion success indicator
function showDeletionSuccess(container) {
    if (container) {
        container.innerHTML = `
            <div class="alert alert-success alert-sm mb-2" role="alert">
                <i class="fas fa-check-circle"></i>
                Image deleted successfully
            </div>
        `;
    }
}

// Function to show deletion pending indicator
function showDeletionPending(container) {
    if (container) {
        container.innerHTML = `
            <div class="alert alert-warning alert-sm mb-2" role="alert">
                <i class="fas fa-exclamation-triangle"></i>
                Image marked for deletion - will be removed when you save
            </div>
        `;
    }
}

// Function to clear deletion indicators
function clearDeletionIndicator(container) {
    if (container) {
        const alerts = container.querySelectorAll('.alert');
        alerts.forEach(alert => alert.remove());
    }
}

// Enhanced clear function
function clearVariantImagePreview(index) {
    const preview = document.getElementById(`previewImage_${index}`);
    const wrapper = preview.closest('.image-preview-wrapper');
    const fileInput = document.getElementById(`variantImage_${index}`);
    const fileLabel = fileInput.nextElementSibling;
    const container = document.getElementById(`imageContainer_${index}`);

    // Clear preview
    preview.src = '';
    wrapper.style.display = 'none';

    // Reset file input
    fileInput.value = '';
    fileLabel.textContent = 'Choose image...';

    // Clear any deletion indicators
    clearDeletionIndicator(container);

    // Reset delete flag
    const deleteInput = document.getElementById(`deleteImage_${index}`);
    if (deleteInput) {
        deleteInput.value = '0';
    }
}
