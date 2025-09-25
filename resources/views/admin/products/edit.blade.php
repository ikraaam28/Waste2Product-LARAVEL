@extends('layouts.admin')

@section('title', 'Modifier le Produit')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Modifier le Produit</h3>
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
                <a href="{{ route('admin.products.index') }}">Produits</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ $product->name }}</a>
            </li>
        </ul>
    </div>

    <form method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <!-- Informations principales -->
            <div class="col-md-8">
                <!-- Informations de base -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Informations de base</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="form-label">Nom du produit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="slug" class="form-label">Slug (URL)</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $product->slug) }}">
                            <small class="form-text text-muted">Laissez vide pour générer automatiquement</small>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="short_description" class="form-label">Description courte</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" 
                                      id="short_description" name="short_description" rows="3">{{ old('short_description', $product->short_description) }}</textarea>
                            <small class="form-text text-muted">Description affichée dans les listes de produits</small>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Description complète <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="6" required>{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Prix et Stock -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Prix et Stock</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="price" class="form-label">Prix <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                               id="price" name="price" value="{{ old('price', $product->price) }}" 
                                               step="0.01" min="0" required>
                                        <span class="input-group-text">€</span>
                                    </div>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="compare_price" class="form-label">Prix comparatif</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('compare_price') is-invalid @enderror" 
                                               id="compare_price" name="compare_price" value="{{ old('compare_price', $product->compare_price) }}" 
                                               step="0.01" min="0">
                                        <span class="input-group-text">€</span>
                                    </div>
                                    <small class="form-text text-muted">Prix barré pour les promotions</small>
                                    @error('compare_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="sku" class="form-label">Code produit (SKU)</label>
                                    <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                           id="sku" name="sku" value="{{ old('sku', $product->sku) }}">
                                    @error('sku')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="stock_quantity" class="form-label">Quantité en stock <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                           id="stock_quantity" name="stock_quantity" value="{{ old('stock_quantity', $product->stock_quantity) }}" 
                                           min="0" required>
                                    @error('stock_quantity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="manage_stock" name="manage_stock" 
                                   {{ old('manage_stock', $product->manage_stock) ? 'checked' : '' }}>
                            <label class="form-check-label" for="manage_stock">
                                Gérer le stock automatiquement
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Informations écologiques -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Informations écologiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="materials" class="form-label">Matériaux utilisés</label>
                            <textarea class="form-control @error('materials') is-invalid @enderror" 
                                      id="materials" name="materials" rows="3">{{ old('materials', $product->materials) }}</textarea>
                            <small class="form-text text-muted">Ex: Plastique recyclé, Métal récupéré, etc.</small>
                            @error('materials')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="recycling_process" class="form-label">Processus de recyclage</label>
                            <textarea class="form-control @error('recycling_process') is-invalid @enderror" 
                                      id="recycling_process" name="recycling_process" rows="4">{{ old('recycling_process', $product->recycling_process) }}</textarea>
                            <small class="form-text text-muted">Décrivez comment ce produit a été créé à partir de déchets</small>
                            @error('recycling_process')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="environmental_impact_score" class="form-label">Score impact environnemental</label>
                                    <input type="range" class="form-range" id="environmental_impact_score" 
                                           name="environmental_impact_score" min="1" max="100" 
                                           value="{{ old('environmental_impact_score', $product->environmental_impact_score ?? 50) }}">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">1 (Faible)</small>
                                        <span id="score-value" class="badge badge-primary">{{ $product->environmental_impact_score ?? 50 }}</span>
                                        <small class="text-muted">100 (Excellent)</small>
                                    </div>
                                    @error('environmental_impact_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="weight" class="form-label">Poids</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('weight') is-invalid @enderror" 
                                               id="weight" name="weight" value="{{ old('weight', $product->weight) }}" 
                                               step="0.01" min="0">
                                        <span class="input-group-text">kg</span>
                                    </div>
                                    @error('weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="dimensions" class="form-label">Dimensions</label>
                            <input type="text" class="form-control @error('dimensions') is-invalid @enderror" 
                                   id="dimensions" name="dimensions" value="{{ old('dimensions', $product->dimensions) }}" 
                                   placeholder="L x l x H (en cm)">
                            @error('dimensions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Certifications écologiques</label>
                            <div class="row">
                                @php
                                    $certifications = ['eco_label', 'recyclable', 'biodegradable', 'energy_star', 'fair_trade', 'organic'];
                                    $certificationLabels = [
                                        'eco_label' => 'Écolabel',
                                        'recyclable' => 'Recyclable',
                                        'biodegradable' => 'Biodégradable',
                                        'energy_star' => 'Energy Star',
                                        'fair_trade' => 'Commerce équitable',
                                        'organic' => 'Biologique'
                                    ];
                                    $productCertifications = old('certifications', $product->certifications ?? []);
                                @endphp
                                @foreach($certifications as $cert)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               id="cert_{{ $cert }}" name="certifications[]" value="{{ $cert }}"
                                               {{ in_array($cert, $productCertifications) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cert_{{ $cert }}">
                                            {{ $certificationLabels[$cert] }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="tags" class="form-label">Tags</label>
                            <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                   id="tags" name="tags" value="{{ old('tags', is_array($product->tags) ? implode(', ', $product->tags) : '') }}" 
                                   placeholder="recyclage, écologique, durable (séparés par des virgules)">
                            <small class="form-text text-muted">Mots-clés pour améliorer la recherche</small>
                            @error('tags')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-md-4">
                <!-- Publication -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Publication</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="category_id" class="form-label">Catégorie <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id" required>
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="published_at" class="form-label">Date de publication</label>
                            <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror" 
                                   id="published_at" name="published_at" 
                                   value="{{ old('published_at', $product->published_at ? $product->published_at->format('Y-m-d\TH:i') : '') }}">
                            <small class="form-text text-muted">Laissez vide pour publier immédiatement si actif</small>
                            @error('published_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Produit actif
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   {{ old('is_featured', $product->is_featured) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                Produit en vedette
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Images actuelles -->
                @if($product->images || $product->gallery)
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Images actuelles</h5>
                    </div>
                    <div class="card-body">
                        @if($product->images)
                        <div class="mb-3">
                            <h6>Images principales</h6>
                            <div class="row">
                                @foreach($product->images as $image)
                                <div class="col-6 mb-2">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Image" 
                                         class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        @if($product->gallery)
                        <div class="mb-3">
                            <h6>Galerie</h6>
                            <div class="row">
                                @foreach($product->gallery as $image)
                                <div class="col-6 mb-2">
                                    <img src="{{ asset('storage/' . $image) }}" alt="Galerie" 
                                         class="img-fluid rounded" style="max-height: 80px; object-fit: cover;">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Nouvelles images -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">{{ $product->images ? 'Remplacer les images' : 'Ajouter des images' }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="images" class="form-label">Images principales</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" accept="image/*" multiple>
                            <small class="form-text text-muted">JPG, PNG, GIF, WEBP (max: 2MB chacune)</small>
                            @error('images.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="gallery" class="form-label">Galerie d'images</label>
                            <input type="file" class="form-control @error('gallery.*') is-invalid @enderror" 
                                   id="gallery" name="gallery[]" accept="image/*" multiple>
                            <small class="form-text text-muted">Images supplémentaires pour la galerie</small>
                            @error('gallery.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prévisualisation des nouvelles images -->
                        <div id="images-preview" class="mt-3"></div>
                        <div id="gallery-preview" class="mt-3"></div>
                    </div>
                </div>

                <!-- Statistiques -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Statistiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h4 class="text-primary">{{ $product->views_count }}</h4>
                                <small class="text-muted">Vues</small>
                            </div>
                            <div class="col-6">
                                <h4 class="text-info">{{ number_format($product->rating_average, 1) }}</h4>
                                <small class="text-muted">Note moyenne</small>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            <i class="fa fa-calendar"></i>
                            Créé le {{ $product->created_at->format('d/m/Y à H:i') }}
                        </small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Mettre à jour
                            </button>
                            <a href="{{ route('admin.products.show', $product) }}" class="btn btn-info">
                                <i class="fa fa-eye"></i> Voir le produit
                            </a>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Retour à la liste
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
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
    });

    // Update environmental score display
    $('#environmental_impact_score').on('input', function() {
        const score = $(this).val();
        $('#score-value').text(score);

        // Update badge color based on score
        $('#score-value').removeClass('badge-danger badge-warning badge-primary badge-info badge-success');
        if (score >= 80) {
            $('#score-value').addClass('badge-success');
        } else if (score >= 60) {
            $('#score-value').addClass('badge-info');
        } else if (score >= 40) {
            $('#score-value').addClass('badge-primary');
        } else if (score >= 20) {
            $('#score-value').addClass('badge-warning');
        } else {
            $('#score-value').addClass('badge-danger');
        }
    });

    // Convert tags input to array
    $('#tags').on('blur', function() {
        const tags = $(this).val().split(',').map(tag => tag.trim()).filter(tag => tag);
        $(this).val(tags.join(', '));
    });

    // Images preview
    $('#images').on('change', function() {
        previewImages(this, '#images-preview', 'Nouvelles images principales');
    });

    $('#gallery').on('change', function() {
        previewImages(this, '#gallery-preview', 'Nouvelle galerie');
    });

    function previewImages(input, container, title) {
        const files = input.files;
        const preview = $(container);
        preview.empty();

        if (files.length > 0) {
            preview.append(`<h6>${title}</h6>`);
            const row = $('<div class="row"></div>');

            Array.from(files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = $(`
                            <div class="col-6 mb-2">
                                <img src="${e.target.result}" alt="Preview ${index + 1}"
                                     class="img-fluid rounded" style="max-height: 100px; object-fit: cover;">
                                <small class="d-block text-muted">${file.name}</small>
                            </div>
                        `);
                        row.append(col);
                    };
                    reader.readAsDataURL(file);
                }
            });

            preview.append(row);
        }
    }

    // Form validation
    $('form').on('submit', function(e) {
        let isValid = true;

        // Check required fields
        const requiredFields = ['name', 'description', 'price', 'stock_quantity', 'category_id'];
        requiredFields.forEach(field => {
            const input = $(`#${field}`);
            if (!input.val() || input.val().trim() === '') {
                isValid = false;
                input.addClass('is-invalid');
            } else {
                input.removeClass('is-invalid');
            }
        });

        // Check price comparison
        const price = parseFloat($('#price').val());
        const comparePrice = parseFloat($('#compare_price').val());
        if (comparePrice && comparePrice <= price) {
            isValid = false;
            $('#compare_price').addClass('is-invalid');
            alert('Le prix comparatif doit être supérieur au prix de vente.');
        }

        if (!isValid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires correctement.');
        }
    });

    // Initialize score display
    $('#environmental_impact_score').trigger('input');
});
</script>
@endpush
