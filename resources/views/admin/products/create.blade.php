@extends('layouts.admin')

@section('title', 'Create Product')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Create Product</h3>
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
                <a href="{{ route('admin.products.index') }}">Products</a>
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

    <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <!-- Informations principales -->
            <div class="col-md-8">
                <!-- Basic information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
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
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                            <small class="form-text text-muted">Shown in product lists</small>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Price and Stock -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Price and Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price') }}" 
                                               step="0.01" min="0" required>
                                        <span class="input-group-text">€</span>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="compare_price" class="form-label">Compare-at Price</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('compare_price') is-invalid @enderror" 
                                               id="compare_price" name="compare_price" value="{{ old('compare_price') }}" 
                                               step="0.01" min="0">
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <small class="form-text text-muted">Strikethrough price for promotions</small>
                                    @error('compare_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sku" class="form-label">SKU</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku') }}">
                                    <small class="form-text text-muted">Leave blank to auto-generate</small>
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock_quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', 0) }}" 
                                           min="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="manage_stock" name="manage_stock" 
                                   {{ old('manage_stock', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="manage_stock">
                                Manage stock automatically
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Environmental Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Environmental Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="materials" class="form-label">Materials Used</label>
                            <textarea class="form-control @error('materials') is-invalid @enderror" 
                                      id="materials" name="materials" rows="3">{{ old('materials') }}</textarea>
                            <small class="form-text text-muted">Ex: Recycled plastic, Reclaimed metal, etc.</small>
                            @error('materials')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="recycling_process" class="form-label">Recycling Process</label>
                            <textarea class="form-control @error('recycling_process') is-invalid @enderror" 
                                      id="recycling_process" name="recycling_process" rows="4">{{ old('recycling_process') }}</textarea>
                            <small class="form-text text-muted">Describe how this product was made from waste</small>
                            @error('recycling_process')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="environmental_impact_score" class="form-label">Environmental Impact Score</label>
                                    <input type="range" class="form-range" id="environmental_impact_score" 
                                           name="environmental_impact_score" min="1" max="100" 
                                           value="{{ old('environmental_impact_score', 50) }}">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">1 (Low)</small>
                                        <span id="score-value" class="badge badge-primary">50</span>
                                        <small class="text-muted">100 (Excellent)</small>
                                    </div>
                                    @error('environmental_impact_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weight" class="form-label">Weight</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                               id="weight" name="weight" value="{{ old('weight') }}" 
                                               step="0.01" min="0">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                                    <label for="dimensions" class="form-label">Dimensions</label>
                            <input type="text" class="form-control @error('dimensions') is-invalid @enderror" 
                                   id="dimensions" name="dimensions" value="{{ old('dimensions') }}" 
                                   placeholder="L x W x H (cm)">
                            @error('dimensions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Eco Certifications</label>
                            <div class="row">
                                @php
                                    $certifications = ['eco_label', 'recyclable', 'biodegradable', 'energy_star', 'fair_trade', 'organic'];
                                    $certificationLabels = [
                                        'eco_label' => 'Eco Label',
                                        'recyclable' => 'Recyclable',
                                        'biodegradable' => 'Biodegradable',
                                        'energy_star' => 'Energy Star',
                                        'fair_trade' => 'Fair Trade',
                                        'organic' => 'Organic'
                                    ];
                                @endphp
                                @foreach($certifications as $cert)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="cert_{{ $cert }}" name="certifications[]" value="{{ $cert }}"
                                               {{ in_array($cert, old('certifications', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cert_{{ $cert }}">
                                            {{ $certificationLabels[$cert] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags') }}" 
                                   placeholder="recycling, eco-friendly, sustainable (comma-separated)">
                            <small class="form-text text-muted">Keywords to improve search</small>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Publishing -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Publishing</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="published_at" class="form-label">Publish Date</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                   id="published_at" name="published_at" value="{{ old('published_at') }}">
                            <small class="form-text text-muted">Leave empty to publish immediately if active</small>
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active product
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Featured product
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Product Images</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="images" class="form-label">Main Images</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (max: 2MB each)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gallery" class="form-label">Gallery Images</label>
                            <input type="file" class="form-control @error('gallery.*') is-invalid @enderror" 
                                   id="gallery" name="gallery[]" accept="image/*" multiple>
                            <small class="form-text text-muted">Additional images for the gallery</small>
                            @error('gallery.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prévisualisation des images -->
                        <div id="images-preview" class="mt-3"></div>
                        <div id="gallery-preview" class="mt-3"></div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Create Product
                            </button>
                                <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

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

    .required-field::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush

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
    });

    // Update environmental score display
    $('#environmental_impact_score').on('input', function() {
        const score = $(this).val();
        $('#score-value').text(score);

        // Update badge color based on score
        $('#score-value').removeClass('badge-danger badge-warning badge-primary badge-info badge-success');
        if (score >= 80) {
            $('#score-value').addClass('badge-success');
        } else if (score >= 60) {
            $('#score-value').addClass('badge-info');
        } else if (score >= 40) {
            $('#score-value').addClass('badge-primary');
        } else if (score >= 20) {
            $('#score-value').addClass('badge-warning');
        } else {
            $('#score-value').addClass('badge-danger');
        }
    });

    // Convert tags input to array
    $('#tags').on('blur', function() {
        const tags = $(this).val().split(',').map(tag => tag.trim()).filter(tag => tag);
        $(this).val(tags.join(', '));
    });

    // Images preview
    $('#images').on('change', function() {
        previewImages(this, '#images-preview', 'Main Images');
    });

    $('#gallery').on('change', function() {
        previewImages(this, '#gallery-preview', 'Gallery');
    });

    function previewImages(input, container, title) {
        const files = input.files;
        const preview = $(container);
        preview.empty();

        if (files.length > 0) {
            preview.append(`<h6>${title}</h6>`);
            const row = $('<div class="row"></div>');

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = $(`
                            <div class="col-6 mb-2">
                                <img src="${e.target.result}" alt="Preview ${index + 1}"
                                     class="img-fluid rounded" style="max-height: 100px; object-fit: cover;">
                                <small class="d-block text-muted">${file.name}</small>
                            </div>
                        `);
                        row.append(col);
                    };
                    reader.readAsDataURL(file);
                }
            });

            preview.append(row);
        }
    }

    // Real-time validation
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
        } else if (validationRules.min && parseFloat(value) < validationRules.min) {
            isValid = false;
            errorMessage = `Value must be at least ${validationRules.min}.`;
        } else if (validationRules.max && parseFloat(value) > validationRules.max) {
            isValid = false;
            errorMessage = `Value must not exceed ${validationRules.max}.`;
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
        validateField('name', { required: true });
    });

    $('#description').on('blur', function() {
        validateField('description', { required: true });
    });

    $('#price').on('blur', function() {
        validateField('price', { required: true, min: 0 });
    });

    $('#stock_quantity').on('blur', function() {
        validateField('stock_quantity', { required: true, min: 0 });
    });

    $('#category_id').on('change', function() {
        validateField('category_id', { required: true });
    });

    // Compare price validation
    $('#compare_price').on('blur', function() {
        const price = parseFloat($('#price').val());
        const comparePrice = parseFloat($(this).val());

        $(this).removeClass('is-invalid');
        $(this).next('.invalid-feedback').remove();

        if (comparePrice && comparePrice <= price) {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">Compare-at price must be greater than the regular price.</div>');
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;
        let errorMessages = [];

        // Clear previous validation states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        // Check required fields
        const requiredFields = [
            { id: 'name', label: 'Product Name' },
            { id: 'description', label: 'Description' },
            { id: 'price', label: 'Price' },
            { id: 'stock_quantity', label: 'Stock Quantity' },
            { id: 'category_id', label: 'Category' }
        ];

        requiredFields.forEach(field => {
            const input = $(`#${field.id}`);
            const value = input.val();

            if (!value || value.toString().trim() === '') {
                isValid = false;
                input.addClass('is-invalid');
                input.after(`<div class="invalid-feedback">${field.label} is required.</div>`);
                errorMessages.push(`${field.label} is required`);
            }
        });

        // Validate numeric fields
        const price = parseFloat($('#price').val());
        const stockQuantity = parseInt($('#stock_quantity').val());
        const comparePrice = parseFloat($('#compare_price').val());

        if (price < 0) {
            isValid = false;
            $('#price').addClass('is-invalid');
            $('#price').after('<div class="invalid-feedback">Price must be a positive number.</div>');
            errorMessages.push('Price must be positive');
        }

        if (stockQuantity < 0) {
            isValid = false;
            $('#stock_quantity').addClass('is-invalid');
            $('#stock_quantity').after('<div class="invalid-feedback">Stock quantity must be a positive number.</div>');
            errorMessages.push('Stock quantity must be positive');
        }

        // Check price comparison
        if (comparePrice && comparePrice <= price) {
            isValid = false;
            $('#compare_price').addClass('is-invalid');
            $('#compare_price').after('<div class="invalid-feedback">Compare-at price must be greater than the regular price.</div>');
            errorMessages.push('Compare-at price must be greater than regular price');
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

    // Initialize score display
    $('#environmental_impact_score').trigger('input');

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);

    // Add loading state to submit button
    $('form').on('submit', function() {
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();

        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Creating Product...');

        // Re-enable button after 10 seconds as fallback
        setTimeout(function() {
            submitBtn.prop('disabled', false);
            submitBtn.html(originalText);
        }, 10000);
    });

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
});
</script>
@endpush
