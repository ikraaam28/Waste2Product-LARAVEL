@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Main Profile Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white py-5 position-relative">
                    <!-- Gradient Background -->
                    <div class="bg-overlay"></div>
                    
                    <!-- Profile Picture Upload Section -->
                    <div class="row g-0 justify-content-center position-relative z-index-1">
                        <div class="col-auto mb-3">
                            <div class="position-relative">
                                @if($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                     alt="Photo de profil" 
                                     class="rounded-circle border-white border-4 shadow-lg"
                                     width="160" height="160" 
                                     style="object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->first_name . ' ' . $user->last_name) }}&background=0D8ABC&color=fff&size=160" 
                                         alt="Default Avatar" 
                                         class="rounded-circle border-white border-4 shadow-lg">
                                @endif
                                
                                <!-- Upload Button Overlay -->
                                <div class="position-absolute bottom-0 end-0 p-2 bg-primary rounded-circle shadow-sm">
                                    <label for="profile_picture" class="cursor-pointer text-white mb-0">
                                        <i class="fas fa-camera fs-5"></i>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="position-relative z-index-1">
                        @csrf
                        <div class="row justify-content-center">
                            <div class="col-auto">
                                <div class="input-group input-group-sm shadow-sm rounded-pill overflow-hidden">
                                    <input type="file" name="profile_picture" id="profile_picture" class="form-control border-0 px-3" 
                                           accept="image/*" style="max-width: 250px;">
                                    <button type="submit" class="btn btn-light px-4 fw-semibold">
                                        <i class="fas fa-upload me-1"></i>Mettre à jour
                                    </button>
                                </div>
                                @error('profile_picture')
                                    <div class="text-danger small mt-1 text-center">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Profile Content -->
                <div class="card-body p-0">
                    <!-- Name Section -->
                    <div class="bg-light py-4 px-4 border-bottom">
                        <div class="text-center">
                            <h2 class="h3 mb-1 text-dark fw-bold">{{ $user->first_name }} {{ $user->last_name }}</h2>
                            <p class="text-muted mb-0">Membre depuis {{ $user->created_at->format('d F Y') }}</p>
                        </div>
                    </div>

                    <!-- Info Grid -->
                    <div class="p-4">
                        <div class="row g-4">
                            <!-- Contact Info -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-white shadow-sm rounded-3 h-100">
                                    <div class="card-body p-4">
                                        <h5 class="card-title text-primary mb-3">
                                            <i class="fas fa-user-circle me-2"></i>Informations
                                        </h5>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-envelope text-primary me-3 fs-5"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Email</small>
                                                        <span class="fw-semibold">{{ $user->email }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-phone text-primary me-3 fs-5"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Téléphone</small>
                                                        <span class="fw-semibold">{{ $user->phone ?? 'Non renseigné' }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-map-marker-alt text-primary me-3 fs-5"></i>
                                                    <div>
                                                        <small class="text-muted d-block">Ville</small>
                                                        <span class="fw-semibold">{{ $user->city ?? 'Non renseignée' }}</span>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Settings -->
                            <div class="col-md-6">
                                <div class="card border-0 bg-white shadow-sm rounded-3 h-100">
                                    <div class="card-body p-4">
                                        <h5 class="card-title text-success mb-3">
                                            <i class="fas fa-cog me-2"></i>Paramètres
                                        </h5>
                                        <ul class="list-unstyled mb-0">
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-newspaper text-success me-3 fs-5"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Newsletter</small>
                                                            <span class="fw-semibold">Abonné{{ $user->newsletter_subscription ? '' : 'e' }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="badge {{ $user->newsletter_subscription ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 py-2">
                                                        {{ $user->newsletter_subscription ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                </div>
                                            </li>
                                            <li class="mb-3">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-file-contract text-success me-3 fs-5"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Conditions</small>
                                                            <span class="fw-semibold">Acceptées</span>
                                                        </div>
                                                    </div>
                                                    <span class="badge {{ $user->terms_accepted ? 'bg-success' : 'bg-warning' }} rounded-pill px-3 py-2">
                                                        {{ $user->terms_accepted ? 'Validé' : 'En attente' }}
                                                    </span>
                                                </div>
                                            </li>
                                            <li class="mb-0">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-calendar-check text-success me-3 fs-5"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Inscrit le</small>
                                                            <span class="fw-semibold">{{ $user->created_at->format('d/m/Y à H:i') }}</span>
                                                        </div>
                                                    </div>
                                                    <span class="badge bg-info rounded-pill px-3 py-2">
                                                        <i class="fas fa-star me-1"></i>Premium
                                                    </span>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Events Card -->
                    @if($participatedEvents->count() > 0)
                    <div class="p-4 border-top">
                        <div class="card border-0 bg-white shadow-sm rounded-3">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <h5 class="card-title text-primary mb-0">
                                        <i class="fas fa-calendar-alt me-2"></i>My Events
                                    </h5>
                                    <a href="{{ route('my-events') }}" class="btn btn-outline-primary btn-sm">
                                        View All <i class="fa fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                                <p class="text-muted mb-4">Your latest event participations and upcoming events.</p>
                                
                                <div class="row g-3">
                                    @foreach($participatedEvents->take(3) as $participation)
                                        @php
                                            $event = $participation->event;
                                            if (!$event) continue;
                                            
                                            $isUpcoming = $event->date >= now();
                                            $isScanned = $participation->pivot->scanned_at;
                                        @endphp
                                        <div class="col-md-4">
                                            <div class="card border-0 shadow-sm h-100">
                                                <div class="card-body p-3">
                                                    <div class="d-flex align-items-start justify-content-between mb-2">
                                                        <h6 class="card-title text-primary mb-1">{{ Str::limit($event->title, 20) }}</h6>
                                                        @if($isScanned)
                                                            <span class="badge bg-success">Checked In</span>
                                                        @elseif($isUpcoming)
                                                            <span class="badge bg-primary">Upcoming</span>
                                                        @else
                                                            <span class="badge bg-secondary">Past</span>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="text-muted small mb-2">
                                                        <div class="d-flex align-items-center mb-1">
                                                            <i class="fa fa-calendar-alt me-2"></i>
                                                            {{ $event->date->format('M j, Y') }}
                                                        </div>
                                                        <div class="d-flex align-items-center mb-1">
                                                            <i class="fa fa-clock me-2"></i>
                                                            {{ $event->time->format('g:i A') }}
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            <i class="fa fa-map-marker-alt me-2"></i>
                                                            {{ Str::limit($event->location, 15) }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="d-flex gap-2">
                                                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                                            <i class="fa fa-eye me-1"></i>View
                                                        </a>
                                                        @if($isUpcoming && !$isScanned)
                                                            <a href="{{ route('events.qr', [$event, $participation->pivot->participant_id]) }}" class="btn btn-primary btn-sm flex-fill">
                                                                <i class="fa fa-qrcode me-1"></i>QR
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Stats Footer -->
                    <div class="bg-gradient-secondary text-white py-3 px-4">
                        <div class="row text-center">
                            <div class="col-4">
                                <div class="h5 mb-0 fw-bold">0</div>
                                <small class="opacity-75">Publications</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 mb-0 fw-bold">0</div>
                                <small class="opacity-75">Abonnements</small>
                            </div>
                            <div class="col-4">
                                <div class="h5 mb-0 fw-bold">0</div>
                                <small class="opacity-75">Abonnés</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bouton pour ouvrir le modal -->
<div class="mt-4 text-center">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
        Modifier mes informations
    </button>
</div>

<!-- Modal Bootstrap -->
<div class="modal fade" id="updateProfileModal" tabindex="-1" aria-labelledby="updateProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="updateProfileModalLabel">Modifier mes informations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Prénom</label>
                        <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name', $user->first_name) }}">
                        @error('first_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="last_name" class="form-label">Nom</label>
                        <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name', $user->last_name) }}">
                        @error('last_name')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}">
                        @error('email')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                        @error('phone')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">Ville</label>
                        <input type="text" name="city" id="city" class="form-control" value="{{ old('city', $user->city) }}">
                        @error('city')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="newsletter_subscription" id="newsletter_subscription" class="form-check-input" {{ $user->newsletter_subscription ? 'checked' : '' }}>
                        <label for="newsletter_subscription" class="form-check-label">S'abonner à la newsletter</label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-success">Mettre à jour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<a href="{{ route('profile.reset-password') }}" class="btn btn-warning me-2">
    <i class="fas fa-key me-1"></i>Changer mon mot de passe
</a>

<style>
    :root {
        --gradient-primary: linear-gradient(135deg, #56ab2f 0%, #a8e6cf 100%);
        --gradient-secondary: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }

    .bg-gradient-primary {
        background: var(--gradient-primary) !important;
        position: relative;
        overflow: hidden;
    }

    .bg-gradient-secondary {
        background: var(--gradient-secondary) !important;
    }

    .bg-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
    }

    .position-relative.z-index-1 {
        position: relative;
        z-index: 1;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .input-group-sm .form-control {
        border-radius: 50px 0 0 50px !important;
    }

    .input-group-sm .btn {
        border-radius: 0 50px 50px 0 !important;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }

    .rounded-4 {
        border-radius: 24px !important;
    }

    .shadow-lg {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    }

    .badge {
        font-size: 0.75rem;
        font-weight: 500;
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

    @media (max-width: 768px) {
        .input-group-sm .form-control,
        .input-group-sm .btn {
            border-radius: 50px !important;
        }
    }

    .modal-backdrop {
        display: none !important;
    }
</style>

<script>
    // Smooth animations
    document.addEventListener('DOMContentLoaded', function() {
        // Add fade-in animation to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // File input styling
        const fileInput = document.getElementById('profile_picture');
        const label = document.querySelector('label[for="profile_picture"]');
        
        if (fileInput && label) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    label.innerHTML = '<i class="fas fa-check fs-5 text-success"></i>';
                }
            });
        }
    });
</script>
@endsection