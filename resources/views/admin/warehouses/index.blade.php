@extends('layouts.admin')
@section('title', 'Warehouses')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h3 class="fw-bold mb-1">Warehouses Management</h3>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Warehouses</a></li>
            </ul>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>Please fix the following errors:
                <ul class="mb-0 mt-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header with Stats and Actions -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="me-4">
                        <h4 class="mb-0 fw-bold">All Warehouses</h4>
                        <p class="text-muted mb-0">Manage your warehouse inventory and locations</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Warehouse
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Warehouses</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'active']) }}">Active</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'inactive']) }}">Inactive</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'maintenance']) }}">Maintenance</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-primary card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-warehouse"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Total Warehouses</p>
                                    <h4 class="card-title">{{ $warehouses->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-success card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Active</p>
                                    <h4 class="card-title">{{ $warehouses->where('status', 'active')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-warning card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-tools"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Maintenance</p>
                                    <h4 class="card-title">{{ $warehouses->where('status', 'maintenance')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-danger card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-pause-circle"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Inactive</p>
                                    <h4 class="card-title">{{ $warehouses->where('status', 'inactive')->count() }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Warehouses Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-warehouse me-3 fs-4"></i>
                                <div>
                                    <h4 class="card-title mb-0">Warehouses List</h4>
                                    <p class="mb-0 opacity-75">All warehouses with their details and status</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="input-group input-group-sm me-2" style="width: 250px;">
                                    <input type="text" class="form-control" placeholder="Search warehouses..." id="searchInput">
                                    <button class="btn btn-outline-light" type="button" id="searchButton">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($warehouses->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="warehousesTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Name</th>
                                            <th>Partner</th>
                                            <th>Location</th>
                                            <th>Capacity</th>
                                            <th>Occupancy</th>
                                            <th>Status</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($warehouses as $warehouse)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <div class="avatar-title rounded-circle bg-primary bg-opacity-10 text-primary">
                                                                <i class="fas fa-warehouse"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $warehouse->name }}</h6>
                                                            <small class="text-muted">{{ $warehouse->city ?? 'No city' }}, {{ $warehouse->country ?? 'No country' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($warehouse->partner)
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm me-2">
                                                                <div class="avatar-title rounded-circle bg-info bg-opacity-10 text-info">
                                                                    <i class="fas fa-handshake"></i>
                                                                </div>
                                                            </div>
                                                            <span>{{ $warehouse->partner->name }}</span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No partner</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($warehouse->location)
                                                        <span class="badge bg-light text-dark">{{ $warehouse->location }}</span>
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress me-2" style="width: 60px; height: 6px;">
                                                            <div class="progress-bar 
                                                                @if($warehouse->occupancy_percentage >= 90) bg-danger
                                                                @elseif($warehouse->occupancy_percentage >= 75) bg-warning
                                                                @else bg-success @endif" 
                                                                role="progressbar" 
                                                                style="width: {{ $warehouse->occupancy_percentage }}%">
                                                            </div>
                                                        </div>
                                                        <small>{{ $warehouse->capacity }} m³</small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <strong>{{ $warehouse->current_occupancy }} m³</strong>
                                                        <div class="text-muted small">{{ $warehouse->occupancy_percentage }}% used</div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($warehouse->status == 'active')
                                                        <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Active</span>
                                                    @elseif($warehouse->status == 'inactive')
                                                        <span class="badge bg-secondary"><i class="fas fa-pause-circle me-1"></i> Inactive</span>
                                                    @else
                                                        <span class="badge bg-warning"><i class="fas fa-tools me-1"></i> Maintenance</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.warehouses.show', $warehouse->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           data-bs-toggle="tooltip" 
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.warehouses.edit', $warehouse->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary" 
                                                           data-bs-toggle="tooltip" 
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-warehouse" 
                                                                data-id="{{ $warehouse->id }}"
                                                                data-name="{{ $warehouse->name }}"
                                                                data-bs-toggle="tooltip" 
                                                                title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-warehouse fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Warehouses Found</h4>
                                <p class="text-muted">Get started by adding your first warehouse.</p>
                                <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Add Warehouse
                                </a>
                            </div>
                        @endif
                    </div>
                    @if($warehouses->count() > 0)
                        <div class="card-footer bg-light py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing <strong>{{ $warehouses->count() }}</strong> of <strong>{{ $warehouses->count() }}</strong> warehouses
                                </div>
                                <!-- Pagination would go here if needed -->
                            </div>
                        </div>
                    @endif
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
                <p>Are you sure you want to delete <strong id="warehouseName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Warehouse</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card-stats .icon-big {
    font-size: 2.5rem;
    opacity: 0.7;
}

.avatar-title {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75rem;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.progress {
    background-color: #e9ecef;
}

.card-round {
    border-radius: 15px;
}

.alert {
    border-radius: 10px;
    border: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Delete warehouse confirmation
    var deleteButtons = document.querySelectorAll('.delete-warehouse');
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    var warehouseName = document.getElementById('warehouseName');
    var deleteForm = document.getElementById('deleteForm');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            
            warehouseName.textContent = name;
            deleteForm.action = '/admin/warehouses/' + id;
            
            deleteModal.show();
        });
    });

    // Search functionality
    var searchInput = document.getElementById('searchInput');
    var searchButton = document.getElementById('searchButton');
    var table = document.getElementById('warehousesTable');
    
    if (table) {
        var rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        function filterTable() {
            var filter = searchInput.value.toLowerCase();
            
            for (var i = 0; i < rows.length; i++) {
                var cells = rows[i].getElementsByTagName('td');
                var found = false;
                
                for (var j = 0; j < cells.length; j++) {
                    var cellText = cells[j].textContent || cells[j].innerText;
                    if (cellText.toLowerCase().indexOf(filter) > -1) {
                        found = true;
                        break;
                    }
                }
                
                if (found) {
                    rows[i].style.display = '';
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }
        
        searchButton.addEventListener('click', filterTable);
        searchInput.addEventListener('keyup', filterTable);
    }
});
</script>
@endsection