@extends('layouts.admin')
@section('title', 'Partners Management')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4">
            <h3 class="fw-bold mb-1">Partners Management</h3>
            <ul class="breadcrumbs mb-0">
                <li class="nav-home">
                    <a href="{{ route('admin.dashboard') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Partners</a></li>
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
                        <h4 class="mb-0 fw-bold">All Partners</h4>
                        <p class="text-muted mb-0">Manage your business partners and their information</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.partners.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add Partner
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => '']) }}">All Partners</a></li>
                            <li><hr class="dropdown-divider"></li>
                            @foreach($types as $type)
                                <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['type' => $type]) }}">{{ $type }}</a></li>
                            @endforeach
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
                                    <i class="fas fa-handshake"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Total Partners</p>
                                    <h4 class="card-title">{{ $partners->count() }}</h4>
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
                                    <i class="fas fa-building"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">With Warehouses</p>
                                    <h4 class="card-title">{{ $partnersWithWarehouses }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card card-stats card-info card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="fas fa-envelope"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">With Email</p>
                                    <h4 class="card-title">{{ $partnersWithEmail }}</h4>
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
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">With Address</p>
                                    <h4 class="card-title">{{ $partnersWithAddress }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Partners Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-handshake me-3 fs-4"></i>
                                <div>
                                    <h4 class="card-title mb-0">Partners List</h4>
                                    <p class="mb-0 opacity-75">All partners with their contact information</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="input-group input-group-sm me-2" style="width: 250px;">
                                    <input type="text" class="form-control" placeholder="Search partners..." id="searchInput">
                                    <button class="btn btn-outline-light" type="button" id="searchButton">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        @if($partners->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="partnersTable">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4">Partner</th>
                                            <th>Contact Information</th>
                                            <th>Type</th>
                                            <th>Address</th>
                                            <th>Warehouses</th>
                                            <th class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($partners as $partner)
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-3">
                                                            <div class="avatar-title rounded-circle bg-primary bg-opacity-10 text-primary">
                                                                <i class="fas fa-handshake"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $partner->name }}</h6>
                                                            <small class="text-muted">ID: {{ $partner->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        @if($partner->email)
                                                            <div class="d-flex align-items-center mb-1">
                                                                <i class="fas fa-envelope text-muted me-2"></i>
                                                                <a href="mailto:{{ $partner->email }}" class="text-decoration-none">{{ $partner->email }}</a>
                                                            </div>
                                                        @endif
                                                        @if($partner->phone)
                                                            <div class="d-flex align-items-center">
                                                                <i class="fas fa-phone text-muted me-2"></i>
                                                                <span>{{ $partner->phone }}</span>
                                                            </div>
                                                        @endif
                                                        @if(!$partner->email && !$partner->phone)
                                                            <span class="text-muted">No contact info</span>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($partner->type)
                                                        <span class="badge bg-primary">{{ $partner->type }}</span>
                                                    @else
                                                        <span class="text-muted">Not specified</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($partner->address)
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-map-marker-alt text-muted me-2"></i>
                                                            <span class="text-truncate" style="max-width: 200px;" title="{{ $partner->address }}">
                                                                {{ Str::limit($partner->address, 30) }}
                                                            </span>
                                                        </div>
                                                    @else
                                                        <span class="text-muted">Not provided</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-2">
                                                            <span class="badge bg-info">{{ $partner->warehouses_count ?? 0 }}</span>
                                                        </div>
                                                        <span class="text-muted small">warehouses</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('admin.partners.show', $partner->id) }}" 
                                                           class="btn btn-sm btn-outline-primary" 
                                                           data-bs-toggle="tooltip" 
                                                           title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('admin.partners.edit', $partner->id) }}" 
                                                           class="btn btn-sm btn-outline-secondary" 
                                                           data-bs-toggle="tooltip" 
                                                           title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-sm btn-outline-danger delete-partner" 
                                                                data-id="{{ $partner->id }}"
                                                                data-name="{{ $partner->name }}"
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
                                    <i class="fas fa-handshake fa-4x text-muted"></i>
                                </div>
                                <h4 class="text-muted">No Partners Found</h4>
                                <p class="text-muted">Get started by adding your first partner.</p>
                                <a href="{{ route('admin.partners.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus me-2"></i>Add Partner
                                </a>
                            </div>
                        @endif
                    </div>
                    @if($partners->count() > 0)
                        <div class="card-footer bg-light py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-muted">
                                    Showing <strong>{{ $partners->count() }}</strong> of <strong>{{ $partners->count() }}</strong> partners
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
                <p>Are you sure you want to delete <strong id="partnerName"></strong>? This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Partner</button>
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

.card-round {
    border-radius: 15px;
}

.alert {
    border-radius: 10px;
    border: none;
}

.text-truncate {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Delete partner confirmation
    var deleteButtons = document.querySelectorAll('.delete-partner');
    var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    var partnerName = document.getElementById('partnerName');
    var deleteForm = document.getElementById('deleteForm');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            var id = this.getAttribute('data-id');
            var name = this.getAttribute('data-name');
            
            partnerName.textContent = name;
            deleteForm.action = '/admin/partners/' + id;
            
            deleteModal.show();
        });
    });

    // Search functionality
    var searchInput = document.getElementById('searchInput');
    var searchButton = document.getElementById('searchButton');
    var table = document.getElementById('partnersTable');
    
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