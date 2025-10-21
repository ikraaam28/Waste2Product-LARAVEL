@extends('layouts.admin')

@section('title', 'Tutorial Management')

@section('content')
<style>
/* General Styling */
.container-fluid {
    max-width: 1400px;
    margin: 0 auto;
}
.card.tuto-table {
    border-radius: 16px;
    background-color: #fff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    transition: transform 0.2s ease;
}
.card.tuto-table:hover {
    transform: translateY(-4px);
}
.card-body {
    padding: 0;
}
.table {
    margin-bottom: 0;
    border-collapse: separate;
    border-spacing: 0;
}
.table th, .table td {
    vertical-align: middle;
    padding: 1rem;
    font-size: 0.95rem;
}
.table th {
    background-color: #f8f9fa;
    color: #2c3e50;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    border-bottom: 2px solid #e9ecef;
}
.table td {
    border-bottom: 1px solid #e9ecef;
}
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Breadcrumbs */
.breadcrumbs {
    font-size: 0.9rem;
}
.breadcrumbs a {
    color: #007bff;
    text-decoration: none;
}
.breadcrumbs a:hover {
    text-decoration: underline;
}
.breadcrumbs .separator {
    color: #2c3e50;
    margin: 0 0.5rem;
}

/* Buttons */
.btn-primary, .btn-outline-primary, .btn-outline-warning, .btn-outline-danger, .btn-outline-secondary {
    border-radius: 20px;
    padding: 0.5rem 1.25rem;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}
.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}
.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
.btn-outline-primary {
    border-color: #007bff;
    color: #007bff;
}
.btn-outline-primary:hover {
    background-color: #007bff;
    color: #fff;
}
.btn-outline-warning {
    border-color: #ffc107;
    color: #ffc107;
}
.btn-outline-warning:hover {
    background-color: #ffc107;
    color: #fff;
}
.btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
}
.btn-outline-danger:hover {
    background-color: #dc3545;
    color: #fff;
}
.btn-outline-secondary {
    border-color: #e9ecef;
    color: #2c3e50;
}
.btn-outline-secondary:hover {
    background-color: #e9ecef;
    color: #2c3e50;
}
.action-icon {
    padding: 0.4rem 0.8rem;
    font-size: 0.9rem;
}

/* Modal */
.modal-content {
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}
.modal-header, .modal-footer {
    background-color: #f8f9fa;
    border: none;
}
.modal-title {
    color: #2c3e50;
    font-weight: 700;
}
.modal-body p {
    font-size: 0.95rem;
}
.modal-body strong {
    color: #2c3e50;
}

/* Alerts */
.alert-success {
    border-radius: 10px;
    background-color: rgba(40,167,69,0.1);
    border-color: #28a745;
    color: #28a745;
}

/* Responsive Design */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }
    .table th, .table td {
        font-size: 0.85rem;
        padding: 0.75rem;
    }
    .action-icon {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
    .d-flex {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
}
</style>

<div class="container-fluid py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold text-dark">Tutorial Management</h2>
            <a href="{{ route('admin.tutos.create') }}" class="btn btn-primary rounded-pill px-4" aria-label="Create a new tutorial">Create New Tutorial</a>
        </div>
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($tutos->isEmpty())
            <div class="card shadow-sm border-0 text-center p-4">
                <p class="text-muted mb-0">No tutorials available at the moment.</p>
            </div>
        @else
            <div class="card tuto-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Author</th>
                                    <th>Views</th>
                                    <th>Likes</th>
                                    <th>Dislikes</th>
                                    <th>Published</th>
                                    <th>Admin Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tutos as $tuto)
                                    <tr>
                                        <td>{{ \Illuminate\Support\Str::limit($tuto->title, 50) }}</td>
                                        <td>{{ $tuto->category ? ucfirst($tuto->category->name) : 'N/A' }}</td>
                                        <td>{{ $tuto->user ? $tuto->user->full_name : 'N/A' }}</td>
                                        <td>{{ $tuto->views }}</td>
                                        <td>{{ $tuto->likes_count }}</td>
                                        <td>{{ $tuto->dislikes_count }}</td>
                                        <td>
                                            <span class="{{ $tuto->is_published ? 'text-success' : 'text-warning' }}">
                                                {{ $tuto->is_published ? 'Published' : 'Unpublished' }}
                                            </span>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($tuto->admin_notes ?? 'No notes', 30) }}</td>
                                        <td class="d-flex gap-1">
                                            <a href="{{ route('admin.tutos.show', $tuto) }}" class="btn btn-sm btn-outline-primary action-icon" title="View Tutorial" aria-label="View tutorial"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('admin.tutos.edit', $tuto) }}" class="btn btn-sm btn-outline-warning action-icon" title="Edit Tutorial" aria-label="Edit tutorial"><i class="fas fa-edit"></i></a>
                                            <button type="button" class="btn btn-sm btn-outline-danger action-icon" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tuto->id }}" title="Delete Tutorial" aria-label="Delete tutorial"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Delete Confirmation Modal -->
                                    <div class="modal fade" id="deleteModal-{{ $tuto->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $tuto->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-sm">
                                                <div class="modal-header bg-light border-0">
                                                    <h5 class="modal-title fw-bold text-dark" id="deleteModalLabel-{{ $tuto->id }}">Confirm Deletion</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4">
                                                    <p class="mb-0">Are you sure you want to delete the tutorial "<strong>{{ \Illuminate\Support\Str::limit($tuto->title, 50) }}</strong>"? This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer border-0 bg-light">
                                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal" aria-label="Cancel">Cancel</button>
                                                    <form action="{{ route('admin.tutos.destroy', $tuto) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4" aria-label="Confirm deletion">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection