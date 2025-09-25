@extends('layouts.admin')

@section('title', 'Créer une Catégorie')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Créer une Catégorie</h3>
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
                <a href="{{ route('admin.categories.index') }}">Catégories</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Créer</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Nouvelle Catégorie</h4>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary btn-round ms-auto">
                            <i class="fa fa-arrow-left"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <!-- Informations de base -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">Informations de base</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="slug" class="form-label">Slug (URL)</label>
                                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                                   id="slug" name="slug" value="{{ old('slug') }}">
                                            <small class="form-text text-muted">Laissez vide pour générer automatiquement</small>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Paramètres et média -->
                            <div class="col-md-4">
                                <!-- Image -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Image de la catégorie</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                                   id="image" name="image" accept="image/*">
                                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (max: 2MB)</small>
                                            @error('image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <!-- Prévisualisation -->
                                        <div id="image-preview" class="mt-3" style="display: none;">
                                            <img id="preview-img" src="" alt="Prévisualisation" 
                                                 class="img-fluid rounded" style="max-height: 200px;">
                                        </div>
                                    </div>
                                </div>

                                <!-- Apparence -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Apparence</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="icon" class="form-label">Icône (Font Awesome)</label>
                                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                                   id="icon" name="icon" value="{{ old('icon', 'fas fa-tag') }}" 
                                                   placeholder="fas fa-tag">
                                            <small class="form-text text-muted">Ex: fas fa-recycle, fas fa-leaf</small>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="color" class="form-label">Couleur</label>
                                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                                   id="color" name="color" value="{{ old('color', '#007bff') }}">
                                            @error('color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Prévisualisation de l'apparence -->
                                        <div class="mt-3">
                                            <label class="form-label">Prévisualisation</label>
                                            <div class="d-flex align-items-center">
                                                <div id="icon-preview" class="avatar avatar-sm me-2" style="background-color: #007bff;">
                                                    <i class="fas fa-tag text-white"></i>
                                                </div>
                                                <span id="name-preview">Nom de la catégorie</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Paramètres -->
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">Paramètres</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="sort_order" class="form-label">Ordre d'affichage</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                                            <small class="form-text text-muted">Plus le nombre est petit, plus la catégorie apparaît en premier</small>
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                Catégorie active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                                                <i class="fa fa-times"></i> Annuler
                                            </a>
                                            <div>
                                                <button type="submit" class="btn btn-primary">
                                                    <i class="fa fa-save"></i> Créer la catégorie
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-generate slug from name
    $('#name').on('input', function() {
        const name = $(this).val();
        const slug = name.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        $('#slug').val(slug);
        
        // Update preview
        $('#name-preview').text(name || 'Nom de la catégorie');
    });

    // Update icon preview
    $('#icon').on('input', function() {
        const iconClass = $(this).val() || 'fas fa-tag';
        $('#icon-preview i').attr('class', iconClass + ' text-white');
    });

    // Update color preview
    $('#color').on('input', function() {
        const color = $(this).val();
        $('#icon-preview').css('background-color', color);
    });

    // Image preview
    $('#image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-img').attr('src', e.target.result);
                $('#image-preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image-preview').hide();
        }
    });

    // Form validation with debugging
    $('form').on('submit', function(e) {
        console.log('Form submission attempted...');

        const nameValue = $('#name').val();
        console.log('Name field value:', nameValue);

        let isValid = true;

        // Check required fields
        if (!nameValue || !nameValue.trim()) {
            console.log('Name field is empty, preventing submission');
            isValid = false;
            $('#name').addClass('is-invalid');
        } else {
            $('#name').removeClass('is-invalid');
            console.log('Name field validation passed');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires.');
            return false;
        }

        console.log('Form validation passed, submitting...');
    });
});
</script>
@endpush
