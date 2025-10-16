@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">User Management</h3>
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
                    <a href="#">Users</a>
                </li>
            </ul>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">User List</h4>
                            <div class="ms-auto">
                                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-round ms-auto">
                                    <i class="fa fa-plus"></i>
                                    Add User
                                </a>
                                <a href="{{ route('admin.users.export') }}" class="btn btn-success btn-round ms-2">
                                    <i class="fa fa-download"></i>
                                    Export CSV
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Search and Filter Form -->
                        <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="search">Search</label>
                                        <input type="text" class="form-control" id="search" name="search" 
                                               value="{{ request('search') }}" 
                                               placeholder="Name, email, phone, city...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="role_filter">Role</label>
                                        <select class="form-control" id="role_filter" name="role_filter">
                                            <option value="">All roles</option>
                                            <option value="admin" {{ request('role_filter') == 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>
                                            <option value="user" {{ request('role_filter') == 'user' ? 'selected' : '' }}>
                                                User
                                            </option>
                                            <option value="supplier" {{ request('role_filter') == 'supplier' ? 'selected' : '' }}>
                                                Supplier
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="status_filter">Status</label>
                                        <select class="form-control" id="status_filter" name="status_filter">
                                            <option value="">All statuses</option>
                                            <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>
                                                Active
                                            </option>
                                            <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>
                                                Inactive
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="filter">Account Type</label>
                                        <select class="form-control" id="filter" name="filter">
                                            <option value="">All types</option>
                                            <option value="google" {{ request('filter') == 'google' ? 'selected' : '' }}>
                                                Google Accounts
                                            </option>
                                            <option value="email" {{ request('filter') == 'email' ? 'selected' : '' }}>
                                                Email Accounts
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="sort">Sort by</label>
                                        <select class="form-control" id="sort" name="sort">
                                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>
                                                Created date
                                            </option>
                                            <option value="first_name" {{ request('sort') == 'first_name' ? 'selected' : '' }}>
                                                First name
                                            </option>
                                            <option value="last_name" {{ request('sort') == 'last_name' ? 'selected' : '' }}>
                                                Last name
                                            </option>
                                            <option value="email" {{ request('sort') == 'email' ? 'selected' : '' }}>
                                                Email
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-primary btn-sm me-2">
                                                <i class="fa fa-search"></i>
                                            </button>
                                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                                                <i class="fa fa-refresh"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- Users Statistics -->
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                    <i class="fas fa-users"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Total Users</p>
                                                    <h4 class="card-title">{{ $users->total() }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Verified</p>
                                                    <h4 class="card-title">{{ \App\Models\User::whereNotNull('email_verified_at')->count() }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                    <i class="fas fa-crown"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Admins</p>
                                                    <h4 class="card-title">{{ \App\Models\User::where('role', 'admin')->count() }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-success bubble-shadow-small">
                                                    <i class="fas fa-store"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category">Suppliers</p>
                                                    <h4 class="card-title">{{ \App\Models\User::where('role', 'supplier')->count() }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Users Table -->
                        <div class="table-responsive">
                            <table id="users-table" class="display table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th>Phone</th>
                                        <th>City</th>
                                        <th>Account Type</th>
                                        <th>Created</th>
                                        <th style="width: 12%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr>
                                            <td>
                                                @if($user->profile_picture)
                                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                         alt="Profile" class="avatar avatar-sm rounded-circle">
                                                @elseif($user->avatar)
                                                    <img src="{{ $user->avatar }}" 
                                                         alt="Profile" class="avatar avatar-sm rounded-circle">
                                                @else
                                                    <div class="avatar avatar-sm">
                                                        <span class="avatar-title rounded-circle bg-primary">
                                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $user->full_name }}</strong>
                                                @if($user->newsletter_subscription)
                                                    <span class="badge badge-info ms-1">Newsletter</span>
                                                @endif
                                                @if($user->company_name)
                                                    <br><small class="text-muted">{{ $user->company_name }}</small>
                                                @endif
                                            </td>
                                            <td>{{ $user->email }}</td>
                                            <td>
                                                <span class="badge badge-{{ $user->role_badge_color }}">
                                                    <i class="{{ $user->role_icon }}"></i> {{ $user->role_label }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($user->is_active)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-check-circle"></i> Active
                                                    </span>
                                                @else
                                                    <span class="badge badge-danger">
                                                        <i class="fas fa-times-circle"></i> Inactive
                                                    </span>
                                                @endif
                                                @if($user->email_verified_at)
                                                    <br><span class="badge badge-success mt-1">Email Verified</span>
                                                @else
                                                    <br><span class="badge badge-warning mt-1">Email Not Verified</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->phone ?? '-' }}</td>
                                            <td>{{ $user->city ?? '-' }}</td>
                                            <td>
                                                @if($user->google_id)
                                                    <span class="badge badge-success">
                                                        <i class="fab fa-google"></i> Google
                                                    </span>
                                                @else
                                                    <span class="badge badge-primary">
                                                        <i class="fas fa-envelope"></i> Email
                                                    </span>
                                                @endif
                                            </td>
                                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <div class="form-button-action">
                                                    <a href="{{ route('admin.users.show', $user) }}" 
                                                       class="btn btn-link btn-primary btn-lg" 
                                                       data-bs-toggle="tooltip" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.users.edit', $user) }}"
                                                       class="btn btn-link btn-primary btn-lg"
                                                       data-bs-toggle="tooltip" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.users.toggle-status', $user) }}"
                                                          method="POST" style="display: inline-block;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-link btn-{{ $user->is_active ? 'warning' : 'success' }}"
                                                                data-bs-toggle="tooltip"
                                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.users.destroy', $user) }}"
                                                          method="POST" style="display: inline-block;"
                                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link btn-danger"
                                                                data-bs-toggle="tooltip" title="Delete">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <div class="py-4">
                                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                                    <h5 class="text-muted">No users found</h5>
                                                    <p class="text-muted">Start by adding your first user.</p>
                                                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                                        <i class="fa fa-plus"></i> Add User
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        @if($users->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $users->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        $('[data-bs-toggle="tooltip"]').tooltip();
        
        // Auto-submit search form on filter change
        $('#filter, #sort').change(function() {
            $(this).closest('form').submit();
        });
    });
</script>
@endpush
