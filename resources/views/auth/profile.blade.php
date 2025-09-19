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
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=0D8ABC&color=fff&size=160" 
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
                            <h2 class="h3 mb-1 text-dark fw-bold">{{ $user->full_name }}</h2>
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

    @media (max-width: 768px) {
        .input-group-sm .form-control,
        .input-group-sm .btn {
            border-radius: 50px !important;
        }
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