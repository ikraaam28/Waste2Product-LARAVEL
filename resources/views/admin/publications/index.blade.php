@extends('layouts.admin')
@section('title', 'Publications')
@section('content')

@auth
    @if (Auth::user()->isAdmin())
        <div class="container">
            <div class="page-inner">
                <div class="page-header">
                    <h3 class="fw-bold mb-3">Publications</h3>
                    <ul class="breadcrumbs mb-3">
                        <li class="nav-home"><a href="{{ url('admin') }}"><i class="icon-home"></i></a></li>
                        <li class="separator"><i class="icon-arrow-right"></i></li>
                        <li class="nav-item"><a href="{{ route('admin.publications.index') }}">Publications</a></li>
                    </ul>
                </div>

                <!-- Statistics Section -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Total Publications</h5>
                                <p class="fw-bold">{{ $stats['total_publications'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Publications (Last 30 Days)</h5>
                                <p class="fw-bold">{{ $stats['recent_publications'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h5>Categories</h5>
                                <ul>
                                    @foreach ($stats['category_distribution'] as $category => $count)
                                        <li>{{ $category }}: {{ $count }}</li>
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
                                <form action="{{ route('admin.publications.index') }}" method="GET" class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                                            <input type="text" name="search" class="form-control" placeholder="Search by title or author" value="{{ request('search') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-list"></i></span>
                                            <select name="category" class="form-control">
                                                <option value="">All Categories</option>
                                                @foreach ($stats['category_distribution'] as $category => $count)
                                                    <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                                @endforeach
                                            </select>
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
                                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary flex-fill">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                        <a href="{{ route('admin.publications.index') }}" class="btn btn-outline-secondary flex-fill">
                                            <i class="fas fa-undo"></i> Reset
                                        </a>
                                    </div>
                                </form>

                                <!-- Export Button -->
                                <form action="{{ route('admin.publications.export') }}" method="GET" class="mt-2">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-file-export"></i> Export
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Publications Table -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="card-title">Publications List</div>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif

                                <div class="table-responsive">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Author</th>
                                                <th>Title</th>
                                                <th>Category</th>
                                                <th>Content</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($publications as $publication)
                                                <tr>
                                                    <td>
                                                        @if ($publication->image && Storage::disk('public')->exists($publication->image))
                                                            <img src="{{ Storage::url($publication->image) }}" alt="Publication Image" style="max-width: 100px; max-height: 100px;">
                                                        @else
                                                            <span>No Image</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $publication->user ? $publication->user->first_name . ' ' . $publication->user->last_name : 'Deleted User' }}</td>
                                                    <td>{{ $publication->titre }}</td>
                                                    <td>{{ $publication->categorie }}</td>
                                                    <td>{{ Str::limit($publication->contenu, 50) }}</td>
                                                    <td>
                                                        <!-- Delete Button with Modal -->
                                                        <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $publication->id }}">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>

                                                        <!-- Ban User Button with Modal -->
                                                        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#banModal{{ $publication->id }}">
                                                            <i class="fas fa-ban"></i>
                                                        </button>

                                                        <!-- Delete Modal -->
                                                        <div class="modal fade" id="deleteModal{{ $publication->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Confirm Deletion</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to delete "{{ $publication->titre }}"?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <form action="{{ route('admin.publications.destroy', $publication->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn btn-danger">Delete</button>
                                                                        </form>
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Ban Modal -->
                                                        <div class="modal fade" id="banModal{{ $publication->id }}" tabindex="-1" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Confirm Ban</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Are you sure you want to ban "{{ $publication->user->first_name ?? 'Deleted User' }} {{ $publication->user->last_name ?? '' }}"? This will delete all their publications and prevent further posting.
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <form action="{{ route('admin.publications.ban', $publication->id) }}" method="POST">
                                                                            @csrf
                                                                            @method('POST')
                                                                            <button type="submit" class="btn btn-warning">Ban</button>
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

                                {{ $publications->links() }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Trends Chart -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h5>Publication Trends</h5>
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
    // Safe access to monthly_trends with fallback
    @if(isset($stats['monthly_trends']) && is_array($stats['monthly_trends']) && !empty($stats['monthly_trends']))
        const ctx = document.getElementById('monthlyTrendsChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json(array_keys($stats['monthly_trends'])),
                datasets: [{
                    label: 'Publications per Month',
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
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    @else
        // Fallback: Hide or show empty chart message
        console.warn('Monthly trends data not available');
        const chartContainer = document.getElementById('monthlyTrendsChart').parentElement;
        if (chartContainer) {
            chartContainer.innerHTML = '<p class="text-muted text-center">Données mensuelles non disponibles.</p>';
        }
    @endif
});
</script>

@endsection