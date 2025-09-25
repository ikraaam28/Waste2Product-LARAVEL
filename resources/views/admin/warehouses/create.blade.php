@extends('layouts.admin')
@section('title', 'Add Warehouse')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h3 class="fw-bold mb-1">Add a Warehouse</h3>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.warehouses.index') }}">Warehouses</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Add</a></li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-warehouse me-3 fs-4"></i>
                            <div>
                                <h4 class="card-title mb-0">Warehouse Information</h4>
                                <p class="mb-0 opacity-75">Fill in the details to create a new warehouse</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('admin.warehouses.store') }}" method="POST" id="warehouseForm">
                            @csrf
                            
                            <!-- Basic Information Section -->
                            <div class="section-header mb-4">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h5>
                            </div>
                            
                            <div class="row g-4">
                                <!-- Warehouse Name -->
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Warehouse Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-warehouse text-primary"></i></span>
                                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" 
                                               placeholder="Enter warehouse name" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Partner Selection -->
                                <div class="col-md-6">
                                    <label for="partner_id" class="form-label fw-semibold">Partner <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-handshake text-primary"></i></span>
                                        <select name="partner_id" id="partner_id" class="form-select" required>
                                            <option value="">Select a Partner</option>
                                            @foreach($partners as $partner)
                                                <option value="{{ $partner->id }}" {{ old('partner_id') == $partner->id ? 'selected' : '' }}>
                                                    {{ $partner->name }} - {{ $partner->type ?? 'No Type' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('partner_id')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Location Type -->
                                <div class="col-md-6">
                                    <label for="location_type" class="form-label fw-semibold">Location Type</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-map-marker-alt text-primary"></i></span>
                                        <select id="location_type" class="form-select" onchange="checkCustom(this, 'customLocation')">
                                            <option value="">Select location type</option>
                                            <option value="Industrial Zone">Industrial Zone</option>
                                            <option value="Commercial Area">Commercial Area</option>
                                            <option value="City Center">City Center</option>
                                            <option value="Suburban Area">Suburban Area</option>
                                            <option value="Port Area">Port Area</option>
                                            <option value="Airport Zone">Airport Zone</option>
                                            <option value="Other">Other (custom)</option>
                                        </select>
                                    </div>
                                    <input type="text" id="customLocation" class="form-control mt-2 d-none" placeholder="Enter custom location type">
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-circle text-primary"></i></span>
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Address Section -->
                            <div class="section-header mb-4 mt-5">
                                <h5 class="text-primary mb-3"><i class="fas fa-address-card me-2"></i>Address Information</h5>
                            </div>

                            <div class="row g-4">
                                <!-- Full Address -->
                                <div class="col-12">
                                    <label for="address" class="form-label fw-semibold">Full Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-map text-primary"></i></span>
                                        <textarea name="address" id="address" class="form-control" rows="2" 
                                                  placeholder="Enter complete address">{{ old('address') }}</textarea>
                                    </div>
                                </div>

                                <!-- City -->
                                <div class="col-md-4">
                                    <label for="city" class="form-label fw-semibold">City</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-city text-primary"></i></span>
                                        <select id="city_select" class="form-select" onchange="checkCustom(this, 'customCity')">
                                            <option value="">Select a city</option>
                                            <option value="Tunis">Tunis</option>
                                            <option value="Ariana">Ariana</option>
                                            <option value="Ben Arous">Ben Arous</option>
                                            <option value="Manouba">Manouba</option>
                                            <option value="Sfax">Sfax</option>
                                            <option value="Sousse">Sousse</option>
                                            <option value="Bizerte">Bizerte</option>
                                            <option value="Gabès">Gabès</option>
                                            <option value="Nabeul">Nabeul</option>
                                            <option value="Other">Other (custom)</option>
                                        </select>
                                    </div>
                                    <input type="text" id="customCity" class="form-control mt-2 d-none" placeholder="Enter custom city">
                                </div>

                                <!-- Postal Code -->
                                <div class="col-md-4">
                                    <label for="postal_code" class="form-label fw-semibold">Postal Code</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                        <input type="text" name="postal_code" id="postal_code" class="form-control" value="{{ old('postal_code') }}" 
                                               placeholder="e.g., 1000">
                                    </div>
                                </div>

                                <!-- Country -->
                                <div class="col-md-4">
                                    <label for="country" class="form-label fw-semibold">Country</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-globe text-primary"></i></span>
                                        <select id="country_select" class="form-select" onchange="checkCustom(this, 'customCountry')">
                                            <option value="Tunisia" selected>Tunisia</option>
                                            <option value="Algeria">Algeria</option>
                                            <option value="Libya">Libya</option>
                                            <option value="Morocco">Morocco</option>
                                            <option value="Other">Other (custom)</option>
                                        </select>
                                    </div>
                                    <input type="text" id="customCountry" class="form-control mt-2 d-none" placeholder="Enter custom country">
                                </div>
                            </div>

                            <!-- Capacity Section -->
                            <div class="section-header mb-4 mt-5">
                                <h5 class="text-primary mb-3"><i class="fas fa-chart-bar me-2"></i>Capacity Information</h5>
                            </div>

                            <div class="row g-4">
                                <!-- Total Capacity -->
                                <div class="col-md-6">
                                    <label for="capacity" class="form-label fw-semibold">Total Capacity (m³) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-arrows-alt text-primary"></i></span>
                                        <input type="number" name="capacity" id="capacity" class="form-control" 
                                               value="{{ old('capacity') }}" step="0.01" min="0" placeholder="0.00" required>
                                        <span class="input-group-text">m³</span>
                                    </div>
                                    @error('capacity')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Current Occupancy -->
                                <div class="col-md-6">
                                    <label for="current_occupancy" class="form-label fw-semibold">Current Occupancy (m³)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-boxes text-primary"></i></span>
                                        <input type="number" name="current_occupancy" id="current_occupancy" class="form-control" 
                                               value="{{ old('current_occupancy', 0) }}" step="0.01" min="0" placeholder="0.00">
                                        <span class="input-group-text">m³</span>
                                    </div>
                                    <div class="form-text">Leave as 0 if empty</div>
                                    @error('current_occupancy')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Capacity Visualization -->
                                <div class="col-12">
                                    <div class="capacity-visualization mt-3">
                                        <div class="d-flex justify-content-between mb-2">
                                            <small class="text-muted">Occupancy</small>
                                            <small class="text-muted" id="occupancyPercentage">0%</small>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div id="capacityBar" class="progress-bar bg-success" role="progressbar" style="width: 0%;"></div>
                                        </div>
                                        <div class="d-flex justify-content-between mt-1">
                                            <small class="text-muted" id="availableCapacity">Available: 0 m³</small>
                                            <small class="text-muted" id="usedCapacity">Used: 0 m³</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contact Information -->
                            <div class="section-header mb-4 mt-5">
                                <h5 class="text-primary mb-3"><i class="fas fa-user-tie me-2"></i>Contact Information</h5>
                            </div>

                            <div class="row g-4">
                                <!-- Contact Person -->
                                <div class="col-md-6">
                                    <label for="contact_person" class="form-label fw-semibold">Contact Person</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-user text-primary"></i></span>
                                        <input type="text" name="contact_person" id="contact_person" class="form-control" 
                                               value="{{ old('contact_person') }}" placeholder="Full name of contact person">
                                    </div>
                                </div>

                                <!-- Contact Phone -->
                                <div class="col-md-6">
                                    <label for="contact_phone" class="form-label fw-semibold">Contact Phone</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-phone text-primary"></i></span>
                                        <input type="text" name="contact_phone" id="contact_phone" class="form-control" 
                                               value="{{ old('contact_phone') }}" placeholder="e.g., 12 345 678"
                                               pattern="[0-9\s\+]{8,15}" title="Enter a valid phone number">
                                    </div>
                                    <div class="form-text">Format: 12 345 678 or +216 12 345 678</div>
                                </div>

                                <!-- Contact Email -->
                                <div class="col-12">
                                    <label for="contact_email" class="form-label fw-semibold">Contact Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-envelope text-primary"></i></span>
                                        <input type="email" name="contact_email" id="contact_email" class="form-control" 
                                               value="{{ old('contact_email') }}" placeholder="contact@example.com">
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="section-header mb-4 mt-5">
                                <h5 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i>Additional Information</h5>
                            </div>

                            <div class="row g-4">
                                <div class="col-12">
                                    <label for="description" class="form-label fw-semibold">Description</label>
                                    <textarea name="description" id="description" class="form-control" rows="4" 
                                              placeholder="Add any additional notes or description about this warehouse">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <!-- Hidden fields for custom values -->
                            <input type="hidden" name="location" id="locationField">
                            <input type="hidden" name="city" id="cityField">
                            <input type="hidden" name="country" id="countryField">

                            <!-- Form Actions -->
                            <div class="row mt-5">
                                <div class="col-12">
                                    <div class="d-flex justify-content-end gap-3">
                                        <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline-secondary px-4">
                                            <i class="fas fa-arrow-left me-2"></i>Cancel
                                        </a>
                                        <button type="submit" class="btn btn-primary px-4" onclick="return mergeCustomFields()">
                                            <i class="fas fa-plus me-2"></i>Create Warehouse
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.section-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}

.card {
    border-radius: 15px;
    overflow: hidden;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.input-group-text {
    border-radius: 8px 0 0 8px;
    background-color: #f8f9fa;
    border-right: none;
}

.form-control {
    border-left: none;
}

.capacity-visualization {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.progress {
    border-radius: 4px;
    background-color: #e9ecef;
}

.btn {
    border-radius: 8px;
    padding: 10px 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary {
    background: linear-gradient(135deg, #007bff, #0056b3);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
}

.form-label {
    color: #495057;
    margin-bottom: 0.5rem;
}

.text-primary {
    color: #007bff !important;
}

.custom-field {
    margin-top: 0.5rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const capacityInput = document.getElementById('capacity');
    const occupancyInput = document.getElementById('current_occupancy');
    const capacityBar = document.getElementById('capacityBar');
    const occupancyPercentage = document.getElementById('occupancyPercentage');
    const availableCapacity = document.getElementById('availableCapacity');
    const usedCapacity = document.getElementById('usedCapacity');

    function updateCapacityVisualization() {
        const capacity = parseFloat(capacityInput.value) || 0;
        const occupancy = parseFloat(occupancyInput.value) || 0;
        
        if (capacity > 0) {
            const percentage = Math.min((occupancy / capacity) * 100, 100);
            capacityBar.style.width = percentage + '%';
            occupancyPercentage.textContent = percentage.toFixed(1) + '%';
            
            // Update colors based on occupancy
            if (percentage >= 90) {
                capacityBar.className = 'progress-bar bg-danger';
            } else if (percentage >= 75) {
                capacityBar.className = 'progress-bar bg-warning';
            } else {
                capacityBar.className = 'progress-bar bg-success';
            }
        } else {
            capacityBar.style.width = '0%';
            occupancyPercentage.textContent = '0%';
        }
        
        availableCapacity.textContent = `Available: ${(capacity - occupancy).toFixed(2)} m³`;
        usedCapacity.textContent = `Used: ${occupancy.toFixed(2)} m³`;
    }

    // Event listeners for capacity inputs
    capacityInput.addEventListener('input', updateCapacityVisualization);
    occupancyInput.addEventListener('input', updateCapacityVisualization);
    
    // Initialize visualization
    updateCapacityVisualization();

    // Form validation for capacity
    document.getElementById('warehouseForm').addEventListener('submit', function(e) {
        const capacity = parseFloat(capacityInput.value) || 0;
        const occupancy = parseFloat(occupancyInput.value) || 0;
        
        if (occupancy > capacity) {
            e.preventDefault();
            alert('Current occupancy cannot exceed total capacity!');
            occupancyInput.focus();
        }
    });

    // Improved phone number formatting
    const phoneInput = document.getElementById('contact_phone');
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        // Only add +216 if it's not already there and the number starts with 2, 5, or 9
        if (value.length > 0 && !e.target.value.includes('+216')) {
            if (value.startsWith('2') || value.startsWith('5') || value.startsWith('9')) {
                if (value.length <= 8) {
                    value = '+216 ' + value;
                }
            }
        }
        
        // Format the number with spaces
        if (value.length > 4) {
            value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1 $2 $3');
        }
        
        e.target.value = value;
    });
});

// Function to handle custom fields
function checkCustom(select, inputId) {
    const input = document.getElementById(inputId);
    if (select.value === 'Other') {
        input.classList.remove('d-none');
        input.focus();
    } else {
        input.classList.add('d-none');
        input.value = '';
    }
}

// Merge custom fields before submit
function mergeCustomFields() {
    const form = document.querySelector('form');

    // Location
    const locationSelect = document.getElementById('location_type');
    const customLocation = document.getElementById('customLocation');
    let locationValue = locationSelect.value === 'Other' ? customLocation.value.trim() : locationSelect.value;
    document.getElementById('locationField').value = locationValue;

    // City
    const citySelect = document.getElementById('city_select');
    const customCity = document.getElementById('customCity');
    let cityValue = citySelect.value === 'Other' ? customCity.value.trim() : citySelect.value;
    document.getElementById('cityField').value = cityValue;

    // Country
    const countrySelect = document.getElementById('country_select');
    const customCountry = document.getElementById('customCountry');
    let countryValue = countrySelect.value === 'Other' ? customCountry.value.trim() : countrySelect.value;
    document.getElementById('countryField').value = countryValue;

    return true;
}
</script>
@endsection