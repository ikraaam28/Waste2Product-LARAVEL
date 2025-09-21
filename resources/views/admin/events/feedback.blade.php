@extends('layouts.admin')
@section('title', 'Event Feedback & Impact')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Event Feedback & Impact</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
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
                <div class="card h-100 shadow-sm">
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
                                         onclick="showImageModal('{{ asset('storage/' . $photo) }}')">
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
</style>
@endsection


