@extends('layouts.app')

@section('content')
<!-- My Events Hero Section -->
<div class="container-xxl py-5 bg-primary hero-header mb-5">
    <div class="container my-5 py-5 px-lg-5">
        <div class="row g-5 py-5">
            <div class="col-lg-8">
                <h1 class="text-white mb-4 animated slideInDown">My Events</h1>
                <p class="text-white pb-3 animated slideInDown">
                    Track all your event participations and manage your upcoming events.
                </p>
                <div class="d-flex flex-wrap gap-3 animated slideInDown">
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-calendar-check me-2"></i>
                        <span>{{ $participatedEvents->count() }} events participated</span>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-user me-2"></i>
                        <span>{{ auth()->user()->full_name }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 text-center text-lg-end">
                <div class="bg-white rounded-4 shadow-lg p-4 animated zoomIn">
                    <i class="fa fa-calendar-alt text-primary" style="font-size: 4rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- My Events Section -->
<div class="container-xxl py-5">
    <div class="container">
        @if($participatedEvents->count() > 0)
            <div class="row g-4">
                @foreach($participatedEvents as $participation)
                    @php
                        $event = $participation->event;
                        $isUpcoming = $event->date >= now();
                        $isPast = $event->date < now();
                        $isScanned = $participation->pivot->scanned_at;
                    @endphp
                    <div class="col-lg-6 col-xl-4">
                        <div class="card h-100 border-0 shadow-lg event-card">
                            <div class="position-relative">
                                @if($event->image)
                                    <img src="{{ Storage::url($event->image) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fa fa-calendar-alt text-white" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="position-absolute top-0 end-0 m-3">
                                    @if($isScanned)
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            <i class="fa fa-check me-1"></i>Checked In
                                        </span>
                                    @elseif($isUpcoming)
                                        <span class="badge bg-primary fs-6 px-3 py-2">
                                            <i class="fa fa-clock me-1"></i>Upcoming
                                        </span>
                                    @else
                                        <span class="badge bg-secondary fs-6 px-3 py-2">
                                            <i class="fa fa-history me-1"></i>Past
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Category Badge -->
                                <div class="position-absolute top-0 start-0 m-3">
                                    <span class="badge bg-light text-dark fs-6 px-3 py-2">
                                        {{ $event->category }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-primary mb-3">{{ $event->title }}</h5>
                                
                                <p class="card-text text-muted mb-3 flex-grow-1">
                                    {{ Str::limit($event->description, 100) }}
                                </p>
                                
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="fa fa-calendar-alt me-2"></i>
                                            <small>{{ $event->date->format('M j, Y') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="fa fa-clock me-2"></i>
                                            <small>{{ $event->time->format('g:i A') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="fa fa-map-marker-alt me-2"></i>
                                            <small>{{ $event->location }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Participation Info -->
                                <div class="bg-light rounded-3 p-3 mb-3">
                                    <div class="row g-2 text-center">
                                        <div class="col-6">
                                            <div class="d-flex flex-column">
                                                <small class="text-muted">Registered</small>
                                                <strong class="text-primary">{{ $participation->pivot->created_at->format('M j') }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex flex-column">
                                                <small class="text-muted">Status</small>
                                                <strong class="text-{{ $isScanned ? 'success' : ($isUpcoming ? 'primary' : 'secondary') }}">
                                                    {{ $isScanned ? 'Checked In' : ($isUpcoming ? 'Registered' : 'Completed') }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="d-grid gap-2">
                                    <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary">
                                        <i class="fa fa-eye me-2"></i>View Details
                                    </a>
                                    
                                    @if($isUpcoming && !$isScanned)
                                        <a href="{{ route('events.qr', [$event, $participation->pivot->participant_id]) }}" class="btn btn-primary">
                                            <i class="fa fa-qrcode me-2"></i>View QR Code
                                        </a>
                                    @elseif($isScanned)
                                        <button class="btn btn-success" disabled>
                                            <i class="fa fa-check me-2"></i>Successfully Checked In
                                        </button>
                                    @else
                                        <button class="btn btn-secondary" disabled>
                                            <i class="fa fa-history me-2"></i>Event Completed
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px;">
                    <i class="fa fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                </div>
                <h4 class="text-muted mb-3">No Events Yet</h4>
                <p class="text-muted mb-4">You haven't participated in any events yet. Start exploring our amazing events!</p>
                <a href="{{ route('events') }}" class="btn btn-primary btn-lg">
                    <i class="fa fa-calendar-plus me-2"></i>Browse Events
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Statistics Section -->
@if($participatedEvents->count() > 0)
    <div class="container-xxl py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-calendar-check" style="font-size: 1.5rem;"></i>
                            </div>
                            <h4 class="text-primary mb-1">{{ $participatedEvents->count() }}</h4>
                            <p class="text-muted mb-0">Total Events</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-check" style="font-size: 1.5rem;"></i>
                            </div>
                            <h4 class="text-success mb-1">{{ $participatedEvents->where('pivot.scanned_at', '!=', null)->count() }}</h4>
                            <p class="text-muted mb-0">Checked In</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-clock" style="font-size: 1.5rem;"></i>
                            </div>
                            <h4 class="text-warning mb-1">{{ $participatedEvents->where('event.date', '>=', now())->count() }}</h4>
                            <p class="text-muted mb-0">Upcoming</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center p-4">
                            <div class="bg-info text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fa fa-trophy" style="font-size: 1.5rem;"></i>
                            </div>
                            <h4 class="text-info mb-1">{{ $participatedEvents->where('pivot.badge_earned', '!=', null)->count() }}</h4>
                            <p class="text-muted mb-0">Badges Earned</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
.hero-header {
    background: linear-gradient(135deg, #28a745, #20c997);
}

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

.card {
    border-radius: 15px;
}

.btn {
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #218838, #1ea085);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.badge {
    border-radius: 20px;
    font-weight: 500;
}
</style>
@endsection
