@extends('layouts.admin')
@section('title', $warehouse->name . ' - Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h3 class="fw-bold mb-1">Warehouse Details</h3>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.warehouses.index') }}">Warehouses</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">{{ $warehouse->name }}</a></li>
            </ul>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header with Actions -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="avatar avatar-xl me-3">
                        <div class="avatar-title rounded-circle bg-primary bg-opacity-10 text-primary p-3">
                            <i class="fas fa-warehouse fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="mb-0 fw-bold">{{ $warehouse->name }}</h2>
                        <div class="d-flex align-items-center mt-1">
                            @if($warehouse->status == 'active')
                                <span class="badge bg-success me-2"><i class="fas fa-check-circle me-1"></i> Active</span>
                            @elseif($warehouse->status == 'inactive')
                                <span class="badge bg-secondary me-2"><i class="fas fa-pause-circle me-1"></i> Inactive</span>
                            @else
                                <span class="badge bg-warning me-2"><i class="fas fa-tools me-1"></i> Maintenance</span>
                            @endif
                            <span class="text-muted">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                {{ $warehouse->city ?? 'Unknown City' }}, {{ $warehouse->country ?? 'Unknown Country' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Warehouse
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="actionsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-cog me-2"></i>Actions
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="actionsDropdown">
                            <li><a class="dropdown-item" href="{{ route('admin.warehouses.index') }}"><i class="fas fa-list me-2"></i>Back to List</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                                    <i class="fas fa-trash me-2"></i>Delete Warehouse
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Left Column - Warehouse Information -->
            <div class="col-lg-8">
                <!-- Basic Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2 text-primary"></i>Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-muted">Warehouse Name</label>
                                <p class="fs-6 mb-0">{{ $warehouse->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-muted">Partner</label>
                                <div class="d-flex align-items-center">
                                    @if($warehouse->partner)
                                        <div class="avatar avatar-sm me-2">
                                            <div class="avatar-title rounded-circle bg-info bg-opacity-10 text-info p-2">
                                                <i class="fas fa-handshake"></i>
                                            </div>
                                        </div>
                                        <span>{{ $warehouse->partner->name }}</span>
                                    @else
                                        <span class="text-muted">No partner assigned</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-muted">Location Type</label>
                                <p class="mb-0">
                                    @if($warehouse->location)
                                        <span class="badge bg-light text-dark">{{ $warehouse->location }}</span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold text-muted">Status</label>
                                <p class="mb-0">
                                    @if($warehouse->status == 'active')
                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                    @elseif($warehouse->status == 'inactive')
                                        <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i> Inactive</span>
                                    @else
                                        <span class="badge bg-warning"><i class="fas fa-tools me-1"></i> Maintenance</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold text-muted">Description</label>
                                <p class="mb-0">
                                    @if($warehouse->description)
                                        {{ $warehouse->description }}
                                    @else
                                        <span class="text-muted">No description provided</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Capacity Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Capacity Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="text-primary mb-1">{{ $warehouse->capacity }} m³</h3>
                                    <small class="text-muted">Total Capacity</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="text-success mb-1">{{ $warehouse->current_occupancy }} m³</h3>
                                    <small class="text-muted">Current Occupancy</small>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="border rounded p-3">
                                    <h3 class="text-info mb-1">{{ $warehouse->available_capacity }} m³</h3>
                                    <small class="text-muted">Available Space</small>
                                </div>
                            </div>
                        </div>

                        <!-- Capacity Progress Bar -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-semibold">Storage Utilization</span>
                                <span class="fw-semibold">{{ $warehouse->occupancy_percentage }}%</span>
                            </div>
                            <div class="progress" style="height: 12px;">
                                <div class="progress-bar 
                                    @if($warehouse->occupancy_percentage >= 90) bg-danger
                                    @elseif($warehouse->occupancy_percentage >= 75) bg-warning
                                    @else bg-success @endif" 
                                    role="progressbar" 
                                    style="width: {{ $warehouse->occupancy_percentage }}%"
                                    aria-valuenow="{{ $warehouse->occupancy_percentage }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-1">
                                <small class="text-muted">0%</small>
                                <small class="text-muted">100%</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-address-card me-2 text-primary"></i>Address Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-semibold text-muted">Full Address</label>
                                <p class="mb-0">
                                    @if($warehouse->address)
                                        {{ $warehouse->address }}
                                    @else
                                        <span class="text-muted">No address provided</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold text-muted">City</label>
                                <p class="mb-0">{{ $warehouse->city ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold text-muted">Postal Code</label>
                                <p class="mb-0">{{ $warehouse->postal_code ?? 'Not specified' }}</p>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold text-muted">Country</label>
                                <p class="mb-0">{{ $warehouse->country ?? 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Sidebar Information -->
            <div class="col-lg-4">
                <!-- Contact Information Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-user-tie me-2 text-primary"></i>Contact Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Contact Person</label>
                            <p class="mb-0">
                                @if($warehouse->contact_person)
                                    <i class="fas fa-user me-2 text-muted"></i>{{ $warehouse->contact_person }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Contact Phone</label>
                            <p class="mb-0">
                                @if($warehouse->contact_phone)
                                    <i class="fas fa-phone me-2 text-muted"></i>{{ $warehouse->contact_phone }}
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold text-muted">Contact Email</label>
                            <p class="mb-0">
                                @if($warehouse->contact_email)
                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                    <a href="mailto:{{ $warehouse->contact_email }}">{{ $warehouse->contact_email }}</a>
                                @else
                                    <span class="text-muted">Not specified</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Quick Stats</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="text-muted">Created</span>
                            <span class="fw-semibold">{{ $warehouse->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="text-muted">Last Updated</span>
                            <span class="fw-semibold">{{ $warehouse->updated_at->format('M d, Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <span class="text-muted">Utilization</span>
                            <span class="fw-semibold">{{ $warehouse->occupancy_percentage }}%</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted">Available Space</span>
                            <span class="fw-semibold text-success">{{ $warehouse->available_capacity }} m³</span>
                        </div>
                    </div>
                </div>

                <!-- Status Actions Card -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0"><i class="fas fa-cogs me-2 text-primary"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($warehouse->status == 'active')
                                <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="inactive">
                                    <button type="submit" class="btn btn-warning btn-sm">
                                        <i class="fas fa-pause-circle me-2"></i>Set to Inactive
                                    </button>
                                </form>
                            @elseif($warehouse->status == 'inactive')
                                <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-play-circle me-2"></i>Activate Warehouse
                                    </button>
                                </form>
                            @endif
                            
                            @if($warehouse->status != 'maintenance')
                                <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="maintenance">
                                    <button type="submit" class="btn btn-info btn-sm">
                                        <i class="fas fa-tools me-2"></i>Set to Maintenance
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.warehouses.update', $warehouse->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check-circle me-2"></i>End Maintenance
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit me-2"></i>Edit Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the warehouse <strong>"{{ $warehouse->name }}"</strong>? This action cannot be undone.</p>
                <p class="text-danger"><small>All associated data will be permanently removed.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="modalDeleteForm" action="{{ route('admin.warehouses.destroy', $warehouse->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Warehouse</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-title {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-sm .avatar-title {
    width: 30px;
    height: 30px;
    font-size: 0.8rem;
}

.card {
    border-radius: 10px;
}

.badge {
    font-size: 0.75rem;
}

.progress {
    border-radius: 6px;
}

.btn-group .btn {
    border-radius: 0.375rem;
}

.alert {
    border-radius: 10px;
    border: none;
}

.border-bottom {
    border-color: #e9ecef !important;
}

.d-grid .btn {
    margin-bottom: 0.5rem;
}

.d-grid .btn:last-child {
    margin-bottom: 0;
}

.avatar {
    display: inline-flex;
}

.avatar-title i {
    font-size: inherit;
}
</style>

<script>
function confirmDelete() {
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});
</script>
@endsection