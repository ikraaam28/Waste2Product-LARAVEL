@extends('layouts.admin')

@section('title', 'Gestion des Produits')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestion des Produits</h3>
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
                <a href="#">Catalogue</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">Produits</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Liste des Produits</h4>
                        <div class="ms-auto">
                            <a href="{{ route('admin.products.export') }}" class="btn btn-success btn-round me-2">
                                <i class="fa fa-download"></i>
                                Exporter CSV
                            </a>
                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-round">
                                <i class="fa fa-plus"></i>
                                Ajouter Produit
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres et Recherche -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Rechercher</label>
                                        <input type="text" class="form-control" name="search" 
                                               value="{{ request('search') }}" 
                                               placeholder="Nom, SKU, description...">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Catégorie</label>
                                        <select class="form-select" name="category_id">
                                            <option value="">Toutes les catégories</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" 
                                                        {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Statut</label>
                                        <select class="form-select" name="status">
                                            <option value="">Tous les statuts</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                            <option value="featured" {{ request('status') == 'featured' ? 'selected' : '' }}>En vedette</option>
                                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Stock</label>
                                        <select class="form-select" name="stock_status">
                                            <option value="">Tous les stocks</option>
                                            <option value="in_stock" {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>En stock</option>
                                            <option value="out_of_stock" {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>Rupture</option>
                                            <option value="on_backorder" {{ request('stock_status') == 'on_backorder' ? 'selected' : '' }}>En commande</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">Trier par</label>
                                        <select class="form-select" name="sort">
                                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date création</option>
                                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Prix</option>
                                            <option value="stock_quantity" {{ request('sort') == 'stock_quantity' ? 'selected' : '' }}>Stock</option>
                                            <option value="views_count" {{ request('sort') == 'views_count' ? 'selected' : '' }}>Vues</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-2">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total</p>
                                                <h4 class="card-title">{{ $products->total() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-success bubble-shadow-small">
                                                <i class="fas fa-check-circle"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Actifs</p>
                                                <h4 class="card-title">{{ $products->where('is_active', true)->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                <i class="fas fa-star"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Vedette</p>
                                                <h4 class="card-title">{{ $products->where('is_featured', true)->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-danger bubble-shadow-small">
                                                <i class="fas fa-exclamation-triangle"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Rupture</p>
                                                <h4 class="card-title">{{ $products->where('stock_status', 'out_of_stock')->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-eye"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Vues</p>
                                                <h4 class="card-title">{{ $products->sum('views_count') }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-secondary bubble-shadow-small">
                                                <i class="fas fa-euro-sign"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Valeur</p>
                                                <h4 class="card-title">{{ number_format($products->sum('price'), 0) }}€</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions en lot -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <form id="bulk-action-form" method="POST" action="{{ route('admin.products.bulk-action') }}">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <select class="form-select me-2" name="action" style="width: auto;">
                                        <option value="">Actions en lot</option>
                                        <option value="activate">Activer</option>
                                        <option value="deactivate">Désactiver</option>
                                        <option value="feature">Mettre en vedette</option>
                                        <option value="unfeature">Retirer de la vedette</option>
                                        <option value="delete">Supprimer</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary btn-sm" disabled id="bulk-action-btn">
                                        Appliquer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des produits -->
                    <div class="table-responsive">
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th width="60">Image</th>
                                    <th>Produit</th>
                                    <th width="120">Catégorie</th>
                                    <th width="100">Prix</th>
                                    <th width="80">Stock</th>
                                    <th width="100">Statut</th>
                                    <th width="80">Vues</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="product-checkbox" name="products[]" value="{{ $product->id }}">
                                    </td>
                                    <td>
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ asset('storage/' . $product->images[0]) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="avatar avatar-sm bg-secondary">
                                                <i class="fas fa-image text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <h6 class="mb-0">{{ Str::limit($product->name, 30) }}</h6>
                                            <small class="text-muted">{{ $product->sku }}</small>
                                            @if($product->is_featured)
                                                <span class="badge badge-warning badge-sm ms-1">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: {{ $product->category->color }}; color: white;">
                                            {{ $product->category->name }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($product->price, 2) }}€</strong>
                                        @if($product->compare_price)
                                            <br><small class="text-muted text-decoration-line-through">
                                                {{ number_format($product->compare_price, 2) }}€
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $product->stock_status_color }}">
                                            {{ $product->stock_quantity }}
                                        </span>
                                        <br><small class="text-muted">{{ $product->stock_status_label }}</small>
                                    </td>
                                    <td>
                                        @if($product->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-danger">Inactif</span>
                                        @endif
                                        @if($product->published_at)
                                            <br><small class="text-success">Publié</small>
                                        @else
                                            <br><small class="text-warning">Brouillon</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $product->views_count }}</span>
                                    </td>
                                    <td>
                                        <div class="form-button-action">
                                            <a href="{{ route('admin.products.show', $product) }}" 
                                               class="btn btn-link btn-primary btn-lg" 
                                               data-bs-toggle="tooltip" title="Voir">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.products.edit', $product) }}" 
                                               class="btn btn-link btn-primary btn-lg" 
                                               data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-link btn-{{ $product->is_active ? 'warning' : 'success' }} btn-lg" 
                                                        data-bs-toggle="tooltip" 
                                                        title="{{ $product->is_active ? 'Désactiver' : 'Activer' }}">
                                                    <i class="fa fa-{{ $product->is_active ? 'times' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.products.toggle-featured', $product) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-link btn-{{ $product->is_featured ? 'secondary' : 'warning' }} btn-lg" 
                                                        data-bs-toggle="tooltip" 
                                                        title="{{ $product->is_featured ? 'Retirer vedette' : 'Mettre en vedette' }}">
                                                    <i class="fa fa-star"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.products.duplicate', $product) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                <button type="submit" 
                                                        class="btn btn-link btn-info btn-lg" 
                                                        data-bs-toggle="tooltip" title="Dupliquer">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-link btn-danger btn-lg" 
                                                        data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucun produit trouvé</h5>
                                            <p class="text-muted">Commencez par créer votre premier produit.</p>
                                            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Créer un produit
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($products->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Select all checkbox
    $('#select-all').change(function() {
        $('.product-checkbox').prop('checked', this.checked);
        toggleBulkActionButton();
    });

    // Individual checkboxes
    $('.product-checkbox').change(function() {
        toggleBulkActionButton();

        // Update select all checkbox
        const totalCheckboxes = $('.product-checkbox').length;
        const checkedCheckboxes = $('.product-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Toggle bulk action button
    function toggleBulkActionButton() {
        const checkedCount = $('.product-checkbox:checked').length;
        $('#bulk-action-btn').prop('disabled', checkedCount === 0);
    }

    // Bulk action form submission
    $('#bulk-action-form').submit(function(e) {
        const action = $('select[name="action"]').val();
        const checkedCount = $('.product-checkbox:checked').length;

        if (!action) {
            e.preventDefault();
            alert('Veuillez sélectionner une action.');
            return;
        }

        if (checkedCount === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins un produit.');
            return;
        }

        // Add selected products to form
        $('.product-checkbox:checked').each(function() {
            $('#bulk-action-form').append('<input type="hidden" name="products[]" value="' + $(this).val() + '">');
        });

        // Confirm action
        let actionText = '';
        switch(action) {
            case 'delete': actionText = 'supprimer'; break;
            case 'activate': actionText = 'activer'; break;
            case 'deactivate': actionText = 'désactiver'; break;
            case 'feature': actionText = 'mettre en vedette'; break;
            case 'unfeature': actionText = 'retirer de la vedette'; break;
        }

        let message = `Êtes-vous sûr de vouloir ${actionText} ${checkedCount} produit(s) ?`;
        if (!confirm(message)) {
            e.preventDefault();
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
