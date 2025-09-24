@extends('layouts.app')

@section('content')
<!-- Events Hero Section -->
<div class="container-xxl py-3 bg-primary hero-header mb-4" style="margin-top: 80px;">
    <div class="container py-3 px-lg-5">
        <div class="row g-3 py-3">
            <div class="col-lg-8 text-center text-lg-start">
                <h1 class="text-white mb-3 animated slideInDown">Discover Our Events</h1>
                <p class="text-white pb-2 animated slideInDown">
                    Join our community for recycling events, environmental education, and hands-on workshops.
                </p>
                <div class="d-flex flex-wrap gap-3 animated slideInDown">
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-calendar-alt me-2"></i>
                        <span>{{ $totalEvents }} upcoming events</span>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-users me-2"></i>
                        <span>{{ $totalParticipants }} participants</span>
                    </div>
                </div>
            </div>
         
        </div>
    </div>
</div>

<!-- Events Section -->
<div class="container-xxl py-5">
    <div class="container">
        <!-- Filters Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('events') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Search</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Title, description, location...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Category</label>
                                <select class="form-select" name="category">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->value }}" {{ request('category') == $category->value ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">City</label>
                                <select class="form-select" name="city">
                                    <option value="">All Cities</option>
                                    @foreach(\App\Helpers\TunisiaCities::getCities() as $key => $city)
                                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">Start Date</label>
                                <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">End Date</label>
                                <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 me-2">
                                    <i class="fa fa-search"></i>
                                </button>
                              
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if(request()->hasAny(['search', 'category', 'city', 'date_from', 'date_to']))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-filter text-primary me-2"></i>
                                    <span class="fw-bold me-3">Active Filters:</span>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if(request('search'))
                                            <span class="badge bg-primary">
                                                Search: "{{ request('search') }}"
                                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="text-white ms-1">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @endif
                                        @if(request('category'))
                                            <span class="badge bg-success">
                                                Category: {{ request('category') }}
                                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="text-white ms-1">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @endif
                                        @if(request('city'))
                                            <span class="badge bg-info">
                                                City: {{ request('city') }}
                                                <a href="{{ request()->fullUrlWithQuery(['city' => null]) }}" class="text-white ms-1">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @endif
                                        @if(request('date_from'))
                                            <span class="badge bg-warning text-dark">
                                                From: {{ request('date_from') }}
                                                <a href="{{ request()->fullUrlWithQuery(['date_from' => null]) }}" class="text-dark ms-1">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @endif
                                        @if(request('date_to'))
                                            <span class="badge bg-warning text-dark">
                                                To: {{ request('date_to') }}
                                                <a href="{{ request()->fullUrlWithQuery(['date_to' => null]) }}" class="text-dark ms-1">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <a href="{{ route('events') }}" class="btn btn-outline-danger btn-sm">
                                    <i class="fa fa-times me-1"></i>Clear All
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Events Grid -->
        <div class="row g-4">
            @forelse($events as $event)
                <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                    <div class="card h-100 border-0 shadow-lg event-card">
                        <div class="position-relative">
                            @if($event->image)
                                <img src="{{ Storage::url($event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 250px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <i class="fa fa-calendar-alt text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            <div class="position-absolute top-0 end-0 m-3">
                                <span class="badge bg-{{ $event->category == 'Recycling' ? 'success' : ($event->category == 'Education' ? 'primary' : ($event->category == 'Awareness' ? 'warning' : 'info')) }} fs-6 px-3 py-2">
                                    {{ $event->category }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary mb-3">{{ $event->title }}</h5>
                            
                            <p class="card-text text-muted mb-3 flex-grow-1">
                                {{ Str::limit($event->description, 120) }}
                            </p>
                            
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fa fa-calendar-alt me-2"></i>
                                        <small>{{ $event->date->format('d/m/Y') }}</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fa fa-clock me-2"></i>
                                        <small>{{ $event->time->format('H:i') }}</small>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="fa fa-map-marker-alt me-2"></i>
                                        <small>{{ $event->location }}, {{ $event->city }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-users me-2 text-primary"></i>
                                    <small class="text-muted">
                                        {{ $event->participants->count() }}
                                        @if($event->max_participants)
                                            / {{ $event->max_participants }}
                                        @endif
                                        participants
                                    </small>
                                </div>
                                @if($event->max_participants && $event->participants->count() >= $event->max_participants)
                                    <span class="badge bg-danger">Complet</span>
                                @else
                                    <span class="badge bg-success">Disponible</span>
                                @endif
                            </div>
                            
                            <div class="d-grid">
                                <a href="{{ route('events.show', $event) }}" class="btn btn-primary">
                                    <i class="fa fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa fa-calendar-times text-muted" style="font-size: 4rem;"></i>
                        <h4 class="text-muted mt-3">No Events Found</h4>
                        <p class="text-muted">
                            @if(request()->hasAny(['search', 'category', 'city', 'date_from', 'date_to']))
                                No events match your current filters. Try adjusting your search criteria.
                            @else
                                No upcoming events available at the moment.
                            @endif
                        </p>
                        @if(request()->hasAny(['search', 'category', 'city', 'date_from', 'date_to']))
                            <a href="{{ route('events') }}" class="btn btn-primary me-2">
                                <i class="fa fa-times me-1"></i>Clear All Filters
                            </a>
                        @endif
                        <a href="{{ route('events') }}" class="btn btn-outline-primary">
                            <i class="fa fa-refresh me-1"></i>View All Events
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($events->hasPages())
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        <nav aria-label="Events pagination">
                            {{ $events->appends(request()->query())->links('pagination::bootstrap-4') }}
                        </nav>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.event-card {
    transition: all 0.3s ease;
    border-radius: 15px;
    overflow: hidden;
}

.event-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
}

.event-card .card-img-top {
    transition: transform 0.3s ease;
}

.event-card:hover .card-img-top {
    transform: scale(1.05);
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #28a745, #20c997);
}

/* Pagination Styles */
.pagination {
    margin-bottom: 0;
}

.pagination .page-link {
    color: #28a745;
    border: 1px solid #dee2e6;
    padding: 0.75rem 1rem;
    margin: 0 2px;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    color: #fff;
    background-color: #28a745;
    border-color: #28a745;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.pagination .page-item.active .page-link {
    background-color: #28a745;
    border-color: #28a745;
    color: #fff;
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

.pagination .page-item:first-child .page-link,
.pagination .page-item:last-child .page-link {
    border-radius: 8px;
}

.card {
    border-radius: 15px;
}

.btn-primary {
    border-radius: 25px;
    padding: 10px 20px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.badge {
    border-radius: 20px;
    font-weight: 500;
}

.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.card {
    border: none;
}

.hero-header {
    background: linear-gradient(135deg, #28a745, #20c997);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form when filters change
    const filterForm = document.querySelector('form');
    const filterInputs = filterForm.querySelectorAll('select, input[type="date"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Add a small delay to prevent too many requests
            setTimeout(() => {
                filterForm.submit();
            }, 300);
        });
    });
    
    // Search input with debounce
    const searchInput = document.querySelector('input[name="search"]');
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            if (this.value.length >= 3 || this.value.length === 0) {
                filterForm.submit();
            }
        }, 500);
    });
    
    // Clear individual filters
    document.querySelectorAll('.badge a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');
            window.location.href = url;
        });
    });
});
</script>
@endsection
