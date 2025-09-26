@extends('layouts.admin')

@section('title', 'Category Details')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Category Details</h3>
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
                <a href="{{ route('admin.categories.index') }}">Categories</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ $category->name }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <!-- Informations de la catégorie -->
        <div class="col-md-4">
            <div class="card card-profile">
                <div class="card-header category-header" style="background: linear-gradient(135deg, {{ $category->color }}22, {{ $category->color }}44);">
                    <div class="profile-picture">
                        <div class="avatar avatar-xl">
                            @if($category->image)
                                <img src="{{ $category->image_url }}" alt="{{ $category->name }}" 
                                     class="avatar-img rounded-circle">
                            @else
                                <span class="avatar-title rounded-circle text-white" 
                                      style="background-color: {{ $category->color }}; font-size: 2rem;">
                                    <i class="{{ $category->icon_class }}"></i>
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="user-profile text-center">
                        <div class="name">{{ $category->name }}</div>
                        <div class="job">{{ $category->slug }}</div>
                        <div class="desc">
                            @if($category->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                            <span class="badge badge-info ms-1">Order: {{ $category->sort_order }}</span>
                        </div>
                    </div>
                    
                    @if($category->description)
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted">{{ $category->description }}</p>
                    </div>
                    @endif

                    <div class="card-footer">
                        <div class="row user-stats text-center">
                            <div class="col">
                                <div class="number">{{ $category->products->count() }}</div>
                                <div class="title">Produits</div>
                            </div>
                            <div class="col">
                                <div class="number">{{ $category->products->where('is_active', true)->count() }}</div>
                                <div class="title">Actifs</div>
                            </div>
                            <div class="col">
                                <div class="number">{{ $category->products->where('is_featured', true)->count() }}</div>
                                <div class="title">En vedette</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Actions</h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-primary">
                                <i class="fa fa-edit"></i> Edit
                        </a>
                        
                        <form method="POST" action="{{ route('admin.categories.toggle-status', $category) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $category->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fa fa-{{ $category->is_active ? 'times' : 'check' }}"></i>
                                {{ $category->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        @if(!$category->hasProducts())
                        <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this category?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>
                        @endif

                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to list
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Produits de la catégorie -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Products in this category</h4>
                        <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" 
                           class="btn btn-primary btn-round ms-auto">
                            <i class="fa fa-plus"></i>
                            Add Product
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($category->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="60">Image</th>
                                        <th>Name</th>
                                        <th width="100">Price</th>
                                        <th width="80">Stock</th>
                                        <th width="100">Status</th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->products as $product)
                                    <tr>
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
                                                <h6 class="mb-0">{{ $product->name }}</h6>
                                                <small class="text-muted">{{ $product->sku }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ number_format($product->price, 2) }} €</strong>
                                            @if($product->compare_price)
                                                <br><small class="text-muted text-decoration-line-through">
                                                    {{ number_format($product->compare_price, 2) }} €
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $product->stock_status_color }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($product->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                            @if($product->is_featured)
                                                <br><span class="badge badge-warning mt-1">Featured</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-button-action">
                                                <a href="{{ route('admin.products.show', $product) }}" 
                                                   class="btn btn-link btn-primary btn-lg" 
                                                   data-bs-toggle="tooltip" title="View">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.products.edit', $product) }}" 
                                                   class="btn btn-link btn-primary btn-lg" 
                                                   data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($category->products->count() > 10)
                        <div class="text-center mt-3">
                            <a href="{{ route('admin.products.index', ['category_id' => $category->id]) }}" 
                               class="btn btn-outline-primary">
                                View all products ({{ $category->products->count() }})
                            </a>
                        </div>
                        @endif
                    @else
                        <div class="empty-state text-center py-5">
                            <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No products in this category</h5>
                            <p class="text-muted">Start by adding your first product to this category.</p>
                            <a href="{{ route('admin.products.create', ['category_id' => $category->id]) }}" 
                               class="btn btn-primary">
                                <i class="fa fa-plus"></i> Add Product
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations détaillées -->
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Detailed Information</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Name</strong></label>
                                <p class="form-control-static">{{ $category->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Slug</strong></label>
                                <p class="form-control-static">{{ $category->slug }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Color</strong></label>
                                <p class="form-control-static">
                                    <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                        {{ $category->color }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Icon</strong></label>
                                <p class="form-control-static">
                                    <i class="{{ $category->icon_class }}"></i> {{ $category->icon }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Created At</strong></label>
                                <p class="form-control-static">{{ $category->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Last Updated</strong></label>
                                <p class="form-control-static">{{ $category->updated_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    @if($category->description)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Description</strong></label>
                                <p class="form-control-static">{{ $category->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.category-header {
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
