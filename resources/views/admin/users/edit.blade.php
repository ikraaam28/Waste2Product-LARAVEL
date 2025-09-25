@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Modifier l'Utilisateur</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}">Utilisateurs</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">{{ $user->full_name }}</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Modifier {{ $user->full_name }}</h4>
                            <div class="ms-auto">
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-round">
                                    <i class="fa fa-eye"></i>
                                    Voir Détails
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-round">
                                    <i class="fa fa-arrow-left"></i>
                                    Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <!-- Current Profile Picture -->
                            @if($user->profile_picture || $user->avatar)
                            <div class="row mb-4">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Photo de Profil Actuelle</label>
                                        <div>
                                            @if($user->profile_picture)
                                                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                                     alt="Profile" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                            @elseif($user->avatar)
                                                <img src="{{ $user->avatar }}" 
                                                     alt="Profile" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="row">
                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Adresse Email <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Téléphone</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">Ville</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror" 
                                               id="city" name="city" value="{{ old('city', $user->city) }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_picture">Nouvelle Photo de Profil</label>
                                        <input type="file" class="form-control @error('profile_picture') is-invalid @enderror" 
                                               id="profile_picture" name="profile_picture" accept="image/*">
                                        @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Formats acceptés: JPG, JPEG, PNG, GIF. Taille max: 2MB. Laissez vide pour conserver l'image actuelle.
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mt-4 mb-3">Changer le Mot de Passe</h5>
                                    <p class="text-muted">Laissez vide pour conserver le mot de passe actuel</p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Nouveau Mot de Passe</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Minimum 8 caractères (si renseigné)
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmer le Nouveau Mot de Passe</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Information -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mt-4 mb-3">Informations du Compte</h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="newsletter_subscription" name="newsletter_subscription" 
                                                   {{ old('newsletter_subscription', $user->newsletter_subscription) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="newsletter_subscription">
                                                Abonner à la newsletter
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Type de Compte</strong></label>
                                        <p class="form-control-static">
                                            @if($user->google_id)
                                                <span class="badge badge-success">
                                                    <i class="fab fa-google"></i> Compte Google
                                                </span>
                                            @else
                                                <span class="badge badge-primary">
                                                    <i class="fas fa-envelope"></i> Compte Email
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Account Status -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Statut Email</strong></label>
                                        <p class="form-control-static">
                                            @if($user->email_verified_at)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle"></i> Vérifié le {{ $user->email_verified_at->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="badge badge-warning">
                                                    <i class="fas fa-exclamation-circle"></i> Non vérifié
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label"><strong>Membre depuis</strong></label>
                                        <p class="form-control-static">
                                            {{ $user->created_at->format('d/m/Y') }} ({{ $user->created_at->diffForHumans() }})
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview New Profile Picture -->
                            <div class="row" id="imagePreview" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Aperçu de la nouvelle photo</label>
                                        <div>
                                            <img id="preview" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="card-action">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Sauvegarder les Modifications
                                </button>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info">
                                    <i class="fa fa-eye"></i> Voir Détails
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-danger">
                                    <i class="fa fa-times"></i> Annuler
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle password visibility
    $('#togglePassword').click(function() {
        const password = $('#password');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    $('#togglePasswordConfirm').click(function() {
        const password = $('#password_confirmation');
        const type = password.attr('type') === 'password' ? 'text' : 'password';
        password.attr('type', type);
        $(this).find('i').toggleClass('fa-eye fa-eye-slash');
    });

    // Image preview
    $('#profile_picture').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview').attr('src', e.target.result);
                $('#imagePreview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#imagePreview').hide();
        }
    });

    // Form validation
    $('form').submit(function(e) {
        const password = $('#password').val();
        const confirmPassword = $('#password_confirmation').val();
        
        // Only validate if password is provided
        if (password) {
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Les mots de passe ne correspondent pas.');
                return false;
            }
            
            if (password.length < 8) {
                e.preventDefault();
                alert('Le mot de passe doit contenir au moins 8 caractères.');
                return false;
            }
        }
    });

    // Real-time password confirmation validation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();
        
        if (password && confirmPassword && password !== confirmPassword) {
            $(this).addClass('is-invalid');
            if (!$(this).next('.invalid-feedback').length) {
                $(this).after('<div class="invalid-feedback">Les mots de passe ne correspondent pas.</div>');
            }
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.invalid-feedback').remove();
        }
    });
});
</script>
@endpush
