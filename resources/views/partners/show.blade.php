@extends('layouts.app')

@section('content')
<div class="container-xxl py-5" style="margin-top: 80px;">
    <div class="container">
        <!-- En-tête -->
        <div class="row mb-5">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('partners.front') }}" class="text-decoration-none">Partners</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $partner->name }}</li>
                    </ol>
                </nav>
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="display-5 fw-bold text-primary">{{ $partner->name }}</h1>
                    <a href="{{ route('partners.front') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Partners
                    </a>
                </div>
                @if($partner->type)
                    <span class="badge bg-primary fs-6 mt-2">{{ $partner->type }}</span>
                @endif
            </div>
        </div>

        <div class="row g-4">
            <!-- Informations du partenaire -->
            <div class="col-lg-4">
                <div class="card shadow-lg border-0 h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Informations</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Address</label>
                            <p class="mb-0">{{ $partner->address ?? 'Not provided' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Email</label>
                            <p class="mb-0">
                                @if($partner->email)
                                    <a href="mailto:{{ $partner->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-2"></i>{{ $partner->email }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold text-muted small">Phone</label>
                            <p class="mb-0">
                                @if($partner->phone)
                                    <a href="tel:{{ $partner->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-2"></i>{{ $partner->phone }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>
                        </div>

                        <!-- Statistiques rapides -->
                        <div class="mt-4 p-3 bg-light rounded">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h4 class="fw-bold text-primary mb-1">{{ $partner->warehouses->count() }}</h4>
                                    <small class="text-muted">Warehouses</small>
                                </div>
                                <div class="col-6">
                                    <h4 class="fw-bold text-success mb-1">
                                        {{ $partner->warehouses->where('status', 'active')->count() }}
                                    </h4>
                                    <small class="text-muted">Active</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section des entrepôts -->
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-warehouse me-2"></i>Warehouses Available</h5>
                    </div>
                    <div class="card-body">
                        @if($partner->warehouses->count() > 0)
                            <div class="row g-3">
                                @foreach($partner->warehouses->where('status', 'active') as $warehouse)
                                    <div class="col-md-6">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="card-title fw-bold text-primary mb-0">{{ $warehouse->name }}</h6>
                                                    <span class="badge bg-success">Available</span>
                                                </div>
                                                
                                                @if($warehouse->location)
                                                    <p class="text-muted small mb-2">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $warehouse->location }}
                                                    </p>
                                                @endif
                                                
                                                @if($warehouse->description)
                                                    <p class="small text-muted mb-3">{{ Str::limit($warehouse->description, 80) }}</p>
                                                @endif

                                                <!-- Capacité -->
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between small text-muted mb-1">
                                                        <span>capacity used</span>
                                                        <span>{{ $warehouse->occupancy_percentage }}%</span>
                                                    </div>
                                                    <div class="progress" style="height: 6px;">
                                                        <div class="progress-bar bg-{{ $warehouse->occupancy_percentage > 80 ? 'danger' : ($warehouse->occupancy_percentage > 50 ? 'warning' : 'success') }}" 
                                                             style="width: {{ $warehouse->occupancy_percentage }}%">
                                                        </div>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted mt-1">
                                                        <span>{{ number_format($warehouse->current_occupancy, 0, ',', ' ') }} m³</span>
                                                        <span>{{ number_format($warehouse->capacity, 0, ',', ' ') }} m³</span>
                                                    </div>
                                                </div>

                                                <!-- Disponibilité -->
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="small text-muted">
                                                        Available: <strong>{{ number_format($warehouse->available_capacity, 0, ',', ' ') }} m³</strong>
                                                    </span>
                                                    <a href="#" class="btn btn-sm btn-outline-primary" 
                                                       data-bs-toggle="modal" data-bs-target="#warehouseModal{{ $warehouse->id }}">
                                                        Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal pour les détails de l'entrepôt -->
                                    <div class="modal fade" id="warehouseModal{{ $warehouse->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title">{{ $warehouse->name }}</h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <h6>Informations</h6>
                                                            <p><strong>Localisation:</strong> {{ $warehouse->location ?? '-' }}</p>
                                                            <p><strong>Adresse:</strong> {{ $warehouse->address ?? '-' }}</p>
                                                            <p><strong>Ville:</strong> {{ $warehouse->city ?? '-' }}</p>
                                                            <p><strong>Pays:</strong> {{ $warehouse->country ?? '-' }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <h6>Capacité</h6>
                                                            <div class="mb-3">
                                                                <strong>Totale:</strong> {{ number_format($warehouse->capacity, 0, ',', ' ') }} m³<br>
                                                                <strong>Utilisée:</strong> {{ number_format($warehouse->current_occupancy, 0, ',', ' ') }} m³<br>
                                                                <strong>Disponible:</strong> {{ number_format($warehouse->available_capacity, 0, ',', ' ') }} m³
                                                            </div>
                                                            <h6>Contact</h6>
                                                            <p>
                                                                @if($warehouse->contact_person)
                                                                    <strong>Personne:</strong> {{ $warehouse->contact_person }}<br>
                                                                @endif
                                                                @if($warehouse->contact_phone)
                                                                    <strong>Tél:</strong> {{ $warehouse->contact_phone }}<br>
                                                                @endif
                                                                @if($warehouse->contact_email)
                                                                    <strong>Email:</strong> {{ $warehouse->contact_email }}
                                                                @endif
                                                            </p>
                                                        </div>
                                                    </div>
                                                    @if($warehouse->description)
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <h6>Description</h6>
                                                                <p class="text-muted">{{ $warehouse->description }}</p>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                    <a href="tel:{{ $partner->phone ?? $warehouse->contact_phone }}" class="btn btn-primary">
                                                        <i class="fas fa-phone me-2"></i>Contacter
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Entrepôts inactifs (si besoin) -->
                            @if($partner->warehouses->where('status', '!=', 'active')->count() > 0)
                                <div class="mt-4">
                                    <h6 class="text-muted">Warehouses Temporarily Unavailable</h6>
                                    <div class="row g-2">
                                        @foreach($partner->warehouses->where('status', '!=', 'active') as $warehouse)
                                            <div class="col-12">
                                                <div class="card border-0 bg-light">
                                                    <div class="card-body py-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="text-muted">
                                                                <i class="fas fa-warehouse me-2"></i>{{ $warehouse->name }}
                                                            </span>
                                                            <span class="badge bg-secondary">
                                                                {{ $warehouse->status === 'maintenance' ? 'In Maintenance' : 'Inactive' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-warehouse fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucun entrepôt disponible</h5>
                                <p class="text-muted">Ce partenaire ne dispose pas d'entrepôts actifs pour le moment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-info text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Localisation des Entrepôts</h5>
                    </div>
                    <div class="card-body">
                        <div id="partnerWarehousesMap" style="height: 400px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const points = @json($warehousePoints ?? []);
    const map = L.map('partnerWarehousesMap', { zoomControl: true }).setView([34.0, 9.0], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.control.scale({ position: 'bottomleft', imperial: false }).addTo(map);

    const bounds = L.latLngBounds();
    let hasAny = false;

    points.forEach(p => {
        if (!p.latitude || !p.longitude) return;
        const lat = parseFloat(p.latitude);
        const lng = parseFloat(p.longitude);
        if (isNaN(lat) || isNaN(lng)) return;

        hasAny = true;
        const popup = `<strong>${escapeHtml(p.name)}</strong><br>${escapeHtml(p.address || '')}<br><a href="#warehouseModal${p.id}" data-bs-toggle="modal">Details</a>`;
        L.marker([lat, lng]).addTo(map).bindPopup(popup);
        bounds.extend([lat, lng]);
    });

    if (hasAny) {
        map.fitBounds(bounds, { padding: [40, 40] });
    } else {
        map.setView([34.0, 9.0], 6);
        // optional: show message or partner address marker if you have partner coords
    }

    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/[&<>"'`=\/]/g, function (c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;','/':'&#x2F;','`':'&#x60;','=':'&#x3D;'}[c];
        });
    }
});
</script>
<style>#partnerWarehousesMap{min-height:200px}</style>

<style>
.card {
    border-radius: 15px;
    transition: transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
.card-header {
    border-radius: 15px 15px 0 0 !important;
}
.breadcrumb {
    background: transparent;
    padding: 0;
}
.progress {
    border-radius: 10px;
}
.badge {
    font-size: 0.7rem;
}
.modal-content {
    border-radius: 15px;
    border: none;
}
</style>

<!-- Inclusion de Bootstrap JS pour les modals -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection