@extends('layouts.admin')
@section('title', 'Gestion des Badges')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Gestion des Badges</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Badges</li>
            </ul>
        </div>

        <!-- Actions -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Badges Disponibles</h5>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createBadgeModal">
                                <i class="fa fa-plus"></i> Créer un Badge
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des Badges -->
        <div class="row">
            @forelse($badges as $badge)
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <i class="{{ $badge->icon ?: 'fa fa-medal' }} fa-3x" style="color: {{ $badge->color }}"></i>
                            </div>
                            <h5 class="card-title">{{ $badge->name }}</h5>
                            <p class="card-text text-muted">{{ $badge->description }}</p>
                            
                            <div class="mb-3">
                                <span class="badge badge-{{ $badge->is_active ? 'success' : 'danger' }}">
                                    {{ $badge->is_active ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>

                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <small class="text-muted">Critère</small>
                                    <br>
                                    <strong>{{ ucfirst($badge->criteria_type) }}</strong>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Valeur</small>
                                    <br>
                                    <strong>{{ $badge->criteria_value }}</strong>
                                </div>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Points requis: </small>
                                <strong>{{ $badge->points_required }}</strong>
                            </div>

                            <div class="mb-3">
                                <small class="text-muted">Utilisateurs ayant ce badge: </small>
                                <strong>{{ $badge->users->count() }}</strong>
                            </div>

                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-info" onclick="viewBadgeUsers({{ $badge->id }})">
                                    <i class="fa fa-users"></i> Voir Utilisateurs
                                </button>
                                <button class="btn btn-sm btn-warning" onclick="editBadge({{ $badge->id }})">
                                    <i class="fa fa-edit"></i> Modifier
                                </button>
                                <button class="btn btn-sm btn-{{ $badge->is_active ? 'secondary' : 'success' }}" 
                                        onclick="toggleBadgeStatus({{ $badge->id }})">
                                    <i class="fa fa-{{ $badge->is_active ? 'pause' : 'play' }}"></i>
                                    {{ $badge->is_active ? 'Désactiver' : 'Activer' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fa fa-medal fa-3x text-muted mb-3"></i>
                            <h5>Aucun badge créé</h5>
                            <p class="text-muted">Créez votre premier badge pour commencer la gamification</p>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createBadgeModal">
                                <i class="fa fa-plus"></i> Créer le premier badge
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Créer Badge -->
<div class="modal fade" id="createBadgeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Créer un Nouveau Badge</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.events.create-badge') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Nom du Badge *</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="icon">Icône</label>
                                <input type="text" class="form-control" id="icon" name="icon" 
                                       placeholder="fa fa-medal" value="fa fa-medal">
                                <small class="form-text text-muted">Classe FontAwesome (ex: fa fa-medal)</small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="color">Couleur</label>
                                <input type="color" class="form-control" id="color" name="color" value="#ffc107">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="criteria_type">Type de Critère *</label>
                                <select class="form-control" id="criteria_type" name="criteria_type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="events_participated">Événements participés</option>
                                    <option value="events_created">Événements créés</option>
                                    <option value="recycled_quantity">Quantité recyclée</option>
                                    <option value="co2_saved">CO₂ économisé</option>
                                    <option value="feedback_given">Feedbacks donnés</option>
                                    <option value="points_earned">Points gagnés</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="criteria_value">Valeur du Critère *</label>
                                <input type="number" class="form-control" id="criteria_value" name="criteria_value" 
                                       min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="points_required">Points Requis *</label>
                                <input type="number" class="form-control" id="points_required" name="points_required" 
                                       min="0" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le Badge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Utilisateurs du Badge -->
<div class="modal fade" id="badgeUsersModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Utilisateurs ayant ce Badge</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="badgeUsersContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewBadgeUsers(badgeId) {
    // Simuler le chargement des utilisateurs
    document.getElementById('badgeUsersContent').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
        </div>
    `;
    
    $('#badgeUsersModal').modal('show');
    
    // Simuler des données (remplacer par une vraie requête AJAX)
    setTimeout(() => {
        document.getElementById('badgeUsersContent').innerHTML = `
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Date d'obtention</th>
                            <th>Événement</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Jean Dupont</td>
                            <td>jean.dupont@email.com</td>
                            <td>15/09/2024</td>
                            <td>Collecte Plastique</td>
                        </tr>
                        <tr>
                            <td>Marie Martin</td>
                            <td>marie.martin@email.com</td>
                            <td>10/09/2024</td>
                            <td>Atelier Recyclage</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        `;
    }, 1000);
}

function editBadge(badgeId) {
    // Implémenter l'édition de badge
    alert('Fonctionnalité d\'édition à implémenter');
}

function toggleBadgeStatus(badgeId) {
    if (confirm('Êtes-vous sûr de vouloir changer le statut de ce badge ?')) {
        // Implémenter le changement de statut
        alert('Fonctionnalité de changement de statut à implémenter');
    }
}
</script>
@endpush
@endsection

