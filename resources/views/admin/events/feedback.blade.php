@extends('layouts.admin')
@section('title', 'Event Feedback & Impact')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Event Feedback & Impact</h3>
        <ul class="breadcrumbs mb-3">
            <li class="nav-home"><a href="{{ route('admin.events.index') }}"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
            <li class="nav-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Feedback & Impact</li>
            </ul>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-comments fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ $totalFeedbacks ?? 0 }}</h4>
                                <small>Total Feedbacks</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-recycle fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ number_format($totalRecycled ?? 0, 1) }} kg</h4>
                                <small>Total Recycled</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-leaf fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ number_format($totalCo2Saved ?? 0, 1) }} kg</h4>
                                <small>CO₂ Saved</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-star fa-2x"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h4 class="mb-0">{{ number_format($averageRating ?? 0, 1) }}/5</h4>
                                <small>Average Rating</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedbacks Display -->
        <div class="row">
            @forelse($feedbacks ?? [] as $feedback)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm feedback-card" style="cursor: pointer;" onclick="showFeedbackModal({{ $feedback->id }})">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-primary">{{ $feedback->event->title }}</h6>
                            <small class="text-muted">{{ $feedback->created_at ? \Carbon\Carbon::parse($feedback->created_at)->format('M j, Y') : 'N/A' }}</small>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- User Info -->
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                @if($feedback->user->profile_picture)
                                    <img src="{{ asset('storage/' . $feedback->user->profile_picture) }}" 
                                         alt="Profile" class="rounded-circle" width="40" height="40">
                                @else
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px;">
                                        {{ substr($feedback->user->first_name, 0, 1) }}{{ substr($feedback->user->last_name, 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0">{{ $feedback->user->first_name }} {{ $feedback->user->last_name }}</h6>
                                <small class="text-muted">{{ $feedback->user->email }}</small>
                            </div>
                        </div>

                        <!-- Rating -->
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <span class="me-2">Rating:</span>
                                <div class="stars">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                                    @endfor
                                </div>
                                <span class="ms-2 fw-bold">{{ $feedback->rating }}/5</span>
                            </div>
                        </div>

                        <!-- Comment -->
                        @if($feedback->comment)
                        <div class="mb-3">
                            <p class="text-muted small mb-1">Comment:</p>
                            <p class="mb-0">{{ Str::limit($feedback->comment, 100) }}</p>
                        </div>
                        @endif

                        <!-- Impact Metrics -->
                        <div class="row text-center mb-3">
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <i class="fas fa-recycle text-success"></i>
                                    <div class="small">{{ $feedback->recycled_quantity }} kg</div>
                                    <div class="small text-muted">Recycled</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-2">
                                    <i class="fas fa-leaf text-info"></i>
                                    <div class="small">{{ $feedback->co2_saved }} kg</div>
                                    <div class="small text-muted">CO₂ Saved</div>
                                </div>
                            </div>
                        </div>

                        <!-- Photos -->
                        @if($feedback->photo)
                        <div class="mb-3">
                            <p class="small text-muted mb-2">Photos:</p>
                            <div class="row g-2">
                                @foreach(json_decode($feedback->photo) as $photo)
                                <div class="col-4">
                                    <img src="{{ asset('storage/' . $photo) }}" 
                                         alt="Feedback Photo" 
                                         class="img-fluid rounded" 
                                         style="height: 60px; object-fit: cover; cursor: pointer;"
                                         onclick="event.stopPropagation(); showImageModal('{{ asset('storage/' . $photo) }}')">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Satisfaction Level -->
                        <div class="text-center">
                            <span class="badge bg-{{ $feedback->satisfaction_level >= 4 ? 'success' : ($feedback->satisfaction_level >= 3 ? 'warning' : 'danger') }}">
                                @switch($feedback->satisfaction_level)
                                    @case(1) Very Dissatisfied @break
                                    @case(2) Dissatisfied @break
                                    @case(3) Neutral @break
                                    @case(4) Satisfied @break
                                    @case(5) Very Satisfied @break
                                @endswitch
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card text-center py-5">
                    <div class="card-body">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No feedback yet</h5>
                        <p class="text-muted">User feedbacks will appear here after events are completed.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Feedback Details Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-comment-dots me-2"></i>Feedback Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="feedbackModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feedback Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Feedback Photo" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
// Feedback data for modal
const feedbacksData = @json($feedbacks ?? []);

function showFeedbackModal(feedbackId) {
    const feedback = feedbacksData.find(f => f.id === feedbackId);
    if (!feedback) return;
    
    const modalBody = document.getElementById('feedbackModalBody');
    modalBody.innerHTML = generateFeedbackModalContent(feedback);
    
    new bootstrap.Modal(document.getElementById('feedbackModal')).show();
}

function generateFeedbackModalContent(feedback) {
    const photos = feedback.photo ? JSON.parse(feedback.photo) : [];
    const satisfactionText = getSatisfactionText(feedback.satisfaction_level);
    const satisfactionClass = feedback.satisfaction_level >= 4 ? 'success' : (feedback.satisfaction_level >= 3 ? 'warning' : 'danger');
    
    return `
        <div class="row">
            <!-- Left Column - User Info & Rating -->
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <!-- User Info -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                ${feedback.user.profile_picture ? 
                                    `<img src="${feedback.user.profile_picture}" alt="Profile" class="rounded-circle" width="80" height="80">` :
                                    `<div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 1.5rem;">
                                        ${feedback.user.first_name.charAt(0)}${feedback.user.last_name.charAt(0)}
                                    </div>`
                                }
                            </div>
                            <h5 class="mb-1">${feedback.user.first_name} ${feedback.user.last_name}</h5>
                            <p class="text-muted mb-2">${feedback.user.email}</p>
                            <small class="text-muted">${new Date(feedback.created_at).toLocaleDateString('en-US', { 
                                year: 'numeric', 
                                month: 'long', 
                                day: 'numeric',
                                hour: '2-digit',
                                minute: '2-digit'
                            })}</small>
                        </div>
                        
                        <!-- Event Info -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-calendar-alt me-2"></i>Event
                            </h6>
                            <p class="mb-1 fw-bold">${feedback.event.title}</p>
                            <small class="text-muted">${new Date(feedback.event.date).toLocaleDateString()}</small>
                        </div>
                        
                        <!-- Rating -->
                        <div class="mb-4">
                            <h6 class="text-primary mb-2">
                                <i class="fas fa-star me-2"></i>Rating
                            </h6>
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="stars me-3">
                                    ${Array.from({length: 5}, (_, i) => 
                                        `<i class="fas fa-star ${i < feedback.rating ? 'text-warning' : 'text-muted'}"></i>`
                                    ).join('')}
                                </div>
                                <span class="fw-bold fs-5">${feedback.rating}/5</span>
                            </div>
                        </div>
                        
                        <!-- Satisfaction Level -->
                        <div class="text-center">
                            <h6 class="text-primary mb-2">Satisfaction Level</h6>
                            <span class="badge bg-${satisfactionClass} fs-6 px-3 py-2">
                                ${satisfactionText}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Comment & Photos -->
            <div class="col-md-8">
                <!-- Comment -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-comment me-2"></i>User Comment
                        </h6>
                    </div>
                    <div class="card-body">
                        ${feedback.comment ? 
                            `<p class="mb-0">${feedback.comment}</p>` : 
                            '<p class="text-muted fst-italic">No comment provided</p>'
                        }
                    </div>
                </div>
                
                <!-- Impact Metrics -->
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-chart-line me-2"></i>Impact Metrics
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="fas fa-recycle text-success fa-2x mb-2"></i>
                                    <div class="h4 text-success">${feedback.recycled_quantity} kg</div>
                                    <div class="text-muted">Recycled Material</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="fas fa-leaf text-info fa-2x mb-2"></i>
                                    <div class="h4 text-info">${feedback.co2_saved} kg</div>
                                    <div class="text-muted">CO₂ Saved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Photos -->
                ${photos.length > 0 ? `
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary">
                            <i class="fas fa-images me-2"></i>Photos (${photos.length})
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            ${photos.map(photo => `
                                <div class="col-md-4 col-sm-6">
                                    <div class="position-relative">
                                        <img src="/storage/${photo}" 
                                             alt="Feedback Photo" 
                                             class="img-fluid rounded shadow-sm" 
                                             style="height: 200px; object-fit: cover; width: 100%; cursor: pointer;"
                                             onclick="showImageModal('/storage/${photo}')">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <button class="btn btn-sm btn-light rounded-circle" 
                                                    onclick="showImageModal('/storage/${photo}')"
                                                    title="View full size">
                                                <i class="fas fa-expand"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
                ` : `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No photos uploaded</h6>
                    </div>
                </div>
                `}
            </div>
        </div>
    `;
}

function getSatisfactionText(level) {
    switch(level) {
        case 1: return 'Very Dissatisfied';
        case 2: return 'Dissatisfied';
        case 3: return 'Neutral';
        case 4: return 'Satisfied';
        case 5: return 'Very Satisfied';
        default: return 'Not specified';
    }
}

function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>

<style>
.stars {
    display: inline-flex;
    gap: 2px;
}

.card {
    transition: transform 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
}

.bg-light {
    background-color: #f8f9fa !important;
}

/* Feedback card hover effects */
.feedback-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.feedback-card:hover {
    transform: translateY(-5px);
    border-color: #007bff;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
}

/* Modal styles */
.modal-xl {
    max-width: 1200px;
}

.modal-header.bg-primary {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

/* Photo gallery styles */
.position-relative img {
    transition: transform 0.3s ease;
}

.position-relative img:hover {
    transform: scale(1.05);
}

/* Impact metrics styling */
.border.rounded.p-3 {
    transition: all 0.3s ease;
}

.border.rounded.p-3:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
}

/* Rating stars in modal */
.stars i {
    font-size: 1.2rem;
}

/* User avatar styling */
.rounded-circle {
    object-fit: cover;
}

/* Badge styling */
.badge.fs-6 {
    font-size: 0.9rem !important;
    padding: 0.5rem 1rem !important;
}

/* Card header styling */
.card-header.bg-light {
    border-bottom: 2px solid #e9ecef;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
    }
    
    .feedback-card {
        margin-bottom: 1rem;
    }
}
</style>
@endsection


