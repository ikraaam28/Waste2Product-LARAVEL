@extends('layouts.admin')
@section('title', 'Partners List')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold mb-0">Partners</h3>
            <a href="{{ route('admin.partners.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Partner
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                @if($partners->count())
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Type</th>
                                <th>Address</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($partners as $partner)
                            <tr>
                                <td>{{ $partner->name }}</td>
                                <td>{{ $partner->email ?? '-' }}</td>
                                <td>{{ $partner->phone ?? '-' }}</td>
                                <td>
                                    @if($partner->type)
                                    <span class="badge bg-primary">{{ $partner->type }}</span>
                                    @else
                                    <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $partner->address ?? '-' }}</td>
                                <td class="text-center">
                                        <a href="{{ route('admin.partners.show', $partner) }}" class="btn btn-sm btn-info me-1">
        <i class="fas fa-eye"></i> View
    </a>
                                    <a href="{{ route('admin.partners.edit', $partner) }}" class="btn btn-sm btn-warning me-1">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this partner?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <p class="text-center text-muted">No partners found.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.table-hover tbody tr:hover {
    background-color: #f8f9fa;
}
.card-header {
    font-size: 1.1rem;
    font-weight: 500;
}
.btn-sm i {
    margin-right: 4px;
}
</style>
@endsection
