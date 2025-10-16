@extends('layouts.admin')

@section('content')
<style>
    .tuto-table {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .tuto-table th, .tuto-table td {
        vertical-align: middle;
    }
    .tuto-table .btn {
        margin-right: 5px;
    }
    .action-icon {
        font-size: 1rem;
        padding: 0.5rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 fw-bold">Tutorial Management</h2>
            <a href="{{ route('admin.tutos.create') }}" class="btn btn-primary rounded-pill px-4">Create New Tutorial</a>
        </div>
        
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($tutos->isEmpty())
            <p class="text-center text-muted">No tutorials available at the moment.</p>
        @else
            <div class="card tuto-table">
                <div class="card-body p-0">
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
                                    <td>{{ ucfirst($tuto->category) }}</td>
                                    <td>{{ $tuto->user->full_name }}</td>
                                    <td>{{ $tuto->views }}</td>
                                    <td>{{ $tuto->likes_count }}</td>
                                    <td>{{ $tuto->dislikes_count }}</td>
                                    <td>
                                        <span class="{{ $tuto->is_published ? 'text-success' : 'text-warning' }}">
                                            {{ $tuto->is_published ? 'Published' : 'Unpublished' }}
                                        </span>
                                    </td>
                                    <td>{{ \Illuminate\Support\Str::limit($tuto->admin_notes ?? 'No notes', 30) }}</td>
                                    <td>
                                        <a href="{{ route('admin.tutos.show', $tuto) }}" class="btn btn-sm btn-outline-primary action-icon" title="View Tutorial"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('admin.tutos.edit', $tuto) }}" class="btn btn-sm btn-outline-warning action-icon" title="Edit Tutorial"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger action-icon" data-bs-toggle="modal" data-bs-target="#deleteModal-{{ $tuto->id }}" title="Delete Tutorial"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <!-- Delete Confirmation Modal -->
                                <div class="modal fade" id="deleteModal-{{ $tuto->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $tuto->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                                            <div class="modal-header bg-light border-0">
                                                <h5 class="modal-title fw-bold" id="deleteModalLabel-{{ $tuto->id }}">Confirm Deletion</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <p class="mb-0">Are you sure you want to delete the tutorial "<strong>{{ \Illuminate\Support\Str::limit($tuto->title, 50) }}</strong>"? This action cannot be undone.</p>
                                            </div>
                                            <div class="modal-footer border-0 bg-light">
                                                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('admin.tutos.destroy', $tuto) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">Delete</button>
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
        @endif
    </div>
</div>
@endsection
