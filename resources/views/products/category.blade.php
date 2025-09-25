@extends('layouts.app')

@section('title', $category->name . ' - Waste2Product')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <!-- En-tête de catégorie -->
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
            <div class="category-header mb-4">
                <div class="category-icon mx-auto mb-3" style="background-color: {{ $category->color ?? '#007bff' }};">
                    <i class="{{ $category->icon ?? 'fas fa-tag' }} text-white"></i>
                </div>
                <h1 class="display-6">{{ $category->name }}</h1>
                <p class="text-muted">{{ $category->description }}</p>
            </div>
        </div>

        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item"><a href="{{ route('products') }}">Produits</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
            </ol>
        </nav>

        <!-- Filtres et tri -->
        <div class="row mb-4">
            <div class="col-md-6">
                <p class="text-muted mb-0">
                    {{ $products->total() }} produit(s) trouvé(s)
                </p>
            </div>
            <div class="col-md-6 text-end">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        Trier par
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="?sort=name">Nom A-Z</a></li>
                        <li><a class="dropdown-item" href="?sort=price_asc">Prix croissant</a></li>
                        <li><a class="dropdown-item" href="?sort=price_desc">Prix décroissant</a></li>
                        <li><a class="dropdown-item" href="?sort=newest">Plus récent</a></li>
                        <li><a class="dropdown-item" href="?sort=popular">Plus populaire</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Produits -->
        @if($products->count() > 0)
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-lg-3 col-md-6 wow fadeInUp" data-wow-delay="{{ 0.1 * $loop->iteration }}s">
                        <div class="product-item text-center h-100">
                            <!-- Image du produit -->
                            <div class="product-image position-relative overflow-hidden">
                                <img class="img-fluid" src="{{ $product->first_image_url }}" alt="{{ $product->name }}">
                                
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

            <!-- Pagination -->
            <div class="row mt-5">
                <div class="col-12">
                    <div class="d-flex justify-content-center">
                        {{ $products->links() }}
                    </div>
                </div>
            </div>
        @else
            <!-- Aucun produit trouvé -->
            <div class="row">
                <div class="col-12 text-center">
                    <div class="no-products py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucun produit disponible</h4>
                        <p class="text-muted">Aucun produit n'est actuellement disponible dans cette catégorie.</p>
                        <a href="{{ route('products') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voir toutes les catégories
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.product-image {
    height: 200px;
    border-radius: 10px 10px 0 0;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-item:hover .product-image img {
    transform: scale(1.05);
}

.product-item {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.product-item:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('styles')
<style>
.category-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
}

.product-item {
    transition: transform 0.3s ease;
}

.product-item:hover {
    transform: translateY(-5px);
}

.product-image {
    height: 250px;
    border-radius: 10px 10px 0 0;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
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

.progress {
    background-color: #e9ecef;
}

.no-products {
    padding: 4rem 2rem;
}
</style>
@endpush

@push('scripts')
<script>
function addToCart(productId) {
    alert('Produit ajouté au panier ! (Fonctionnalité à implémenter)');
}

function addToWishlist(productId) {
    alert('Produit ajouté à la liste de souhaits ! (Fonctionnalité à implémenter)');
}
</script>
@endpush
