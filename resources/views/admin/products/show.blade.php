@extends('layouts.admin')

@section('title', 'Product Details')

@section('content')
<div class="page-inner">
    <div class="page-header">
        <h3 class="fw-bold mb-3">Product Details</h3>
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
                <a href="{{ route('admin.products.index') }}">Products</a>
            </li>
            <li class="separator">
                <i class="icon-arrow-right"></i>
            </li>
            <li class="nav-item">
                <a href="#">{{ $product->name }}</a>
            </li>
        </ul>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-md-8">
            <!-- Image gallery -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Product Images</h5>
                </div>
                <div class="card-body">
                    @if($product->images || $product->gallery)
                        <div class="row">
                            @if($product->images)
                                @foreach($product->images as $image)
                                <div class="col-md-3 mb-3">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" 
                                         class="img-fluid rounded shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                </div>
                                @endforeach
                            @endif
                            
                            @if($product->gallery)
                                @foreach($product->gallery as $image)
                                <div class="col-md-3 mb-3">
                                    <img src="{{ asset('storage/' . $image) }}" alt="{{ $product->name }}" 
                                         class="img-fluid rounded shadow-sm" style="height: 150px; object-fit: cover; width: 100%;">
                                </div>
                                @endforeach
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No images available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Detailed information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Detailed Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Product Name</strong></label>
                                <p class="form-control-static">{{ $product->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>SKU</strong></label>
                                <p class="form-control-static">{{ $product->sku }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Category</strong></label>
                                <p class="form-control-static">
                                    <span class="badge" style="background-color: {{ $product->category->color }}; color: white;">
                                        <i class="{{ $product->category->icon_class }}"></i>
                                        {{ $product->category->name }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Slug (URL)</strong></label>
                                <p class="form-control-static">{{ $product->slug }}</p>
                            </div>
                        </div>
                    </div>

                    @if($product->short_description)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Short Description</strong></label>
                                <p class="form-control-static">{{ $product->short_description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Full Description</strong></label>
                                <div class="form-control-static" style="white-space: pre-wrap;">{{ $product->description }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Price and Stock -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Price and Stock</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><strong>Price</strong></label>
                                <p class="form-control-static">
                                    <span class="h4 text-primary">{{ number_format($product->price, 2) }}€</span>
                                    @if($product->compare_price)
                                        <br><span class="text-muted text-decoration-line-through">
                                            {{ number_format($product->compare_price, 2) }}€
                                        </span>
                                        <span class="badge badge-success ms-1">
                                            -{{ $product->discount_percentage }}%
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><strong>Stock</strong></label>
                                <p class="form-control-static">
                                    <span class="badge badge-{{ $product->stock_status_color }} badge-lg">
                                        {{ $product->stock_quantity }} units
                                    </span>
                                    <br><small class="text-muted">{{ $product->stock_status_label }}</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><strong>Weight</strong></label>
                                <p class="form-control-static">
                                    {{ $product->weight ? number_format($product->weight, 2) . ' kg' : 'Not specified' }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="form-label"><strong>Dimensions</strong></label>
                                <p class="form-control-static">{{ $product->dimensions ?: 'Not specified' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Environmental Information -->
            @if($product->materials || $product->recycling_process || $product->environmental_impact_score || $product->certifications)
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Environmental Information</h5>
                </div>
                <div class="card-body">
                    @if($product->environmental_impact_score)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Environmental Impact Score</strong></label>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 me-3" style="height: 20px;">
                                        <div class="progress-bar bg-{{ $product->environmental_score_color }}" 
                                             role="progressbar" 
                                             style="width: {{ $product->environmental_impact_score }}%"
                                             aria-valuenow="{{ $product->environmental_impact_score }}" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                    <span class="badge badge-{{ $product->environmental_score_color }}">
                                        {{ $product->environmental_impact_score }}/100
                                    </span>
                                    <span class="ms-2 text-muted">{{ $product->environmental_score_label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($product->materials)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Materials Used</strong></label>
                                <p class="form-control-static">{{ $product->materials }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($product->recycling_process)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Recycling Process</strong></label>
                                <div class="form-control-static" style="white-space: pre-wrap;">{{ $product->recycling_process }}</div>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($product->certifications && count($product->certifications) > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Eco Certifications</strong></label>
                                <p class="form-control-static">
                                    @php
                                        $certificationLabels = [
                                            'eco_label' => 'Eco Label',
                                            'recyclable' => 'Recyclable',
                                            'biodegradable' => 'Biodegradable',
                                            'energy_star' => 'Energy Star',
                                            'fair_trade' => 'Fair Trade',
                                            'organic' => 'Organic'
                                        ];
                                    @endphp
                                    @foreach($product->certifications as $cert)
                                        <span class="badge badge-success me-1">
                                            <i class="fas fa-certificate"></i>
                                            {{ $certificationLabels[$cert] ?? $cert }}
                                        </span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($product->tags && count($product->tags) > 0)
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label"><strong>Tags</strong></label>
                                <p class="form-control-static">
                                    @foreach($product->tags as $tag)
                                        <span class="badge badge-info me-1">#{{ $tag }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- System Information -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">System Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Created by</strong></label>
                                <p class="form-control-static">
                                    {{ $product->creator->first_name }} {{ $product->creator->last_name }}
                                    <br><small class="text-muted">{{ $product->creator->email }}</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Created At</strong></label>
                                <p class="form-control-static">{{ $product->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Last Updated</strong></label>
                                <p class="form-control-static">{{ $product->updated_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label"><strong>Publish Date</strong></label>
                                <p class="form-control-static">
                                    {{ $product->published_at ? $product->published_at->format('Y-m-d H:i') : 'Not published' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Status and actions -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="card-title">Status and Actions</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Status:</span>
                            @if($product->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Publishing:</span>
                            @if($product->published_at)
                                <span class="badge badge-success">Published</span>
                            @else
                                <span class="badge badge-warning">Draft</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Featured:</span>
                            @if($product->is_featured)
                                <span class="badge badge-warning">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <span>Stock management:</span>
                            @if($product->manage_stock)
                                <span class="badge badge-info">Automatic</span>
                            @else
                                <span class="badge badge-secondary">Manual</span>
                            @endif
                        </div>
                    </div>

                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        
                        <form method="POST" action="{{ route('admin.products.toggle-status', $product) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $product->is_active ? 'warning' : 'success' }} w-100">
                                <i class="fa fa-{{ $product->is_active ? 'times' : 'check' }}"></i>
                                {{ $product->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.products.toggle-featured', $product) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $product->is_featured ? 'secondary' : 'warning' }} w-100">
                                <i class="fa fa-star"></i>
                                {{ $product->is_featured ? 'Unfeature' : 'Feature' }}
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.products.duplicate', $product) }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fa fa-copy"></i> Duplicate
                            </button>
                        </form>

                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                              onsubmit="return confirm('Are you sure you want to delete this product?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        </form>

                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to list
                        </a>
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h3 class="text-primary">{{ $product->views_count }}</h3>
                            <small class="text-muted">Views</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h3 class="text-info">{{ number_format($product->rating_average, 1) }}</h3>
                            <small class="text-muted">Average rating</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-success">{{ $product->rating_count }}</h3>
                            <small class="text-muted">Ratings</small>
                        </div>
                        <div class="col-6">
                            <h3 class="text-warning">{{ $product->stock_quantity }}</h3>
                            <small class="text-muted">In stock</small>
                        </div>
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
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>
@endpush
