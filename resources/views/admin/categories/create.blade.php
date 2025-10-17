@extends('layouts.admin')

@section('title', 'Create Category')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Create Category</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="icon-home"></i>
                </a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.categories.index') }}">Categories</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Create</a>
            </li>
        </ul>
    </div>

    <!-- Global Error Messages -->
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-check-circle"></i> Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong><i class="fas fa-exclamation-circle"></i> Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">New Category</h4>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-round ms-auto">
                            <i class="fa fa-arrow-left"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Basic information -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Basic Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Category Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="slug" class="form-label">Slug (URL)</label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                   id="slug" name="slug" value="{{ old('slug') }}">
                                            <small class="form-text text-muted">Leave blank to auto-generate</small>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings and media -->
                            <div class="col-md-4">
                                <!-- Image -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Category Image</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                   id="image" name="image" accept="image/*">
                                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (max: 2MB)</small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Preview -->
                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <img id="preview-img" src="" alt="Preview" 
                                                 class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Apparence -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Appearance</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="icon" class="form-label">Icon (Font Awesome)</label>
                                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                                   id="icon" name="icon" value="{{ old('icon', 'fas fa-tag') }}" 
                                                   placeholder="fas fa-tag">
                                            <small class="form-text text-muted">Ex: fas fa-recycle, fas fa-leaf</small>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="color" class="form-label">Color</label>
                                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                                   id="color" name="color" value="{{ old('color', '#007bff') }}">
                                            @error('color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Appearance preview -->
                                        <div class="mt-3">
                                            <label class="form-label">Preview</label>
                                            <div class="d-flex align-items-center">
                                                <div id="icon-preview" class="avatar avatar-sm me-2" style="background-color: #007bff;">
                                                    <i class="fas fa-tag text-white"></i>
                                                </div>
                                                <span id="name-preview">Category name</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paramètres -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Settings</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="sort_order" class="form-label">Display order</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                            <small class="form-text text-muted">Lower numbers appear first</small>
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Active category
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                                <i class="fa fa-times"></i> Cancel
                                            </a>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-save"></i> Create Category
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#slug').val(slug);
        
        // Update preview
        $('#name-preview').text(name || 'Nom de la catégorie');
    });

    // Update icon preview
    $('#icon').on('input', function() {
        const iconClass = $(this).val() || 'fas fa-tag';
        $('#icon-preview i').attr('class', iconClass + ' text-white');
    });

    // Update color preview
    $('#color').on('input', function() {
        const color = $(this).val();
        $('#icon-preview').css('background-color', color);
    });

    // Image preview
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    // Real-time validation functions
    function validateField(fieldId, validationRules) {
        const input = $(`#${fieldId}`);
        const value = input.val().trim();
        let isValid = true;
        let errorMessage = '';

        // Remove existing error messages
        input.removeClass('is-invalid');
        input.next('.invalid-feedback').remove();

        // Check validation rules
        if (validationRules.required && !value) {
            isValid = false;
            errorMessage = 'This field is required.';
        } else if (validationRules.maxLength && value.length > validationRules.maxLength) {
            isValid = false;
            errorMessage = `Maximum ${validationRules.maxLength} characters allowed.`;
        } else if (validationRules.minLength && value.length < validationRules.minLength) {
            isValid = false;
            errorMessage = `Minimum ${validationRules.minLength} characters required.`;
        } else if (validationRules.pattern && !validationRules.pattern.test(value)) {
            isValid = false;
            errorMessage = validationRules.patternMessage || 'Invalid format.';
        }

        if (!isValid) {
            input.addClass('is-invalid');
            if (!input.next('.invalid-feedback').length) {
                input.after(`<div class="invalid-feedback">${errorMessage}</div>`);
            }
        }

        return isValid;
    }

    // Real-time validation on blur
    $('#name').on('blur', function() {
        validateField('name', { required: true, maxLength: 255 });
    });

    $('#slug').on('blur', function() {
        const slugPattern = /^[a-z0-9-]+$/;
        validateField('slug', {
            maxLength: 255,
            pattern: slugPattern,
            patternMessage: 'Slug can only contain lowercase letters, numbers, and hyphens.'
        });
    });

    $('#description').on('blur', function() {
        validateField('description', { maxLength: 1000 });
    });

    $('#icon').on('blur', function() {
        const iconPattern = /^fa[srb]?\s+fa-[\w-]+$/;
        validateField('icon', {
            maxLength: 100,
            pattern: iconPattern,
            patternMessage: 'Please use valid Font Awesome class (e.g., fas fa-tag).'
        });
    });

    $('#color').on('blur', function() {
        const colorPattern = /^#[0-9A-Fa-f]{6}$/;
        validateField('color', {
            pattern: colorPattern,
            patternMessage: 'Please use valid hex color format (#000000).'
        });
    });

    $('#sort_order').on('blur', function() {
        const value = parseInt($(this).val());
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();

        if ($(this).val() && (isNaN(value) || value < 0)) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Sort order must be a positive number.</div>');
        }
    });

    // File validation
    $('#image').on('change', function() {
        const file = this.files[0];
        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();

        if (file) {
            // Check file size (2MB = 2097152 bytes)
            if (file.size > 2097152) {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">Image size must not exceed 2MB.</div>');
                return;
            }

            // Check file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                $(this).addClass('is-invalid');
                $(this).after('<div class="invalid-feedback">Please select a valid image file (JPG, PNG, GIF, WEBP).</div>');
                return;
            }

            // Show preview if valid
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    // Enhanced form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];

        // Clear previous validation states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Validate required fields
        const nameValue = $('#name').val().trim();
        if (!nameValue) {
            isValid = false;
            $('#name').addClass('is-invalid');
            $('#name').after('<div class="invalid-feedback">Category name is required.</div>');
            errorMessages.push('Category name is required');
        } else if (nameValue.length > 255) {
            isValid = false;
            $('#name').addClass('is-invalid');
            $('#name').after('<div class="invalid-feedback">Category name cannot exceed 255 characters.</div>');
            errorMessages.push('Category name too long');
        }

        // Validate slug if provided
        const slugValue = $('#slug').val().trim();
        if (slugValue) {
            const slugPattern = /^[a-z0-9-]+$/;
            if (!slugPattern.test(slugValue)) {
                isValid = false;
                $('#slug').addClass('is-invalid');
                $('#slug').after('<div class="invalid-feedback">Slug can only contain lowercase letters, numbers, and hyphens.</div>');
                errorMessages.push('Invalid slug format');
            } else if (slugValue.length > 255) {
                isValid = false;
                $('#slug').addClass('is-invalid');
                $('#slug').after('<div class="invalid-feedback">Slug cannot exceed 255 characters.</div>');
                errorMessages.push('Slug too long');
            }
        }

        // Validate description length
        const descriptionValue = $('#description').val().trim();
        if (descriptionValue && descriptionValue.length > 1000) {
            isValid = false;
            $('#description').addClass('is-invalid');
            $('#description').after('<div class="invalid-feedback">Description cannot exceed 1000 characters.</div>');
            errorMessages.push('Description too long');
        }

        // Validate icon format
        const iconValue = $('#icon').val().trim();
        if (iconValue) {
            const iconPattern = /^fa[srb]?\s+fa-[\w-]+$/;
            if (!iconPattern.test(iconValue)) {
                isValid = false;
                $('#icon').addClass('is-invalid');
                $('#icon').after('<div class="invalid-feedback">Please use valid Font Awesome class (e.g., fas fa-tag).</div>');
                errorMessages.push('Invalid icon format');
            }
        }

        // Validate color format
        const colorValue = $('#color').val().trim();
        if (colorValue) {
            const colorPattern = /^#[0-9A-Fa-f]{6}$/;
            if (!colorPattern.test(colorValue)) {
                isValid = false;
                $('#color').addClass('is-invalid');
                $('#color').after('<div class="invalid-feedback">Please use valid hex color format (#000000).</div>');
                errorMessages.push('Invalid color format');
            }
        }

        // Validate sort order
        const sortOrderValue = $('#sort_order').val();
        if (sortOrderValue) {
            const sortOrder = parseInt(sortOrderValue);
            if (isNaN(sortOrder) || sortOrder < 0) {
                isValid = false;
                $('#sort_order').addClass('is-invalid');
                $('#sort_order').after('<div class="invalid-feedback">Sort order must be a positive number.</div>');
                errorMessages.push('Invalid sort order');
            }
        }

        // Validate image file
        const imageFile = $('#image')[0].files[0];
        if (imageFile) {
            if (imageFile.size > 2097152) {
                isValid = false;
                $('#image').addClass('is-invalid');
                $('#image').after('<div class="invalid-feedback">Image size must not exceed 2MB.</div>');
                errorMessages.push('Image too large');
            }

            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(imageFile.type)) {
                isValid = false;
                $('#image').addClass('is-invalid');
                $('#image').after('<div class="invalid-feedback">Please select a valid image file (JPG, PNG, GIF, WEBP).</div>');
                errorMessages.push('Invalid image type');
            }
        }

        if (!isValid) {
            e.preventDefault();

            // Show toast notification
            showErrorToast('Please fix the validation errors before submitting the form.');

            // Scroll to first error
            const firstError = $('.is-invalid').first();
            if (firstError.length) {
                $('html, body').animate({
                    scrollTop: firstError.offset().top - 100
                }, 500);
                firstError.focus();
            }
        } else {
            // Add loading state to submit button
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();

            submitBtn.prop('disabled', true);
            submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating Category...');

            // Re-enable button after 10 seconds as fallback
            setTimeout(function() {
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }, 10000);
        }
    });

    // Toast notification function
    function showErrorToast(message) {
        // Create toast if it doesn't exist
        if (!$('#errorToast').length) {
            $('body').append(`
                <div class="toast-container position-fixed top-0 end-0 p-3">
                    <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header bg-danger text-white">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong class="me-auto">Validation Error</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body"></div>
                    </div>
                </div>
            `);
        }

        $('#errorToast .toast-body').text(message);
        const toast = new bootstrap.Toast(document.getElementById('errorToast'));
        toast.show();
    }
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Add character counter for description
    $('#description').on('input', function() {
        const maxLength = 1000;
        const currentLength = $(this).val().length;
        const remaining = maxLength - currentLength;

        let counterHtml = `<small class="form-text ${remaining < 50 ? 'text-danger' : 'text-muted'}">
            ${currentLength}/${maxLength} characters ${remaining < 0 ? '(exceeded by ' + Math.abs(remaining) + ')' : ''}
        </small>`;

        $(this).next('.char-counter').remove();
        $(this).after(`<div class="char-counter">${counterHtml}</div>`);
    });

    // Trigger character counter on page load
    $('#description').trigger('input');

    // Enhanced slug generation with validation
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#slug').val(slug);

        // Update preview
        $('#name-preview').text(name || 'Category name');

        // Validate name in real-time
        if (name.length > 255) {
            $('#name').addClass('is-invalid');
            $('#name').next('.invalid-feedback').remove();
            $('#name').after('<div class="invalid-feedback">Category name cannot exceed 255 characters.</div>');
        } else {
            $('#name').removeClass('is-invalid');
            $('#name').next('.invalid-feedback').remove();
        }
    });
});
</script>
@endpush

@push('styles')
<style>
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .invalid-feedback {
        display: block !important;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }

    .alert {
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
    }

    .alert-success {
        background-color: #d1edff;
        border-color: #bee5eb;
        color: #0c5460;
    }

    .form-control:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-control.is-invalid:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .char-counter {
        margin-top: 0.25rem;
    }

    .toast-container {
        z-index: 1055;
    }

    .form-label.required::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush
