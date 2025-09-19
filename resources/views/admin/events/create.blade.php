@extends('layouts.admin')
@section('title', 'Create Event')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Create Event</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Create</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Event Information</div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label for="title">Event Title *</label>
                                        <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                               id="title" name="title" value="{{ old('title') }}" required minlength="3" maxlength="255">
                                        @error('title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Minimum 3 characters, maximum 255 characters</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="category">Category *</label>
                                        <select class="form-control @error('category') is-invalid @enderror" 
                                                id="category" name="category" required>
                                            <option value="">Select a category</option>
                                            <option value="Recycling" {{ old('category') == 'Recycling' ? 'selected' : '' }}>Recycling</option>
                                            <option value="Education" {{ old('category') == 'Education' ? 'selected' : '' }}>Education</option>
                                            <option value="Awareness" {{ old('category') == 'Awareness' ? 'selected' : '' }}>Awareness</option>
                                            <option value="Collection" {{ old('category') == 'Collection' ? 'selected' : '' }}>Collection</option>
                                            <option value="Workshop" {{ old('category') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                                        </select>
                                        @error('category')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" maxlength="1000">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Maximum 1000 characters</small>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="date">Date *</label>
                                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                                               id="date" name="date" value="{{ old('date') }}" required min="{{ date('Y-m-d') }}">
                                        @error('date')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="time">Time *</label>
                                        <input type="time" class="form-control @error('time') is-invalid @enderror" 
                                               id="time" name="time" value="{{ old('time') }}" required>
                                        @error('time')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="max_participants">Max Participants</label>
                                        <input type="number" class="form-control @error('max_participants') is-invalid @enderror" 
                                               id="max_participants" name="max_participants" 
                                               value="{{ old('max_participants') }}" min="1" max="1000">
                                        @error('max_participants')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="organizer_email">Organizer Email *</label>
                                        <input type="email" class="form-control @error('organizer_email') is-invalid @enderror" 
                                               id="organizer_email" name="organizer_email" value="{{ old('organizer_email') }}" required>
                                        @error('organizer_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                                                <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                            @endforeach
                                        </select>
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="location">Location *</label>
                                        <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                               id="location" name="location" value="{{ old('location') }}" required maxlength="255">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">Specific address or venue name</small>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="image">Event Image</label>
                                <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                       id="image" name="image" accept="image/*">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF (max 2MB)</small>
                            </div>

                            <div class="form-group">
                                <label>Products by Category</label>
                                <div class="row">
                                    @foreach($categories as $category)
                                        <div class="col-md-4">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h6 class="mb-0" style="color: {{ $category->color }}">
                                                        <i class="{{ $category->icon }}"></i> {{ $category->name }}
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($products->where('category_id', $category->id) as $product)
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="products[]" value="{{ $product->id }}" 
                                                                   id="product_{{ $product->id }}"
                                                                   {{ in_array($product->id, old('products', [])) ? 'checked' : '' }}>
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
                            </div>

                            <div class="form-group text-right">
                                <a href="{{ route('admin.events.manage') }}" class="btn btn-secondary">
                                    <i class="fa fa-arrow-left"></i> Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> Create Event
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
    const form = document.getElementById('eventForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Clear previous validation
        clearValidation();
        
        // Validate form
        if (validateForm()) {
            form.submit();
        }
    });
    
    function validateForm() {
        let isValid = true;
        
        // Title validation
        const title = document.getElementById('title');
        if (title.value.length < 3 || title.value.length > 255) {
            showError(title, 'Title must be between 3 and 255 characters');
            isValid = false;
        }
        
        // Category validation
        const category = document.getElementById('category');
        if (!category.value) {
            showError(category, 'Please select a category');
            isValid = false;
        }
        
        // Date validation
        const date = document.getElementById('date');
        const selectedDate = new Date(date.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            showError(date, 'Date cannot be in the past');
            isValid = false;
        }
        
        // Time validation
        const time = document.getElementById('time');
        if (!time.value) {
            showError(time, 'Please select a time');
            isValid = false;
        }
        
        // Email validation
        const email = document.getElementById('organizer_email');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!email.value || !emailRegex.test(email.value)) {
            showError(email, 'Please enter a valid email address');
            isValid = false;
        }
        
        // City validation
        const city = document.getElementById('city');
        if (!city.value) {
            showError(city, 'Please select a city');
            isValid = false;
        }
        
        // Location validation
        const location = document.getElementById('location');
        if (!location.value || location.value.length > 255) {
            showError(location, 'Location is required and must be less than 255 characters');
            isValid = false;
        }
        
        // Max participants validation
        const maxParticipants = document.getElementById('max_participants');
        if (maxParticipants.value && (maxParticipants.value < 1 || maxParticipants.value > 1000)) {
            showError(maxParticipants, 'Max participants must be between 1 and 1000');
            isValid = false;
        }
        
        // Description validation
        const description = document.getElementById('description');
        if (description.value.length > 1000) {
            showError(description, 'Description must be less than 1000 characters');
            isValid = false;
        }
        
        return isValid;
    }
    
    function showError(field, message) {
        field.classList.add('is-invalid');
        let errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback';
            field.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
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
</script>
@endpush
@endsection
