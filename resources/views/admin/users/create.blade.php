@extends('layouts.admin')

@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Ajouter un Utilisateur</h3>
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
                    <a href="#">Ajouter</a>
                </li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h4 class="card-title">Nouveau Utilisateur</h4>
                            <div class="ms-auto">
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-round">
                                    <i class="fa fa-arrow-left"></i>
                                    Retour à la liste
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <!-- Personal Information -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="first_name">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="last_name">Nom <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name') }}" required>
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
                                               id="email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="role">Rôle <span class="text-danger">*</span></label>
                                        <select class="form-control @error('role') is-invalid @enderror"
                                                id="role" name="role" required>
                                            <option value="">Sélectionner un rôle</option>
                                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                                                Administrateur
                                            </option>
                                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>
                                                Utilisateur
                                            </option>
                                            <option value="supplier" {{ old('role') == 'supplier' ? 'selected' : '' }}>
                                                Fournisseur
                                            </option>
                                        </select>
                                        @error('role')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Téléphone</label>
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" name="phone" value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <div class="form-check mt-4">
                                            <input class="form-check-input" type="checkbox"
                                                   id="is_active" name="is_active"
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Compte actif
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">Ville</label>
                                        <input type="text" class="form-control @error('city') is-invalid @enderror"
                                               id="city" name="city" value="{{ old('city') }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="profile_picture">Photo de Profil</label>
                                        <input type="file" class="form-control @error('profile_picture') is-invalid @enderror"
                                               id="profile_picture" name="profile_picture" accept="image/*">
                                        @error('profile_picture')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="form-text text-muted">
                                            Formats acceptés: JPG, JPEG, PNG, GIF. Taille max: 2MB
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Supplier-specific fields -->
                            <div id="supplier-fields" style="display: none;">
                                <h5 class="mt-4 mb-3">Informations Fournisseur</h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="company_name">Nom de l'Entreprise <span class="text-danger supplier-required">*</span></label>
                                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                                   id="company_name" name="company_name" value="{{ old('company_name') }}">
                                            @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="business_license">Numéro de Licence</label>
                                            <input type="text" class="form-control @error('business_license') is-invalid @enderror"
                                                   id="business_license" name="business_license" value="{{ old('business_license') }}">
                                            @error('business_license')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="company_description">Description de l'Entreprise</label>
                                            <textarea class="form-control @error('company_description') is-invalid @enderror"
                                                      id="company_description" name="company_description" rows="3">{{ old('company_description') }}</textarea>
                                            @error('company_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="supplier_categories">Catégories de Produits</label>
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="plastique" id="cat_plastique">
                                                        <label class="form-check-label" for="cat_plastique">Plastique</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="papier" id="cat_papier">
                                                        <label class="form-check-label" for="cat_papier">Papier</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="metal" id="cat_metal">
                                                        <label class="form-check-label" for="cat_metal">Métal</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="verre" id="cat_verre">
                                                        <label class="form-check-label" for="cat_verre">Verre</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="textile" id="cat_textile">
                                                        <label class="form-check-label" for="cat_textile">Textile</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="electronique" id="cat_electronique">
                                                        <label class="form-check-label" for="cat_electronique">Électronique</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="organique" id="cat_organique">
                                                        <label class="form-check-label" for="cat_organique">Déchets Organiques</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="bois" id="cat_bois">
                                                        <label class="form-check-label" for="cat_bois">Bois</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="supplier_categories[]" value="autre" id="cat_autre">
                                                        <label class="form-check-label" for="cat_autre">Autre</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Mot de Passe <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required>
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
                                            Minimum 8 caractères
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmer le Mot de Passe <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                   id="newsletter_subscription" name="newsletter_subscription" 
                                                   {{ old('newsletter_subscription') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="newsletter_subscription">
                                                Abonner à la newsletter
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Preview Profile Picture -->
                            <div class="row" id="imagePreview" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Aperçu de la photo</label>
                                        <div>
                                            <img id="preview" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="card-action">
                                <button type="submit" class="btn btn-success">
                                    <i class="fa fa-save"></i> Créer l'Utilisateur
                                </button>
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
    // Toggle supplier fields based on role selection
    $('#role').change(function() {
        const role = $(this).val();
        if (role === 'supplier') {
            $('#supplier-fields').show();
            $('#company_name').attr('required', true);
        } else {
            $('#supplier-fields').hide();
            $('#company_name').attr('required', false);
        }
    });

    // Initialize supplier fields visibility on page load
    const initialRole = $('#role').val();
    if (initialRole === 'supplier') {
        $('#supplier-fields').show();
        $('#company_name').attr('required', true);
    }

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
        const role = $('#role').val();

        // Validate supplier fields
        if (role === 'supplier') {
            const companyName = $('#company_name').val();
            if (!companyName) {
                e.preventDefault();
                alert('Le nom de l\'entreprise est requis pour les fournisseurs.');
                return false;
            }
        }

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
    });

    // Real-time password confirmation validation
    $('#password_confirmation').on('input', function() {
        const password = $('#password').val();
        const confirmPassword = $(this).val();

        if (confirmPassword && password !== confirmPassword) {
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
