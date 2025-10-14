@extends('layouts.admin')
@section('title', 'Commentaires')
@section('content')

@auth
    @if (Auth::user()->isAdmin())
        <div class="container">
            <div class="page-inner">
                <div class="page-header">
                    <h3 class="fw-bold mb-3">Commentaires</h3>
                    <ul class="breadcrumbs mb-3">
                        <li class="nav-home"><a href="{{ url('admin') }}"><i class="icon-home"></i></a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="{{ route('admin.commentaires.index') }}">Commentaires</a></li>
                    </ul>
                </div>

                <!-- Statistics Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Total Commentaires</h5>
                                <p class="fw-bold">{{ $stats['total_commentaires'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Commentaires (Last 30 Days)</h5>
                                <p class="fw-bold">{{ $stats['recent_commentaires'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Commentaires par Publication</h5>
                                <ul>
                                    @foreach ($stats['publication_distribution'] as $publication => $count)
                                        <li>{{ $publication }}: {{ $count }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('admin.commentaires.index') }}" method="GET" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" name="search" class="form-control" placeholder="Search by content or author" value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"> <!-- Corrected from route('date_to') to request('date_to') -->
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.commentaires.index') }}" class="btn btn-outline-secondary flex-fill">
                                            <i class="fas fa-undo"></i> Reset
                                        </a>
                                    </div>
                                </form>

                                <!-- Export Button -->
                                <form action="{{ route('admin.commentaires.export') }}" method="GET" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commentaires Table -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Commentaires List</div>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Author</th>
                                                <th>Content</th>
                                                <th>Publication</th>
                                                <th>Created At</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($commentaires as $commentaire)
                                                <tr>
                                                    <td>{{ $commentaire->user ? $commentaire->user->full_name : 'Deleted User' }}</td>
                                                    <td>{{ Str::limit($commentaire->contenu, 50) }}</td>
                                                    <td>{{ $commentaire->publication ? $commentaire->publication->titre : 'Deleted Publication' }}</td>
                                                    <td>{{ $commentaire->created_at->format('Y-m-d H:i:s') }}</td>
                                                    <td>
                                                        <!-- Delete Button with Modal -->
                                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $commentaire->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>

                                                        <!-- Delete Modal -->
                                                        <div class="modal fade" id="deleteModal{{ $commentaire->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Confirm Deletion</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete the comment by "{{ $commentaire->user->full_name ?? 'Deleted User' }}"?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <form action="{{ route('admin.commentaires.destroy', $commentaire->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                                        </form>
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                {{ $commentaires->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends Chart -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Commentaire Trends</h5>
                                <canvas id="monthlyTrendsChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="container">
            <div class="alert alert-danger">You do not have permission to access this page.</div>
        </div>
    @endif
@else
    <div class="container">
        <div class="alert alert-danger">Please log in to access this page.</div>
    </div>
@endauth

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json(array_keys($stats['monthly_trends'])),
            datasets: [{
                label: 'Commentaires per Month',
                data: @json(array_values($stats['monthly_trends'])),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Number of Commentaires' } },
                x: { title: { display: true, text: 'Month' } }
            },
            plugins: { legend: { display: true, position: 'top' } }
        }
    });
});
</script>

@endsection