@extends('layouts.admin')
@section('title', 'Partner Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header mb-4 d-flex justify-content-between align-items-center">
            <h3 class="fw-bold mb-0">Partner Details</h3>
            <a href="{{ route('admin.partners.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Name:</strong>
                        <p>{{ $partner->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Email:</strong>
                        <p>{{ $partner->email ?? '-' }}</p>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Phone:</strong>
                        <p>{{ $partner->phone ?? '-' }}</p>
                    </div>
                    <div class="col-md-6">
                        <strong>Type:</strong>
                        @if($partner->type)
                            <span class="badge bg-primary">{{ $partner->type }}</span>
                        @else
                            <span class="text-muted">N/A</span>
                        @endif
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Address:</strong>
                        <p>{{ $partner->address ?? '-' }}</p>
                    </div>
                </div>

        @if($partner->relationLoaded('warehouses') && $partner->warehouses->count())
    <div class="row mb-3">
        <div class="col-md-12">
            <strong>Warehouses:</strong>
            <ul>
                @foreach($partner->warehouses as $warehouse)
                    <li>{{ $warehouse->name }} - {{ $warehouse->location }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif


                <div class="mt-4 d-flex gap-2">
                    <a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Edit Partner
                    </a>
                    <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this partner?')">
                            <i class="fas fa-trash"></i> Delete Partner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card-body p {
    margin: 0;
    font-size: 1rem;
}
.badge {
    font-size: 0.9rem;
    padding: 0.4em 0.7em;
}
</style>
@endsection
