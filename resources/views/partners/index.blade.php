@extends('layouts.app')

@section('content')
<!-- Partners Hero Section -->
<div class="container-xxl py-3 bg-primary hero-header mb-4" style="margin-top: 80px;">
    <div class="container py-3 px-lg-5">
        <div class="row g-3 py-3">
            <div class="col-lg-8 text-center text-lg-start">
                <h1 class="text-white mb-3 animated slideInDown">Our Partners</h1>
                <p class="text-white pb-2 animated slideInDown">
                    Discover the companies and organizations that collaborate with us.
                </p>
                <div class="d-flex flex-wrap gap-3 animated slideInDown">
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-handshake me-2"></i>
                        <span>{{ $partners->count() }} partners</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Partners Section -->
<div class="container-xxl py-5">
    <div class="container">
        <!-- Filters Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <form method="GET" action="{{ route('partners.front') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Search</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name, email, type...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Type</label>
                                <select class="form-select" name="type">
                                    <option value="">All Types</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">City</label>
                                <select class="form-select" name="city">
                                    <option value="">All Cities</option>
                                    @foreach(\App\Helpers\TunisiaCities::getCities() as $city)
                                        <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100 me-2">
                                    <i class="fa fa-search"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if(request()->hasAny(['search','type','city']))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-0 bg-light">
                        <div class="card-body p-3 d-flex align-items-center justify-content-between">
                            <div class="d-flex flex-wrap gap-2">
                                <span class="fw-bold me-2">Active Filters:</span>
                                @if(request('search'))
                                    <span class="badge bg-primary">
                                        Search: "{{ request('search') }}"
                                        <a href="{{ request()->fullUrlWithQuery(['search'=>null]) }}" class="text-white ms-1"><i class="fa fa-times"></i></a>
                                    </span>
                                @endif
                                @if(request('type'))
                                    <span class="badge bg-success">
                                        Type: {{ request('type') }}
                                        <a href="{{ request()->fullUrlWithQuery(['type'=>null]) }}" class="text-white ms-1"><i class="fa fa-times"></i></a>
                                    </span>
                                @endif
                                @if(request('city'))
                                    <span class="badge bg-info">
                                        City: {{ request('city') }}
                                        <a href="{{ request()->fullUrlWithQuery(['city'=>null]) }}" class="text-white ms-1"><i class="fa fa-times"></i></a>
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('partners.front') }}" class="btn btn-outline-danger btn-sm">
                                <i class="fa fa-times me-1"></i>Clear All
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Partners Grid -->
        <div class="row g-4">
            @forelse($partners as $partner)
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-lg partner-card">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-primary mb-2">{{ $partner->name }}</h5>
                            <p class="text-muted mb-2">{{ $partner->type ?? '-' }}</p>
                            <p class="text-muted mb-3">{{ $partner->address ?? '-' }}</p>
                            <p class="text-muted mb-3"><i class="fa fa-envelope me-2"></i>{{ $partner->email ?? '-' }}</p>
                            <p class="text-muted mb-3"><i class="fa fa-phone me-2"></i>{{ $partner->phone ?? '-' }}</p>
                            <div class="mt-auto">
                                <a href="{{ route('partners.show', $partner) }}" class="btn btn-primary w-100">
                                    <i class="fa fa-eye me-2"></i>View Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="fa fa-handshake text-muted" style="font-size:4rem;"></i>
                    <h4 class="text-muted mt-3">No Partners Found</h4>
                    <p class="text-muted">Try adjusting your filters to see partners.</p>
                    <a href="{{ route('partners.front') }}" class="btn btn-outline-primary">
                        <i class="fa fa-refresh me-1"></i>View All Partners
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($partners->hasPages())
            <div class="row mt-5">
                <div class="col-12 d-flex justify-content-center">
                    {{ $partners->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.partner-card {
    transition: all 0.3s ease;
    border-radius: 15px;
}
.partner-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}
.btn-primary {
    border-radius: 25px;
    padding: 10px 20px;
}
.badge {
    border-radius: 20px;
}
.form-control, .form-select {
    border-radius: 10px;
    border: 2px solid #e9ecef;
}
.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40,167,69,0.25);
}
.hero-header {
    background: linear-gradient(135deg, #28a745, #20c997);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterForm = document.querySelector('form');
    const filterInputs = filterForm.querySelectorAll('select, input[type="text"]');
    
    filterInputs.forEach(input => {
        input.addEventListener('change', () => filterForm.submit());
    });

    const searchInput = document.querySelector('input[name="search"]');
    let timeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            filterForm.submit();
        }, 500);
    });

    document.querySelectorAll('.badge a').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = this.getAttribute('href');
        });
    });
});
</script>
@endsection
