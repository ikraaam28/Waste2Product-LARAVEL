@extends('layouts.admin')
@section('title', 'Event Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">{{ $event->title }}</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="{{ route('admin.events.index') }}"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Details</li>
            </ul>
        </div>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Event Information</div>
                        <div class="card-tools">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Modifier
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($event->image)
                            <div class="text-center mb-4">
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" 
                                     class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Category:</strong>
                                <span class="badge badge-info">{{ $event->category }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Statut:</strong>
                                <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                    {{ $event->status ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Date:</strong> {{ $event->date ? \Carbon\Carbon::parse($event->date)->format('d/m/Y') : 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Heure:</strong> {{ $event->time ? \Carbon\Carbon::parse($event->time)->format('H:i') : 'N/A' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Lieu:</strong> {{ $event->location }}
                            </div>
                        </div>

                        @if($event->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $event->description }}</p>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Participants:</strong> {{ $event->total_participants_count }}
                                @if($event->max_participants)
                                    / {{ $event->max_participants }}
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>QR Code:</strong> 
                                <code>{{ $event->qr_code }}</code>
                            </div>
                        </div>

                        @if($event->products->count() > 0)
                            <div class="mb-3">
                                <strong>Related Products:</strong>
                                <div class="row mt-2">
                                    @foreach($event->products as $product)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                                    <small class="text-muted">{{ $product->category->name }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Statistiques</div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="metric">
                                    <span class="icon"><i class="fa fa-users"></i></span>
                                    <p>
                                        <span class="number">{{ $event->total_participants_count }}</span>
                                        <span class="title">Participants</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric">
                                    <span class="icon"><i class="fa fa-qrcode"></i></span>
                                    <p>
                                        <span class="number">{{ $event->scanned_participants_count }}</span>
                                        <span class="title">Scanned</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="card">
                    <div class="card-header">
                        <div class="card-title">Actions</div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.events.qr-scanner') }}" class="btn btn-success">
                                <i class="fa fa-qrcode"></i> Scanner QR
                            </a>
                            <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $event->status ? 'warning' : 'success' }} w-100">
                                    <i class="fa fa-{{ $event->status ? 'pause' : 'play' }}"></i>
                                    {{ $event->status ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.events.feedback') }}" class="btn btn-info">
                                <i class="fa fa-chart-line"></i> Voir Feedback
                            </a>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Participants -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Participants</div>
                    </div>
                    <div class="card-body">
                        @if($event->participants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Ville</th>
                                            <th>Statut Scan</th>
                                            <th>Badge</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->participants as $participant)
                                            <tr>
                                                <td>{{ $participant->full_name }}</td>
                                                <td>{{ $participant->email }}</td>
                                                <td>{{ $participant->phone }}</td>
                                                <td>{{ $participant->city }}</td>
                                                <td>
                                                    @if($participant->pivot->scanned_at)
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-check"></i> Scanned
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">{{ $participant->pivot->scanned_at ? \Carbon\Carbon::parse($participant->pivot->scanned_at)->format('d/m/Y H:i') : 'N/A' }}</small>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fa fa-clock"></i> En attente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($participant->pivot->badge_earned)
                                                        <span class="badge badge-primary">
                                                            <i class="fa fa-medal"></i> Badge obtenu
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">Aucun participant inscrit</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedbacks -->
        @if($event->feedbacks->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Feedbacks</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($event->feedbacks as $feedback)
                                    <div class="col-md-6 mb-3">
                                        <div class="card feedback-card" style="cursor: pointer;"
                                             onclick='showAdminFeedbackModal({
                                                id: {{ $feedback->id }},
                                                rating: {{ (int) $feedback->rating }},
                                                comment: @json($feedback->comment),
                                                recycled_quantity: {{ (float) $feedback->recycled_quantity }},
                                                co2_saved: {{ (float) $feedback->co2_saved }},
                                                satisfaction_level: {{ (int) $feedback->satisfaction_level }},
                                                created_at: @json($feedback->created_at),
                                                photo: @json($feedback->photo),
                                                user: {
                                                    first_name: @json(optional($feedback->user)->first_name),
                                                    last_name: @json(optional($feedback->user)->last_name),
                                                    email: @json(optional($feedback->user)->email),
                                                    profile_picture: @json(optional($feedback->user)->profile_picture),
                                                    full_name: @json(optional($feedback->user)->full_name)
                                                },
                                                event: {
                                                    title: @json($event->title),
                                                    date: @json($event->date)
                                                }
                                             })'>
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="card-title">{{ $feedback->user->full_name }}</h6>
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fa fa-star{{ $i <= $feedback->rating ? '' : '-o' }} text-warning"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($feedback->comment)
                                                    <p class="card-text">{{ $feedback->comment }}</p>
                                                @endif
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <small class="text-muted">Recycled</small>
                                                        <br>
                                                        <strong>{{ $feedback->recycled_quantity }}kg</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">CO₂ Saved</small>
                                                        <br>
                                                        <strong>{{ $feedback->co2_saved }}kg</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">Satisfaction</small>
                                                        <br>
                                                        <strong>{{ $feedback->satisfaction_level }}/5</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
<!-- Feedback Details Modal -->
<div class="modal fade" id="adminFeedbackModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-comment-dots me-2"></i>Feedback Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="adminFeedbackModalBody">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
    </div>

<!-- Image Modal -->
<div class="modal fade" id="adminImageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Feedback Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="adminModalImage" src="" alt="Feedback Photo" class="img-fluid">
            </div>
        </div>
    </div>
</div>

<script>
function showAdminFeedbackModal(feedback) {
    const modalBody = document.getElementById('adminFeedbackModalBody');
    modalBody.innerHTML = generateAdminFeedbackContent(feedback);
    new bootstrap.Modal(document.getElementById('adminFeedbackModal')).show();
}

function generateAdminFeedbackContent(feedback) {
    const photos = feedback.photo ? (typeof feedback.photo === 'string' ? JSON.parse(feedback.photo) : feedback.photo) : [];
    const satisfactionText = getSatisfactionText(feedback.satisfaction_level);
    const satisfactionClass = feedback.satisfaction_level >= 4 ? 'success' : (feedback.satisfaction_level >= 3 ? 'warning' : 'danger');

    return `
        <div class="row">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                ${feedback.user && feedback.user.profile_picture ? 
                                    `<img src="/storage/${feedback.user.profile_picture}" alt="Profile" class="rounded-circle" width="80" height="80">` :
                                    `<div class=\"bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto\" style=\"width: 80px; height: 80px; font-size: 1.5rem;\">${(feedback.user?.first_name || '?').charAt(0)}${(feedback.user?.last_name || '?').charAt(0)}</div>`
                                }
                            </div>
                            <h5 class="mb-1">${feedback.user?.first_name || ''} ${feedback.user?.last_name || ''}</h5>
                            <p class="text-muted mb-2">${feedback.user?.email || ''}</p>
                            <small class="text-muted">${formatDate(feedback.created_at)}</small>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-primary mb-2"><i class="fas fa-calendar-alt me-2"></i>Event</h6>
                            <p class="mb-1 fw-bold">${feedback.event?.title || ''}</p>
                            <small class="text-muted">${formatDate(feedback.event?.date)}</small>
                        </div>
                        <div class="mb-4">
                            <h6 class="text-primary mb-2"><i class="fas fa-star me-2"></i>Rating</h6>
                            <div class="d-flex align-items-center justify-content-center">
                                <div class="stars me-3">
                                    ${Array.from({length: 5}, (_, i) => `<i class=\"fas fa-star ${i < (feedback.rating || 0) ? 'text-warning' : 'text-muted'}\"></i>`).join('')}
                                </div>
                                <span class="fw-bold fs-5">${feedback.rating || 0}/5</span>
                            </div>
                        </div>
                        <div class="text-center">
                            <h6 class="text-primary mb-2">Satisfaction Level</h6>
                            <span class="badge bg-${satisfactionClass} fs-6 px-3 py-2">${satisfactionText}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary"><i class="fas fa-comment me-2"></i>User Comment</h6>
                    </div>
                    <div class="card-body">
                        ${feedback.comment ? `<p class=\"mb-0\">${escapeHtml(feedback.comment)}</p>` : '<p class=\"text-muted fst-italic\">No comment provided</p>'}
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary"><i class="fas fa-chart-line me-2"></i>Impact Metrics</h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="fas fa-recycle text-success fa-2x mb-2"></i>
                                    <div class="h4 text-success">${feedback.recycled_quantity || 0} kg</div>
                                    <div class="text-muted">Recycled Material</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="border rounded p-3">
                                    <i class="fas fa-leaf text-info fa-2x mb-2"></i>
                                    <div class="h4 text-info">${feedback.co2_saved || 0} kg</div>
                                    <div class="text-muted">CO₂ Saved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                ${photos.length ? `
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-primary"><i class="fas fa-images me-2"></i>Photos (${photos.length})</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            ${photos.map(p => `
                                <div class=\"col-md-4 col-sm-6\">
                                    <div class=\"position-relative\">
                                        <img src=\"/storage/${p}\" alt=\"Feedback Photo\" class=\"img-fluid rounded shadow-sm\" style=\"height: 200px; object-fit: cover; width: 100%; cursor: pointer;\" onclick=\"showAdminImageModal('/storage/${p}')\">
                                        <div class=\"position-absolute top-0 end-0 m-2\">
                                            <button class=\"btn btn-sm btn-light rounded-circle\" onclick=\"showAdminImageModal('/storage/${p}')\" title=\"View full size\"><i class=\"fas fa-expand\"></i></button>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>` : `
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-image fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No photos uploaded</h6>
                    </div>
                </div>`}
            </div>
        </div>`;
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

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d)) return '';
    return d.toLocaleString();
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/\"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

function showAdminImageModal(src) {
    document.getElementById('adminModalImage').src = src;
    new bootstrap.Modal(document.getElementById('adminImageModal')).show();
}
</script>

<style>
.feedback-card {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}
.feedback-card:hover {
    transform: translateY(-5px);
    border-color: #007bff;
    box-shadow: 0 8px 25px rgba(0, 123, 255, 0.15);
}
.modal-xl { max-width: 1200px; }
.modal-header.bg-primary { background: linear-gradient(135deg, #007bff, #0056b3) !important; }
.stars i { font-size: 1.1rem; }
.rounded-circle { object-fit: cover; }
.card-header.bg-light { border-bottom: 2px solid #e9ecef; }
</style>

@endsection

