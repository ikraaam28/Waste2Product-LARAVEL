@extends('layouts.app')


@section('title', 'Nos Produits - Waste2Product')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <!-- En-tête -->
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Nos Produits</p>
            <h1 class="display-6">
                @if($selectedCategory)
                    {{ $selectedCategory->name }}
                @else
                    Découvrez nos produits éco-responsables
                @endif
            </h1>
            @if($selectedCategory)
                <p class="text-muted">{{ $selectedCategory->description }}</p>
            @else
                <p class="text-muted">Des produits innovants créés à partir de déchets recyclés pour un avenir durable</p>
            @endif
        </div>

        <!-- Filtres par catégorie -->
        @if(!$selectedCategory && $categories->count() > 1)
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8">
                <div class="text-center">
                    <a href="{{ route('products') }}" class="btn btn-outline-primary me-2 mb-2 {{ !request('category') ? 'active' : '' }}">
                        Toutes les catégories
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('products', ['category' => $category->slug]) }}"
                           class="btn btn-outline-primary me-2 mb-2 {{ request('category') == $category->slug ? 'active' : '' }}">
                            <i class="{{ $category->icon ?? 'fas fa-tag' }}"></i>
                            {{ $category->name }}
                            <span class="badge bg-primary ms-1">{{ $category->products->count() }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <!-- Produits par catégorie -->
        @if($categories->count() > 0)
            @foreach($categories as $category)
                @if($category->products->count() > 0)
                    <div class="category-section mb-5">
                        <!-- En-tête de catégorie -->
                        @if(!$selectedCategory)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon me-3" style="background-color: {{ $category->color ?? '#007bff' }};">
                                            <i class="{{ $category->icon ?? 'fas fa-tag' }} text-white"></i>
                                        </div>
                                        <div>
                                            <h3 class="mb-1">{{ $category->name }}</h3>
                                            <p class="text-muted mb-0">{{ $category->description }}</p>
                                        </div>
                                    </div>
                                    <a href="{{ route('products.category', $category->slug) }}" class="btn btn-outline-primary">
                                        Voir tout <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Produits de la catégorie -->
                        <div class="row g-4">
                            @foreach($category->products->take($selectedCategory ? 12 : 4) as $product)
                                <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ 0.1 * $loop->iteration }}s">
                                    <div class="product-item text-center h-100">
                                        <!-- Image du produit -->
                                        <div class="product-image-container position-relative" style="height: 200px; width: 100%; border-radius: 15px; overflow: hidden; background: #f8f9fa;">
                                            <img src="{{ $product->first_image_url }}"
                                                 alt="{{ $product->name }}"
                                                 style="width: 100%; height: 200px; object-fit: cover; display: block; max-width: 100%; max-height: 200px;"
                                                 class="product-image-fixed">

                                            <!-- Badges -->
                                            <div class="product-badges position-absolute top-0 start-0 p-2">
                                                @if($product->is_featured)
                                                    <span class="badge bg-warning text-dark mb-1 d-block">
                                                        <i class="fas fa-star"></i> Vedette
                                                    </span>
                                                @endif
                                                @if($product->compare_price && $product->compare_price > $product->price)
                                                    <span class="badge bg-danger mb-1 d-block">
                                                        -{{ $product->discount_percentage }}%
                                                    </span>
                                                @endif
                                                @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                                    <span class="badge bg-warning text-dark mb-1 d-block">
                                                        Stock limité
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Actions au survol -->
                                            <div class="product-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                                <div class="product-actions">
                                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-primary btn-sm me-2">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <button class="btn btn-outline-primary btn-sm" onclick="addToWishlist({{ $product->id }})">
                                                        <i class="fas fa-heart"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Informations du produit -->
                                        <div class="bg-white shadow-sm text-center p-4 position-relative mt-n5 mx-4 product-info">
                                            <h5 class="text-primary mb-2">{{ $product->name }}</h5>
                                            <p class="text-body mb-3">{{ Str::limit($product->short_description ?: $product->description, 80) }}</p>

                                            <!-- Prix -->
                                            <div class="product-price mb-3">
                                                @if($product->compare_price && $product->compare_price > $product->price)
                                                    <span class="text-muted text-decoration-line-through me-2">{{ number_format($product->compare_price, 2) }} €</span>
                                                @endif
                                                <span class="text-primary fw-bold fs-5">{{ number_format($product->price, 2) }} €</span>
                                            </div>

                                            <!-- Score environnemental -->
                                            @if($product->environmental_impact_score)
                                            <div class="environmental-score mb-3">
                                                <small class="text-muted">Impact environnemental:</small>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-success" style="width: {{ $product->environmental_impact_score }}%"></div>
                                                </div>
                                                <small class="text-success">{{ $product->environmental_impact_score }}/100</small>
                                            </div>
                                            @endif

                                            <!-- Stock -->
                                            <div class="stock-info mb-3">
                                                @if($product->stock_status === 'in_stock')
                                                    <small class="text-success">
                                                        <i class="fas fa-check-circle"></i> En stock ({{ $product->stock_quantity }})
                                                    </small>
                                                @elseif($product->stock_status === 'out_of_stock')
                                                    <small class="text-danger">
                                                        <i class="fas fa-times-circle"></i> Rupture de stock
                                                    </small>
                                                @else
                                                    <small class="text-warning">
                                                        <i class="fas fa-clock"></i> Sur commande
                                                    </small>
                                                @endif
                                            </div>

                                            <!-- Bouton d'action -->
                                            <div class="product-actions">
                                                @if($product->stock_status === 'in_stock')
                                                    <button class="btn btn-primary btn-sm" onclick="addToCart({{ $product->id }})">
                                                        <i class="fas fa-shopping-cart"></i> Ajouter au panier
                                                    </button>
                                                @else
                                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-info-circle"></i> Plus d'infos
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        @else
            <!-- Aucun produit trouvé -->
            <div class="row">
                <div class="col-12 text-center">
                    <div class="no-products py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucun produit disponible</h4>
                        <p class="text-muted">
                            @if($selectedCategory)
                                Aucun produit n'est actuellement disponible dans cette catégorie.
                            @else
                                Aucun produit n'est actuellement disponible. Revenez bientôt !
                            @endif
                        </p>
                        @if($selectedCategory)
                            <a href="{{ route('products') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left"></i> Voir toutes les catégories
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
/* Force la taille des images de produits */
.product-img, .product-image img, .product-image-fixed {
    width: 100% !important;
    height: 200px !important;
    max-height: 200px !important;
    min-height: 200px !important;
    object-fit: cover !important;
    display: block !important;
}

.product-image-container {
    width: 100% !important;
    height: 200px !important;
    max-height: 200px !important;
    min-height: 200px !important;
    overflow: hidden !important;
}
.category-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.product-item {
    transition: transform 0.3s ease;
}

.product-item:hover {
    transform: translateY(-5px);
}

.product-image {
    height: 200px !important;
    max-height: 200px !important;
    border-radius: 15px;
    overflow: hidden;
    background: #f8f9fa;
    position: relative;
}

.product-image img {
    width: 100% !important;
    height: 200px !important;
    max-height: 200px !important;
    object-fit: cover;
    transition: transform 0.3s ease;
    display: block;
}

.product-item:hover .product-image img {
    transform: scale(1.05);
}

.product-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border: none;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    max-width: 100%;
    border-radius: 15px;
    overflow: hidden;
}

.product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

/* Forcer la taille des colonnes */
.col-lg-3 {
    max-width: 25%;
    flex: 0 0 25%;
}

.col-md-6 {
    max-width: 50%;
    flex: 0 0 50%;
}

@media (max-width: 768px) {
    .col-lg-3, .col-md-6 {
        max-width: 100%;
        flex: 0 0 100%;
    }
}

.product-content {
    padding: 1.5rem;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.product-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #27ae60;
    margin-bottom: 1rem;
}

.environmental-score .progress {
    background-color: #f8f9fa;
}

.btn-group .btn {
    border-radius: 6px;
    font-weight: 500;
}

.product-overlay {
    background: rgba(0,0,0,0.7);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-item:hover .product-overlay {
    opacity: 1;
}

.product-actions .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.product-info {
    border-radius: 15px;
}

.product-badges .badge {
    font-size: 0.75rem;
}

.product-overlay {
    background: rgba(0, 123, 255, 0.8);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.product-item:hover .product-overlay {
    opacity: 1;
}

.product-info {
    border-radius: 0 0 10px 10px;
    min-height: 280px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.product-badges {
    z-index: 2;
}

.btn-outline-primary.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}

.progress {
    background-color: #e9ecef;
}

.no-products {
    padding: 4rem 2rem;
}

@media (max-width: 768px) {
    .category-icon {
        width: 50px;
        height: 50px;
        font-size: 1.2rem;
    }

    .product-info {
        min-height: auto;
    }
}
</style>
@endpush

@push('scripts')
<script>
function addToCart(productId) {
    // Fonction pour ajouter au panier (à implémenter)
    alert('Produit ajouté au panier ! (Fonctionnalité à implémenter)');
}

function addToWishlist(productId) {
    // Fonction pour ajouter à la liste de souhaits (à implémenter)
    alert('Produit ajouté à la liste de souhaits ! (Fonctionnalité à implémenter)');
}
</script>
@endpush

