@extends('layouts.admin')
@section('title', 'Event Details')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">{{ $event->title }}</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="{{ route('admin.events.index') }}"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.events.index') }}">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Details</li>
            </ul>
        </div>

        <div class="row">
            <!-- Informations principales -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Event Information</div>
                        <div class="card-tools">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i> Modifier
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($event->image)
                            <div class="text-center mb-4">
                                <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" 
                                     class="img-fluid rounded" style="max-height: 300px;">
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Category:</strong>
                                <span class="badge badge-info">{{ $event->category }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Statut:</strong>
                                <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                    {{ $event->status ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Date:</strong> {{ $event->date ? \Carbon\Carbon::parse($event->date)->format('d/m/Y') : 'N/A' }}
                            </div>
                            <div class="col-md-6">
                                <strong>Heure:</strong> {{ $event->time ? \Carbon\Carbon::parse($event->time)->format('H:i') : 'N/A' }}
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <strong>Lieu:</strong> {{ $event->location }}
                            </div>
                        </div>

                        @if($event->description)
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="mt-2">{{ $event->description }}</p>
                            </div>
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Participants:</strong> {{ $event->total_participants_count }}
                                @if($event->max_participants)
                                    / {{ $event->max_participants }}
                                @endif
                            </div>
                            <div class="col-md-6">
                                <strong>QR Code:</strong> 
                                <code>{{ $event->qr_code }}</code>
                            </div>
                        </div>

                        @if($event->products->count() > 0)
                            <div class="mb-3">
                                <strong>Related Products:</strong>
                                <div class="row mt-2">
                                    @foreach($event->products as $product)
                                        <div class="col-md-4 mb-2">
                                            <div class="card">
                                                <div class="card-body p-2">
                                                    <h6 class="card-title mb-1">{{ $product->name }}</h6>
                                                    <small class="text-muted">{{ $product->category->name }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Statistiques</div>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="metric">
                                    <span class="icon"><i class="fa fa-users"></i></span>
                                    <p>
                                        <span class="number">{{ $event->total_participants_count }}</span>
                                        <span class="title">Participants</span>
                                    </p>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="metric">
                                    <span class="icon"><i class="fa fa-qrcode"></i></span>
                                    <p>
                                        <span class="number">{{ $event->scanned_participants_count }}</span>
                                        <span class="title">Scanned</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- <div class="card">
                    <div class="card-header">
                        <div class="card-title">Actions</div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.events.qr-scanner') }}" class="btn btn-success">
                                <i class="fa fa-qrcode"></i> Scanner QR
                            </a>
                            <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-{{ $event->status ? 'warning' : 'success' }} w-100">
                                    <i class="fa fa-{{ $event->status ? 'pause' : 'play' }}"></i>
                                    {{ $event->status ? 'Disable' : 'Enable' }}
                                </button>
                            </form>
                            <a href="{{ route('admin.events.feedback') }}" class="btn btn-info">
                                <i class="fa fa-chart-line"></i> Voir Feedback
                            </a>
                        </div>
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Participants -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Participants</div>
                    </div>
                    <div class="card-body">
                        @if($event->participants->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Ville</th>
                                            <th>Statut Scan</th>
                                            <th>Badge</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($event->participants as $participant)
                                            <tr>
                                                <td>{{ $participant->full_name }}</td>
                                                <td>{{ $participant->email }}</td>
                                                <td>{{ $participant->phone }}</td>
                                                <td>{{ $participant->city }}</td>
                                                <td>
                                                    @if($participant->pivot->scanned_at)
                                                        <span class="badge badge-success">
                                                            <i class="fa fa-check"></i> Scanned
                                                        </span>
                                                        <br>
                                                        <small class="text-muted">{{ $participant->pivot->scanned_at ? \Carbon\Carbon::parse($participant->pivot->scanned_at)->format('d/m/Y H:i') : 'N/A' }}</small>
                                                    @else
                                                        <span class="badge badge-warning">
                                                            <i class="fa fa-clock"></i> En attente
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($participant->pivot->badge_earned)
                                                        <span class="badge badge-primary">
                                                            <i class="fa fa-medal"></i> Badge obtenu
                                                        </span>
                                                    @else
                                                        <span class="badge badge-secondary">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">Aucun participant inscrit</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Feedbacks -->
        @if($event->feedbacks->count() > 0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="card-title">Feedbacks</div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($event->feedbacks as $feedback)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <h6 class="card-title">{{ $feedback->user->full_name }}</h6>
                                                    <div class="rating">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fa fa-star{{ $i <= $feedback->rating ? '' : '-o' }} text-warning"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                @if($feedback->comment)
                                                    <p class="card-text">{{ $feedback->comment }}</p>
                                                @endif
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <small class="text-muted">Recycled</small>
                                                        <br>
                                                        <strong>{{ $feedback->recycled_quantity }}kg</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">CO₂ Saved</small>
                                                        <br>
                                                        <strong>{{ $feedback->co2_saved }}kg</strong>
                                                    </div>
                                                    <div class="col-4">
                                                        <small class="text-muted">Satisfaction</small>
                                                        <br>
                                                        <strong>{{ $feedback->satisfaction_level }}/10</strong>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

