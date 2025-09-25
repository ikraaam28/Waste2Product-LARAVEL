@extends('layouts.admin')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Gestion des Catégories</h3>
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
                <a href="#">Catégories</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Liste des Catégories</h4>
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-plus"></i>
                            Ajouter Catégorie
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres et Recherche -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="form-label">Rechercher</label>
                                        <input type="text" class="form-control" name="search" 
                                               value="{{ request('search') }}" 
                                               placeholder="Nom, description...">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Statut</label>
                                        <select class="form-select" name="status">
                                            <option value="">Tous les statuts</option>
                                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="form-label">Trier par</label>
                                        <select class="form-select" name="sort">
                                            <option value="sort_order" {{ request('sort') == 'sort_order' ? 'selected' : '' }}>Ordre</option>
                                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Nom</option>
                                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Date de création</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fa fa-search"></i> Filtrer
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                <i class="fas fa-tags"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Total Catégories</p>
                                                <h4 class="card-title">{{ $categories->total() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                                                <p class="card-category">Actives</p>
                                                <h4 class="card-title">{{ $categories->where('is_active', true)->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-warning bubble-shadow-small">
                                                <i class="fas fa-times-circle"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Inactives</p>
                                                <h4 class="card-title">{{ $categories->where('is_active', false)->count() }}</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-stats card-round">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-icon">
                                            <div class="icon-big text-center icon-info bubble-shadow-small">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        </div>
                                        <div class="col col-stats ms-3 ms-sm-0">
                                            <div class="numbers">
                                                <p class="card-category">Avec Produits</p>
                                                <h4 class="card-title">{{ $categories->filter(function($cat) { return ($cat->products_count ?? 0) > 0; })->count() }}</h4>
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
                            <form id="bulk-action-form" method="POST" action="{{ route('admin.categories.bulk-action') }}">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <select class="form-select me-2" name="action" style="width: auto;">
                                        <option value="">Actions en lot</option>
                                        <option value="activate">Activer</option>
                                        <option value="deactivate">Désactiver</option>
                                        <option value="delete">Supprimer</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary btn-sm" disabled id="bulk-action-btn">
                                        Appliquer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Tableau des catégories -->
                    <div class="table-responsive">
                        <table class="table table-striped mt-3">
                            <thead>
                                <tr>
                                    <th width="30">
                                        <input type="checkbox" id="select-all">
                                    </th>
                                    <th width="60">Image</th>
                                    <th>Nom</th>
                                    <th>Description</th>
                                    <th width="100">Produits</th>
                                    <th width="80">Ordre</th>
                                    <th width="100">Statut</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <input type="checkbox" class="category-checkbox" name="categories[]" value="{{ $category->id }}">
                                    </td>
                                    <td>
                                        @if($category->image)
                                            <img src="{{ $category->image_url }}" alt="{{ $category->name }}" 
                                                 class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="avatar avatar-sm" style="background-color: {{ $category->color }};">
                                                <i class="{{ $category->icon_class }} text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $category->name }}</h6>
                                                <small class="text-muted">{{ $category->slug }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            {{ Str::limit($category->description, 50) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $category->products_count ?? 0 }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $category->sort_order }}</span>
                                    </td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-button-action">
                                            <a href="{{ route('admin.categories.show', $category) }}" 
                                               class="btn btn-link btn-primary btn-lg" 
                                               data-bs-toggle="tooltip" title="Voir">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-link btn-primary btn-lg" 
                                               data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" 
                                                  style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" 
                                                        class="btn btn-link btn-{{ $category->is_active ? 'warning' : 'success' }} btn-lg" 
                                                        data-bs-toggle="tooltip" 
                                                        title="{{ $category->is_active ? 'Désactiver' : 'Activer' }}">
                                                    <i class="fa fa-{{ $category->is_active ? 'times' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            @if(!$category->hasProducts())
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                                  style="display: inline;" 
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-link btn-danger btn-lg" 
                                                        data-bs-toggle="tooltip" title="Supprimer">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Aucune catégorie trouvée</h5>
                                            <p class="text-muted">Commencez par créer votre première catégorie.</p>
                                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                                <i class="fa fa-plus"></i> Créer une catégorie
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($categories->hasPages())
                    <div class="d-flex justify-content-center mt-4">
                        {{ $categories->links() }}
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
        $('.category-checkbox').prop('checked', this.checked);
        toggleBulkActionButton();
    });

    // Individual checkboxes
    $('.category-checkbox').change(function() {
        toggleBulkActionButton();
        
        // Update select all checkbox
        const totalCheckboxes = $('.category-checkbox').length;
        const checkedCheckboxes = $('.category-checkbox:checked').length;
        $('#select-all').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Toggle bulk action button
    function toggleBulkActionButton() {
        const checkedCount = $('.category-checkbox:checked').length;
        $('#bulk-action-btn').prop('disabled', checkedCount === 0);
    }

    // Bulk action form submission
    $('#bulk-action-form').submit(function(e) {
        const action = $('select[name="action"]').val();
        const checkedCount = $('.category-checkbox:checked').length;
        
        if (!action) {
            e.preventDefault();
            alert('Veuillez sélectionner une action.');
            return;
        }
        
        if (checkedCount === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une catégorie.');
            return;
        }
        
        // Add selected categories to form
        $('.category-checkbox:checked').each(function() {
            $('#bulk-action-form').append('<input type="hidden" name="categories[]" value="' + $(this).val() + '">');
        });
        
        // Confirm action
        let message = `Êtes-vous sûr de vouloir ${action === 'delete' ? 'supprimer' : (action === 'activate' ? 'activer' : 'désactiver')} ${checkedCount} catégorie(s) ?`;
        if (!confirm(message)) {
            e.preventDefault();
        }
    });

    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
