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
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="text-primary mb-0">Event Details</h3>
                            <span class="badge bg-primary fs-6 px-3 py-2">{{ $event->category }}</span>
                        </div>
                        
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
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-tag"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Category</h6>
                                        <p class="text-muted mb-0">
                                            <span class="badge bg-primary">{{ $event->category }}</span>
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
                        @if($isParticipating && $event->date < now())
                            <!-- Feedback Form for Completed Events -->
                            <h4 class="text-primary mb-4">
                                <i class="fas fa-star me-2"></i>
                                @if($existingFeedback)
                                    Update Your Experience
                                @else
                                    Share Your Experience
                                @endif
                            </h4>
                            
                            @if(session('feedback_success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    {{ session('feedback_success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>Please fix the following errors:</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            @if($existingFeedback)
                                <div class="alert alert-info alert-dismissible fade show" role="alert">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>You have already submitted feedback for this event.</strong> You can update your feedback below.
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif

                            <form action="{{ route('events.feedback.store', $event) }}" method="POST" enctype="multipart/form-data" id="feedbackForm">
                                @csrf
                                
                                <!-- Rating Section -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-star text-warning me-2"></i>
                                        How was your experience?
                                    </label>
                                    <div class="rating-input">
                                        <div class="stars">
                                            @php
                                                $currentRating = old('rating', $existingFeedback ? $existingFeedback->rating : '');
                                            @endphp
                                            <input type="radio" id="star5" name="rating" value="5" {{ $currentRating == 5 ? 'checked' : '' }}>
                                            <label for="star5" class="star">★</label>
                                            <input type="radio" id="star4" name="rating" value="4" {{ $currentRating == 4 ? 'checked' : '' }}>
                                            <label for="star4" class="star">★</label>
                                            <input type="radio" id="star3" name="rating" value="3" {{ $currentRating == 3 ? 'checked' : '' }}>
                                            <label for="star3" class="star">★</label>
                                            <input type="radio" id="star2" name="rating" value="2" {{ $currentRating == 2 ? 'checked' : '' }}>
                                            <label for="star2" class="star">★</label>
                                            <input type="radio" id="star1" name="rating" value="1" {{ $currentRating == 1 ? 'checked' : '' }}>
                                            <label for="star1" class="star">★</label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Satisfaction Level -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-heart text-danger me-2"></i>
                                        Satisfaction Level
                                    </label>
                                    <select class="form-select" name="satisfaction_level" required>
                                        <option value="">Choose your satisfaction level</option>
                                        @php
                                            $currentSatisfaction = old('satisfaction_level', $existingFeedback ? $existingFeedback->satisfaction_level : '');
                                        @endphp
                                        <option value="1" {{ $currentSatisfaction == 1 ? 'selected' : '' }}>Very Dissatisfied</option>
                                        <option value="2" {{ $currentSatisfaction == 2 ? 'selected' : '' }}>Dissatisfied</option>
                                        <option value="3" {{ $currentSatisfaction == 3 ? 'selected' : '' }}>Neutral</option>
                                        <option value="4" {{ $currentSatisfaction == 4 ? 'selected' : '' }}>Satisfied</option>
                                        <option value="5" {{ $currentSatisfaction == 5 ? 'selected' : '' }}>Very Satisfied</option>
                                    </select>
                                </div>

                                <!-- Comment Section -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-comment text-primary me-2"></i>
                                        Share Your Story
                                    </label>
                                    <textarea class="form-control" name="comment" rows="3" 
                                              placeholder="Tell us about your experience, what you created, challenges you faced, or any tips for others...">{{ old('comment', $existingFeedback ? $existingFeedback->comment : '') }}</textarea>
                                </div>

                                <!-- Photo Upload Section -->
                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-camera text-purple me-2"></i>
                                        Show Your Creations
                                    </label>
                                    
                                    <!-- Display existing photos if any -->
                                    @if($existingFeedback && $existingFeedback->photo)
                                        <div class="mb-3">
                                            <label class="form-label small text-muted">Current Photos:</label>
                                            <div class="row g-2" id="existingPhotos">
                                                @foreach(json_decode($existingFeedback->photo) as $photo)
                                                <div class="col-3">
                                                    <div class="position-relative">
                                                        <img src="{{ asset('storage/' . $photo) }}" 
                                                             alt="Existing Photo" 
                                                             class="img-fluid rounded" 
                                                             style="height: 80px; object-fit: cover; width: 100%;">
                                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                                                style="transform: translate(50%, -50%);" 
                                                                onclick="removeExistingPhoto(this, '{{ $photo }}')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="upload-area" id="uploadArea">
                                        <div class="upload-content">
                                            <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                                            <h6>Upload New Photos</h6>
                                            <p class="text-muted small">Drag & drop or click to browse</p>
                                            <input type="file" name="photos[]" id="photoInput" multiple accept="image/*" style="display: none;">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('photoInput').click()">
                                                <i class="fas fa-plus me-1"></i>Choose Images
                                            </button>
                                        </div>
                                    </div>
                                    <div id="imagePreview" class="row mt-2"></div>
                                    <small class="text-muted">Upload up to 5 images of your recycled/upcycled products</small>
                                </div>

                                <!-- Submit Button -->
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        @if($existingFeedback)
                                            Update My Experience
                                        @else
                                            Share My Experience
                                        @endif
                                    </button>
                                </div>
                            </form>
                        @elseif($isParticipating)
                            <div class="text-center mb-4">
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                    <i class="fa fa-check" style="font-size: 2rem;"></i>
                                </div>
                                <h5 class="text-success">You're Registered!</h5>
                                <p class="text-muted">You're all set for this event. Check your email for the QR code.</p>
                            </div>
                            
                            <div class="d-grid gap-2">
                                @php
                                    $qrParticipantId = $participantId ?? session('participant_id');
                                @endphp
                                @if($qrParticipantId)
                                    <a href="{{ route('events.qr', [$event, $qrParticipantId]) }}" class="btn btn-outline-primary">
                                        <i class="fa fa-qrcode me-2"></i>View QR Code
                                    </a>
                                @endif
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
                                    @php
                                        $now = now()->startOfDay();
                                        $eventDate = $event->date->startOfDay();
                                        $isPast = $eventDate < $now;
                                        $isToday = $eventDate->isSameDay($now);
                                        $isTomorrow = $eventDate->isSameDay($now->copy()->addDay());
                                        
                                        if ($isPast) {
                                            $daysLeft = 0;
                                        } elseif ($isToday) {
                                            $daysLeft = 0;
                                        } elseif ($isTomorrow) {
                                            $daysLeft = 1;
                                        } else {
                                            $daysLeft = $now->diffInDays($eventDate, false);
                                        }
                                    @endphp
                                    
                                    @if($isPast)
                                        <h6 class="mb-1 text-danger">Event Ended</h6>
                                        <small class="text-muted">{{ $event->date->diffForHumans() }}</small>
                                    @elseif($isToday)
                                        <h6 class="mb-1 text-warning">Today!</h6>
                                        <small class="text-muted">Event is today</small>
                                    @elseif($isTomorrow)
                                        <h6 class="mb-1 text-info">Tomorrow</h6>
                                        <small class="text-muted">1 day left</small>
                                    @else
                                        <h6 class="mb-1 text-success">{{ $daysLeft }}</h6>
                                        <small class="text-muted">{{ $daysLeft == 1 ? 'Day Left' : 'Days Left' }}</small>
                                    @endif
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

/* Feedback Form Styles */
.rating-input {
    margin-bottom: 1rem;
}

.stars {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
    gap: 5px;
}

.stars input[type="radio"] {
    display: none;
}

.stars label {
    font-size: 2rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s ease;
}

.stars label:hover,
.stars label:hover ~ label,
.stars input[type="radio"]:checked ~ label {
    color: #ffc107;
}

.upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
}

.upload-area:hover {
    border-color: #28a745;
    background-color: #f8f9fa;
}

.upload-area.dragover {
    border-color: #28a745;
    background-color: #e8f5e8;
}

.upload-content {
    pointer-events: none;
}

.image-preview-item {
    position: relative;
    margin-bottom: 1rem;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
    border: 2px solid #dee2e6;
}

.remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
}

.remove-image:hover {
    background: rgba(220, 53, 69, 1);
}

/* Form validation styles */
.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Animation for form submission */
.btn-loading {
    position: relative;
    pointer-events: none;
}

.btn-loading::after {
    content: "";
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const photoInput = document.getElementById('photoInput');
    const imagePreview = document.getElementById('imagePreview');
    const feedbackForm = document.getElementById('feedbackForm');
    const submitBtn = feedbackForm.querySelector('button[type="submit"]');
    
    let selectedFiles = [];
    
    // Drag and drop functionality
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    });
    
    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    });
    
    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        const files = Array.from(e.dataTransfer.files);
        handleFiles(files);
    });
    
    // Click to upload
    uploadArea.addEventListener('click', function() {
        photoInput.click();
    });
    
    // File input change
    photoInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        handleFiles(files);
    });
    
    function handleFiles(files) {
        const imageFiles = files.filter(file => file.type.startsWith('image/'));
        
        if (selectedFiles.length + imageFiles.length > 5) {
            alert('You can only upload up to 5 images');
            return;
        }
        
        selectedFiles = [...selectedFiles, ...imageFiles];
        updateImagePreview();
        updateFileInput();
    }
    
    function updateImagePreview() {
        imagePreview.innerHTML = '';
        
        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-4 col-sm-6';
                col.innerHTML = `
                    <div class="image-preview-item">
                        <img src="${e.target.result}" alt="Preview ${index + 1}">
                        <button type="button" class="remove-image" onclick="removeImage(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                imagePreview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }
    
    function updateFileInput() {
        const dt = new DataTransfer();
        selectedFiles.forEach(file => dt.items.add(file));
        photoInput.files = dt.files;
    }
    
    // Remove image function
    window.removeImage = function(index) {
        selectedFiles.splice(index, 1);
        updateImagePreview();
        updateFileInput();
    };
    
    // Form submission
    feedbackForm.addEventListener('submit', function(e) {
        // Add loading state
        submitBtn.classList.add('btn-loading');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sharing Experience...';
    });
    
    // Star rating interaction
    const stars = document.querySelectorAll('.stars input[type="radio"]');
    stars.forEach(star => {
        star.addEventListener('change', function() {
            const rating = this.value;
            console.log('Rating selected:', rating);
        });
    });
    
    // Function to remove existing photos
    window.removeExistingPhoto = function(button, photoPath) {
        if (confirm('Are you sure you want to remove this photo?')) {
            // Add to a hidden input to track removed photos
            let removedPhotosInput = document.getElementById('removedPhotos');
            if (!removedPhotosInput) {
                removedPhotosInput = document.createElement('input');
                removedPhotosInput.type = 'hidden';
                removedPhotosInput.name = 'removed_photos[]';
                removedPhotosInput.id = 'removedPhotos';
                document.getElementById('feedbackForm').appendChild(removedPhotosInput);
            }
            
            // Add the photo path to removed photos
            const removedPhotos = removedPhotosInput.value ? removedPhotosInput.value.split(',') : [];
            removedPhotos.push(photoPath);
            removedPhotosInput.value = removedPhotos.join(',');
            
            // Remove the photo element from the DOM
            button.closest('.col-3').remove();
        }
    };
});
</script>
@endsection
