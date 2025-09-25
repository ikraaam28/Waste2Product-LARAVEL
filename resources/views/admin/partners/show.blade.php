@extends('layouts.admin')
@section('title', 'Partners Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0">Partners Details</h3>
            <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Partner Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">name</label>
                                <p class="fs-6 mb-0">{{ $partner->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Email</label>
                                <p class="fs-6 mb-0">{{ $partner->email ?? '-' }}</p>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">phone number</label>
                                <p class="fs-6 mb-0">{{ $partner->phone ?? '-' }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold text-muted small">Type</label>
                                <div>
                                    @if($partner->type)
                                        <span class="badge bg-primary fs-6">{{ $partner->type }}</span>
                                    @else
                                        <span class="text-muted">not specified</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold text-muted small">Address</label>
                                <p class="fs-6 mb-0">{{ $partner->address ?? 'no address specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Statistics</h5>
                    </div>
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-warehouse fa-2x text-info mb-2"></i>
                            <h3 class="fw-bold">{{ $partner->warehouses->count() }}</h3>
                            <p class="text-muted mb-0">Associated Warehouses</p>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-cube fa-2x text-success mb-2"></i>
                            <h3 class="fw-bold">
                                {{ $partner->warehouses->sum('current_occupancy') }} / {{ $partner->warehouses->sum('capacity') }}
                            </h3>
                            <p class="text-muted mb-0">Total Capacity Used</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Entrepôts -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Partner Warehouses</h5>
                <a href="{{ route('admin.warehouses.create') }}?partner_id={{ $partner->id }}" 
                   class="btn btn-light btn-sm">
                    <i class="fas fa-plus"></i> Add Warehouse
                </a>
            </div>
            <div class="card-body">
                @if($partner->warehouses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Location</th>
                                    <th>Capacity</th>
                                    <th>Occupancy</th>
                                    <th>Availability</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($partner->warehouses as $warehouse)
                                    <tr>
                                        <td>
                                            <strong>{{ $warehouse->name }}</strong>
                                            @if($warehouse->description)
                                                <br><small class="text-muted">{{ Str::limit($warehouse->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($warehouse->location)
                                                {{ $warehouse->location }}
                                                @if($warehouse->city)
                                                    <br><small class="text-muted">{{ $warehouse->city }}</small>
                                                @endif
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ number_format($warehouse->capacity, 0, ',', ' ') }} m³</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                    <div class="progress-bar bg-{{ $warehouse->occupancy_percentage > 80 ? 'danger' : ($warehouse->occupancy_percentage > 50 ? 'warning' : 'success') }}" 
                                                         style="width: {{ $warehouse->occupancy_percentage }}%">
                                                    </div>
                                                </div>
                                                <small>{{ $warehouse->occupancy_percentage }}%</small>
                                            </div>
                                            <small class="text-muted">
                                                {{ number_format($warehouse->current_occupancy, 0, ',', ' ') }} m³
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $warehouse->available_capacity > 0 ? 'success' : 'danger' }}">
                                                {{ number_format($warehouse->available_capacity, 0, ',', ' ') }} m³
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $warehouse->status === 'active' ? 'success' : ($warehouse->status === 'maintenance' ? 'warning' : 'secondary') }}">
                                                {{ $warehouse->status === 'active' ? 'active' : ($warehouse->status === 'maintenance' ? 'Maintenance' : 'Inactive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('admin.warehouses.show', $warehouse) }}" 
                                                   class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.warehouses.edit', $warehouse) }}" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun entrepôt associé à ce partenaire.</p>
                        <a href="{{ route('admin.warehouses.create') }}?partner_id={{ $partner->id }}" 
                           class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter le premier entrepôt
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Actions -->
        <div class="mt-4 d-flex justify-content-end gap-2">
            <a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Edit Partner
            </a>
            <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" style="display:inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('Are you sure you want to delete this partner? This action is irreversible.')">
                    <i class="fas fa-trash"></i> Delete Partner
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}
.card-header {
    border-radius: 10px 10px 0 0 !important;
}
.progress {
    border-radius: 4px;
}
.badge {
    font-size: 0.75rem;
}
.table th {
    border-top: none;
    font-weight: 600;
    color: #6c757d;
}
</style>
@endsection