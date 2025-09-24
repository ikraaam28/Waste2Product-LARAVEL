@extends('layouts.admin')
@section('title', 'Edit Event')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Edit Event</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Edit</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Event Information</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Event Title *</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title', $event->title) }}" required>
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="title-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category">Category *</label>
                                        <select class="form-control @error('category') is-invalid @enderror" 
                                                id="category" name="category" required>
                                            <option value="">Select a category</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->value }}" {{ old('category', $event->category) == $category->value ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="category-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description', $event->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="invalid-feedback" id="description-error"></div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="date">Date *</label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                               id="date" name="date" value="{{ old('date', $event->date ? \Carbon\Carbon::parse($event->date)->format('Y-m-d') : '') }}" required>
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="date-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="time">Heure *</label>
                                        <input type="time" class="form-control @error('time') is-invalid @enderror" 
                                               id="time" name="time" value="{{ old('time', $event->time ? \Carbon\Carbon::parse($event->time)->format('H:i') : '') }}" required>
                                        @error('time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="time-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="max_participants">Participants max</label>
                                        <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                               id="max_participants" name="max_participants" 
                                               value="{{ old('max_participants', $event->max_participants) }}" min="1">
                                        @error('max_participants')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="max_participants-error"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">City *</label>
                                        <select class="form-control @error('city') is-invalid @enderror" 
                                                id="city" name="city" required>
                                            <option value="">Select a city</option>
                                            @foreach(\App\Helpers\TunisiaCities::getCities() as $key => $city)
                                                <option value="{{ $city }}" {{ old('city', $event->city ?? '') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                            @endforeach
                                        </select>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="city-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location *</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location', $event->location) }}" required>
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="invalid-feedback" id="location-error"></div>
                                        <small class="form-text text-muted">Specific address or venue name</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="image">Image de l'événement</label>
                                @if($event->image)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" 
                                             class="img-thumbnail" style="max-width: 200px;">
                                        <br>
                                        <small class="text-muted">Image actuelle</small>
                                    </div>
                                @endif
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF (max 2MB)</small>
                            </div>

                            <div class="form-group">
                                <label>Related Products by Category</label>
                                <div class="row">
                                    @php
                                        $productCategories = \App\Models\ProductCategory::all();
                                    @endphp
                                    @foreach($productCategories as $productCategory)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0" style="color: {{ $productCategory->color }}">
                                                        <i class="{{ $productCategory->icon }}"></i> {{ $productCategory->name }}
                                                    </h6>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <button type="button" class="btn btn-outline-success btn-sm" 
                                                                onclick="selectAllProducts('category_{{ $productCategory->id }}')" 
                                                                title="Select All">
                                                            <i class="fa fa-check-square"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                                onclick="deselectAllProducts('category_{{ $productCategory->id }}')" 
                                                                title="Deselect All">
                                                            <i class="fa fa-square"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body" id="category_{{ $productCategory->id }}">
                                                    @foreach($products->where('category_id', $productCategory->id) as $product)
                                                        <div class="form-check">
                                                            <input class="form-check-input product-checkbox" type="checkbox" 
                                                                   name="products[]" value="{{ $product->id }}" 
                                                                   id="product_{{ $product->id }}"
                                                                   data-category="category_{{ $productCategory->id }}"
                                                                   {{ in_array($product->id, old('products', $event->products->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="product_{{ $product->id }}">
                                                                {{ $product->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Global Selection Controls -->
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0">Global Selection</h6>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-success btn-sm" onclick="selectAllProducts()">
                                                <i class="fa fa-check-square"></i> Select All Products
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" onclick="deselectAllProducts()">
                                                <i class="fa fa-square"></i> Deselect All Products
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group text-right">
                                <a href="{{ route('admin.events.manage') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Update Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    
    // Real-time validation for all fields
    const fields = [
        'title', 'category', 'description', 'date', 'time', 
        'max_participants', 'city', 'location'
    ];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // Validate on input/change
            field.addEventListener('input', () => validateField(fieldId));
            field.addEventListener('change', () => validateField(fieldId));
            field.addEventListener('blur', () => validateField(fieldId));
        }
    });
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous validation
        clearValidation();
        
        // Validate all fields
        let isValid = true;
        fields.forEach(fieldId => {
            if (!validateField(fieldId)) {
                isValid = false;
            }
        });
        
        if (isValid) {
            form.submit();
        }
    });
    
    function validateField(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return true;
        
        let isValid = true;
        let errorMessage = '';
        
        // Clear previous error
        clearFieldError(fieldId);
        
        switch(fieldId) {
            case 'title':
                if (field.value.length < 3) {
                    errorMessage = 'Title must be at least 3 characters';
                    isValid = false;
                } else if (field.value.length > 255) {
                    errorMessage = 'Title must be less than 255 characters';
                    isValid = false;
                }
                break;
                
            case 'category':
                if (!field.value) {
                    errorMessage = 'Please select a category';
                    isValid = false;
                }
                break;
                
            case 'description':
                if (field.value.length > 1000) {
                    errorMessage = 'Description must be less than 1000 characters';
                    isValid = false;
                }
                break;
                
            case 'date':
                if (!field.value) {
                    errorMessage = 'Date is required';
                    isValid = false;
                } else {
                    const selectedDate = new Date(field.value);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate < today) {
                        errorMessage = 'Date cannot be in the past';
                        isValid = false;
                    }
                }
                break;
                
            case 'time':
                if (!field.value) {
                    errorMessage = 'Time is required';
                    isValid = false;
                }
                break;
                
            case 'max_participants':
                if (field.value && (field.value < 1 || field.value > 1000)) {
                    errorMessage = 'Max participants must be between 1 and 1000';
                    isValid = false;
                }
                break;
                
            case 'city':
                if (!field.value) {
                    errorMessage = 'Please select a city';
                    isValid = false;
                }
                break;
                
            case 'location':
                if (!field.value.trim()) {
                    errorMessage = 'Location is required';
                    isValid = false;
                } else if (field.value.length > 255) {
                    errorMessage = 'Location must be less than 255 characters';
                    isValid = false;
                }
                break;
        }
        
        if (!isValid) {
            showFieldError(fieldId, errorMessage);
        }
        
        return isValid;
    }
    
    function showFieldError(fieldId, message) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        
        if (field && errorDiv) {
            field.classList.add('is-invalid');
            errorDiv.textContent = message;
        }
    }
    
    function clearFieldError(fieldId) {
        const field = document.getElementById(fieldId);
        const errorDiv = document.getElementById(fieldId + '-error');
        
        if (field && errorDiv) {
            field.classList.remove('is-invalid');
            errorDiv.textContent = '';
        }
    }
    
    function clearValidation() {
        const invalidFields = form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => {
            field.classList.remove('is-invalid');
        });
        
        const errorMessages = form.querySelectorAll('.invalid-feedback');
        errorMessages.forEach(error => {
            error.textContent = '';
        });
    }
});

// Product selection functions
function selectAllProducts(categoryId = null) {
    if (categoryId) {
        // Select all products in a specific category
        const checkboxes = document.querySelectorAll(`#${categoryId} .product-checkbox`);
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    } else {
        // Select all products across all categories
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    }
}

function deselectAllProducts(categoryId = null) {
    if (categoryId) {
        // Deselect all products in a specific category
        const checkboxes = document.querySelectorAll(`#${categoryId} .product-checkbox`);
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    } else {
        // Deselect all products across all categories
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    }
}
</script>
@endpush

@endsection

