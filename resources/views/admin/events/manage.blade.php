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
                            <table class="table table-hover" id="eventsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="border-0">Image</th>
                                        <th class="border-0">Event Name</th>
                                        <th class="border-0">Category</th>
                                        <th class="border-0">Date & Time</th>
                                        <th class="border-0">Location</th>
                                        <th class="border-0">Participants</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0 text-center">Actions</th>
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
                                                <div class="d-flex flex-column">
                                                    <strong class="text-dark mb-1">{{ $event->title }}</strong>
                                                    <small class="text-muted">{{ Str::limit($event->description, 60) }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-info px-3 py-2">{{ $event->category }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    <span class="font-weight-bold">{{ $event->date ? \Carbon\Carbon::parse($event->date)->format('M d, Y') : 'N/A' }}</span>
                                                    <small class="text-muted">{{ $event->time }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $event->location }}">
                                                    {{ $event->location }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge badge-primary px-2 py-1 mr-1">{{ $event->total_participants_count }}</span>
                                                    @if($event->max_participants)
                                                        <small class="text-muted">/ {{ $event->max_participants }}</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                                    {{ $event->status ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton{{ $event->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $event->id }}">
                                                        <a class="dropdown-item" href="{{ route('admin.events.show', $event) }}">
                                                            <i class="fa fa-eye text-info mr-2"></i>View
                                                        </a>
                                                        <a class="dropdown-item" href="{{ route('admin.events.edit', $event) }}">
                                                            <i class="fa fa-edit text-warning mr-2"></i>Edit
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deleteEvent({{ $event->id }})">
                                                            <i class="fa fa-trash mr-2"></i>Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center text-muted py-5">
                                                <div class="mb-4">
                                                    <i class="fa fa-calendar-o" style="font-size: 4rem; color: #e9ecef;"></i>
                                                </div>
                                                <h5 class="text-muted mb-3">No events at the moment</h5>
                                                <p class="text-muted mb-4">Start by creating your first event to manage your activities.</p>
                                                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-lg">
                                                    <i class="fa fa-plus mr-2"></i>Create First Event
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

@push('styles')
<style>
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    left: auto;
    z-index: 1000;
    min-width: 120px;
    max-width: 150px;
    padding: 5px 0;
    margin: 2px 0 0;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-shadow: 0 6px 12px rgba(0,0,0,.175);
}

.dropdown-menu.show {
    display: block;
}

.dropdown-item {
    display: block;
    width: 100%;
    padding: 6px 12px;
    clear: both;
    font-weight: normal;
    line-height: 1.4;
    color: #333;
    white-space: nowrap;
    text-decoration: none;
    font-size: 13px;
}

.dropdown-item:hover {
    color: #262626;
    text-decoration: none;
    background-color: #f5f5f5;
}

.dropdown-divider {
    height: 1px;
    margin: 6px 0;
    overflow: hidden;
    background-color: #e5e5e5;
}

.dropdown {
    position: relative;
}

.dropdown-item i {
    width: 16px;
    text-align: center;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#eventsTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/English.json"
        },
        "pageLength": 25,
        "order": [[ 3, "desc" ]]
    });
    
    // Manual dropdown toggle
    $('.dropdown-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Close other dropdowns
        $('.dropdown-menu').not($(this).next('.dropdown-menu')).removeClass('show');
        
        // Toggle current dropdown
        $(this).next('.dropdown-menu').toggleClass('show');
    });
    
    // Close dropdown when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.dropdown').length) {
            $('.dropdown-menu').removeClass('show');
        }
    });
});

// Function to handle event deletion
function deleteEvent(eventId) {
    if (confirm('Are you sure you want to delete this event?')) {
        // Create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/events/${eventId}`;
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        // Submit the form
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection

