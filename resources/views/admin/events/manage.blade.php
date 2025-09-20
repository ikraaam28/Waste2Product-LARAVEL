@extends('layouts.admin')
@section('title', 'Manage Events')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Manage Events</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Manage</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="card-title">Events List</div>
                        <div class="card-tools">
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                <i class="fa fa-plus"></i> New Event
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped" id="eventsTable">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Category</th>
                                        <th>Date</th>
                                        <th>Location</th>
                                        <th>Participants</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($events as $event)
                                        <tr>
                                            <td>
                                                @if($event->image)
                                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                                @else
                                                    <div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                                        <i class="fa fa-calendar text-muted"></i>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $event->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ Str::limit($event->description, 50) }}</small>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $event->category }}</span>
                                            </td>
                                            <td>
                                                {{ $event->date->format('d/m/Y') }}
                                                <br>
                                                <small class="text-muted">{{ $event->time }}</small>
                                            </td>
                                            <td>{{ $event->location }}</td>
                                            <td>
                                                <span class="badge badge-primary">{{ $event->total_participants_count }}</span>
                                                @if($event->max_participants)
                                                    / {{ $event->max_participants }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                                    {{ $event->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.events.toggle-status', $event) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-{{ $event->status ? 'secondary' : 'success' }}" title="{{ $event->status ? 'Deactivate' : 'Activate' }}">
                                                            <i class="fa fa-{{ $event->status ? 'pause' : 'play' }}"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this event?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                <i class="fa fa-calendar fa-3x mb-3"></i>
                                                <br>
                                                No events at the moment
                                                <br>
                                                <a href="{{ route('admin.events.create') }}" class="btn btn-primary mt-2">
                                                    <i class="fa fa-plus"></i> Create the first event
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#eventsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json"
        },
        "pageLength": 25,
        "order": [[ 3, "desc" ]]
    });
});
</script>
@endpush
@endsection

