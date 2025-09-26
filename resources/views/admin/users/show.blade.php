@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">User Details</h3>
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
                    <a href="{{ route('admin.users.index') }}">Users</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">{{ $user->full_name }}</a>
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

        <div class="row">
            <!-- User Profile Card -->
            <div class="col-md-4">
                <div class="card card-profile">
                    <div class="card-header profile-header">
                        <div class="profile-picture">
                            <div class="avatar avatar-xl">
                                @if($user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                         alt="Profile" class="avatar-img rounded-circle">
                                @elseif($user->avatar)
                                    <img src="{{ $user->avatar }}" 
                                         alt="Profile" class="avatar-img rounded-circle">
                                @else
                                    <span class="avatar-title rounded-circle bg-primary text-white" style="font-size: 2rem;">
                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="user-profile text-center">
                            <div class="name">{{ $user->full_name }}</div>
                            <div class="job">{{ $user->email }}</div>
                            <div class="desc">
                                <span class="badge badge-{{ $user->role_badge_color }} mb-2">
                                    <i class="{{ $user->role_icon }}"></i> {{ $user->role_label }}
                                </span>
                                <br>
                                @if($user->is_active)
                                    <span class="badge badge-success mb-2">
                                        <i class="fas fa-check-circle"></i> Active Account
                                    </span>
                                @else
                                    <span class="badge badge-danger mb-2">
                                        <i class="fas fa-times-circle"></i> Inactive Account
                                    </span>
                                @endif
                                <br>
                                @if($user->google_id)
                                    <span class="badge badge-success mb-2">
                                        <i class="fab fa-google"></i> Google Account
                                    </span>
                                @else
                                    <span class="badge badge-primary mb-2">
                                        <i class="fas fa-envelope"></i> Email Account
                                    </span>
                                @endif
                                <br>
                                @if($user->email_verified_at)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check-circle"></i> Email Verified
                                    </span>
                                @else
                                    <span class="badge badge-warning">
                                        <i class="fas fa-exclamation-circle"></i> Email Not Verified
                                    </span>
                                @endif
                            </div>
                            <div class="social-media mt-3">
                                <a class="btn btn-info btn-twitter btn-sm btn-link" href="{{ route('admin.users.edit', $user) }}">
                                    <span class="btn-label just-icon"><i class="fa fa-edit"></i></span>
                                </a>
                                <form action="{{ route('admin.users.toggle-verification', $user) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning btn-sm btn-link"
                                            data-bs-toggle="tooltip"
                                            title="{{ $user->email_verified_at ? 'Mark as unverified' : 'Mark as verified' }}">
                                        <span class="btn-label just-icon">
                                            <i class="fas {{ $user->email_verified_at ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                        </span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-{{ $user->is_active ? 'secondary' : 'success' }} btn-sm btn-link"
                                            data-bs-toggle="tooltip"
                                            title="{{ $user->is_active ? 'Deactivate account' : 'Activate account' }}">
                                        <span class="btn-label just-icon">
                                            <i class="fas {{ $user->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                        </span>
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" 
                                      style="display: inline-block;" 
                                      onsubmit="return confirm('Are you sure you want to delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-link">
                                        <span class="btn-label just-icon"><i class="fa fa-trash"></i></span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row user-stats text-center">
                            <div class="col">
                                <div class="number">{{ $user->created_at->diffForHumans() }}</div>
                                <div class="title">Member since</div>
                            </div>
                            <div class="col">
                                <div class="number">
                                    @if($user->newsletter_subscription)
                                        <i class="fas fa-check text-success"></i>
                                    @else
                                        <i class="fas fa-times text-danger"></i>
                                    @endif
                                </div>
                                <div class="title">Newsletter</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details Card -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Detailed Information</h4>
                            <div class="ms-auto">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-round">
                                    <i class="fa fa-arrow-left"></i>
                                    Retour à la liste
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-round">
                                    <i class="fa fa-edit"></i>
                                    Modifier
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>First Name</strong></label>
                                    <p class="form-control-static">{{ $user->first_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Last Name</strong></label>
                                    <p class="form-control-static">{{ $user->last_name }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Email Address</strong></label>
                                    <p class="form-control-static">
                                        {{ $user->email }}
                                        @if($user->email_verified_at)
                                            <span class="badge badge-success ms-2">Verified</span>
                                        @else
                                            <span class="badge badge-warning ms-2">Not verified</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Phone</strong></label>
                                    <p class="form-control-static">{{ $user->phone ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Role</strong></label>
                                    <p class="form-control-static">
                                        <span class="badge badge-{{ $user->role_badge_color }}">
                                            <i class="{{ $user->role_icon }}"></i> {{ $user->role_label }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Account Status</strong></label>
                                    <p class="form-control-static">
                                        @if($user->is_active)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Active
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> Inactive
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>City</strong></label>
                                    <p class="form-control-static">{{ $user->city ?? 'Non renseignée' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Account Type</strong></label>
                                    <p class="form-control-static">
                                        @if($user->google_id)
                                            <span class="badge badge-success">
                                                <i class="fab fa-google"></i> Google Account
                                            </span>
                                        @else
                                            <span class="badge badge-primary">
                                                <i class="fas fa-envelope"></i> Email Account
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($user->isSupplier())
                        <div class="row">
                            <div class="col-md-12">
                                <h5 class="mt-4 mb-3">Supplier Information</h5>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Company Name</strong></label>
                                    <p class="form-control-static">{{ $user->company_name ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>License Number</strong></label>
                                    <p class="form-control-static">{{ $user->business_license ?? 'Non renseigné' }}</p>
                                </div>
                            </div>
                        </div>

                        @if($user->company_description)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"><strong>Company Description</strong></label>
                                    <p class="form-control-static">{{ $user->company_description }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($user->supplier_categories)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"><strong>Product Categories</strong></label>
                                    <p class="form-control-static">
                                        @foreach($user->supplier_categories as $category)
                                            <span class="badge badge-info me-1">{{ ucfirst($category) }}</span>
                                        @endforeach
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Newsletter</strong></label>
                                    <p class="form-control-static">
                                        @if($user->newsletter_subscription)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Subscribed
                                            </span>
                                        @else
                                            <span class="badge badge-secondary">
                                                <i class="fas fa-times"></i> Not subscribed
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Terms Accepted</strong></label>
                                    <p class="form-control-static">
                                        @if($user->terms_accepted)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check"></i> Accepted
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times"></i> Not accepted
                                            </span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Created At</strong></label>
                                    <p class="form-control-static">
                                        {{ $user->created_at->format('Y-m-d H:i') }}
                                        <small class="text-muted">({{ $user->created_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Last Updated</strong></label>
                                    <p class="form-control-static">
                                        {{ $user->updated_at->format('Y-m-d H:i') }}
                                        <small class="text-muted">({{ $user->updated_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                        </div>

                        @if($user->email_verified_at)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label"><strong>Email Verified at</strong></label>
                                    <p class="form-control-static">
                                        {{ $user->email_verified_at->format('Y-m-d H:i') }}
                                        <small class="text-muted">({{ $user->email_verified_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($user->google_id)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label"><strong>Google ID</strong></label>
                                    <p class="form-control-static">
                                        <code>{{ $user->google_id }}</code>
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.profile-header {
    background-image: url('{{ asset("vendor/kaiadmin/img/blogpost.jpg") }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
