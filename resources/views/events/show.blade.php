@extends('layouts.app')

@section('content')
<!-- Event Hero Section -->
<div class="container-xxl py-2 mb-2">
    <div class="container py-2 px-lg-5">
        <div class="row g-2 py-2">
            <div class="col-12" style="margin-top: 80px;">
                <a href="{{ route('events') }}" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Events
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Event Details Section -->
<div class="container-xxl py-3">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Event Information -->
                <div class="card shadow-lg border-0 mb-5">
                    <div class="card-body p-5">
                        <h3 class="text-primary mb-4">Event Details</h3>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Date</h6>
                                        <p class="text-muted mb-0">{{ $event->date->format('l, F j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Time</h6>
                                        <p class="text-muted mb-0">{{ $event->time->format('g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Location</h6>
                                        <p class="text-muted mb-0">{{ $event->location }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-users"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Participants</h6>
                                        <p class="text-muted mb-0">
                                            {{ $event->participants->count() }}
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($event->description)
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">About This Event</h5>
                                <p class="text-muted">{{ $event->description }}</p>
                            </div>
                        @endif

                        <!-- Products Section -->
                        @if($event->products->count() > 0)
                            <div class="mb-4">
                                <h5 class="text-primary mb-3">Related Products</h5>
                                <div class="row g-3">
                                    @foreach($event->products as $product)
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center p-3 bg-light rounded-3">
                                                @if($product->image)
                                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-primary text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fa fa-box"></i>
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                                    <small class="text-muted">{{ $product->category->name ?? 'Product' }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Similar Events -->
                @if($similarEvents->count() > 0)
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <h3 class="text-primary mb-4">Similar Events</h3>
                            <div class="row g-4">
                                @foreach($similarEvents as $similarEvent)
                                    <div class="col-md-4">
                                        <div class="card h-100 border-0 shadow-sm">
                                            @if($similarEvent->image)
                                                <img src="{{ Storage::url($similarEvent->image) }}" class="card-img-top" alt="{{ $similarEvent->title }}" style="height: 150px; object-fit: cover;">
                                            @else
                                                <div class="card-img-top bg-gradient-primary d-flex align-items-center justify-content-center" style="height: 150px;">
                                                    <i class="fa fa-calendar-alt text-white" style="font-size: 2rem;"></i>
                                                </div>
                                            @endif
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $similarEvent->title }}</h6>
                                                <p class="card-text text-muted small">{{ Str::limit($similarEvent->description, 80) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">{{ $similarEvent->date->format('M j') }}</small>
                                                    <a href="{{ route('events.show', $similarEvent) }}" class="btn btn-sm btn-outline-primary">View</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Mobile Toggle Button -->
                <div class="d-lg-none mb-3">
                    <button class="btn btn-primary w-100" type="button" data-bs-toggle="collapse" data-bs-target="#participationCard" aria-expanded="false">
                        <i class="fa fa-calendar-plus me-2"></i>Join This Event
                    </button>
                </div>
                
                <!-- Participation Card - Sticky in sidebar -->
                <div class="card shadow-lg border-0 " id="participationCard">
                    <div class="card-body p-4">
                        <h4 class="text-primary mb-4">Join This Event</h4>
                        
                        @if($isParticipating)
                            <div class="text-center mb-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa fa-check" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="text-success">You're Registered!</h5>
                                <p class="text-muted">You're all set for this event. Check your email for the QR code.</p>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('events.qr', [$event, $participantId]) }}" class="btn btn-outline-primary">
                                    <i class="fa fa-qrcode me-2"></i>View QR Code
                                </a>
                                <a href="{{ route('my-events') }}" class="btn btn-outline-secondary">
                                    <i class="fa fa-calendar me-2"></i>My Events
                                </a>
                            </div>
                        @else
                            @if($event->max_participants && $event->participants->count() >= $event->max_participants)
                                <div class="text-center mb-4">
                                    <div class="bg-danger text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-times" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="text-danger">Event Full</h5>
                                    <p class="text-muted">This event has reached its maximum capacity.</p>
                                </div>
                            @elseif($event->date < now())
                                <div class="text-center mb-4">
                                    <div class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-clock" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="text-warning">Event Passed</h5>
                                    <p class="text-muted">This event has already taken place.</p>
                                </div>
                            @else
                                <div class="text-center mb-4">
                                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                        <i class="fa fa-calendar-plus" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="text-primary">Join Now</h5>
                                    <p class="text-muted">Be part of this amazing event and make a difference!</p>
                                </div>
                                
                                @auth
                                    <form method="POST" action="{{ route('events.participate', $event) }}">
                                        @csrf
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="fa fa-user-plus me-2"></i>Register for Event
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="d-grid">
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                                            <i class="fa fa-sign-in-alt me-2"></i>Login to Register
                                        </a>
                                    </div>
                                @endauth
                            @endif
                        @endif
                    </div>
                </div>

                <!-- Event Stats - Normal position below fixed card -->
                <div class="card shadow-lg border-0 mt-4 event-stats-card">
                    <div class="card-body p-4">
                        <h5 class="text-primary mb-4">Event Statistics</h5>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <i class="fa fa-users text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <h6 class="mb-1">{{ $event->participants->count() }}</h6>
                                    <small class="text-muted">Participants</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded-3">
                                    <i class="fa fa-calendar text-primary mb-2" style="font-size: 1.5rem;"></i>
                                    <h6 class="mb-1">{{ $event->date->diffInDays(now()) }}</h6>
                                    <small class="text-muted">Days Left</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($event->max_participants)
                            <div class="mt-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="text-muted">Capacity</small>
                                    <small class="text-muted">{{ $event->participants->count() }}/{{ $event->max_participants }}</small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: {{ ($event->participants->count() / $event->max_participants) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hero-header {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
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

.bg-gradient-primary {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.badge {
    border-radius: 20px;
    font-weight: 500;
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.sticky-top {
    z-index: 1020;
}

.fixed-card {
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.fixed-card:hover {
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

/* Responsive design for fixed card */
@media (max-width: 1200px) {
    .fixed-card {
        position: relative !important;
        top: auto !important;
        right: auto !important;
        width: 100% !important;
        max-height: none !important;
        overflow-y: visible !important;
        margin-bottom: 2rem;
    }
}

@media (max-width: 768px) {
    .fixed-card {
        margin: 0 -15px 2rem -15px;
        border-radius: 0;
    }
}

/* Smooth transitions for collapse */
.collapse {
    transition: all 0.3s ease;
}

/* Ensure the fixed card doesn't interfere with content */
@media (min-width: 1200px) {
    .col-lg-8 {
        padding-right: 2rem;
    }
    
    /* Event Stats positioning below sticky card */
    .event-stats-card {
        margin-top: 2rem !important;
    }
}

/* Sticky participation card */
.sticky-participation {
    position: sticky;
    top: 2rem;
    z-index: 1050;
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    border-radius: 15px;
    transition: all 0.3s ease;
}

.sticky-participation:hover {
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

/* Event Stats responsive positioning */
@media (max-width: 1200px) {
    .event-stats-card {
        margin-top: 2rem !important;
    }
    
    .sticky-participation {
        position: relative !important;
        top: auto !important;
    }
}
</style>
@endsection
