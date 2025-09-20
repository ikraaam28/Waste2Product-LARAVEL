@extends('layouts.admin')
@section('title', 'Events Dashboard')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Events Dashboard</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Dashboard</li>
            </ul>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="flaticon-calendar text-primary"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Total Events</p>
                                    <h4 class="card-title">{{ $totalEvents }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="flaticon-users text-success"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Total Participants</p>
                                    <h4 class="card-title">{{ $totalParticipants }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="flaticon-qr-code text-warning"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">QR Scanned</p>
                                    <h4 class="card-title">{{ $totalScanned }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-stats card-round">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-5">
                                <div class="icon-big text-center">
                                    <i class="flaticon-medal text-info"></i>
                                </div>
                            </div>
                            <div class="col-7 col-stats">
                                <div class="numbers">
                                    <p class="card-category">Badges Distributed</p>
                                    <h4 class="card-title">{{ $totalBadges }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Compact Calendar -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Events Calendar</div>
                    </div>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Quick Actions</div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('admin.events.create') }}" class="btn btn-primary btn-block">
                                    <i class="fa fa-plus"></i> New Event
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('admin.events.qr-scanner') }}" class="btn btn-success btn-block">
                                    <i class="fa fa-qrcode"></i> Scan QR
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('admin.events.manage') }}" class="btn btn-info btn-block">
                                    <i class="fa fa-list"></i> Manage Events
                                </a>
                            </div>
                            <div class="col-md-6 mb-3">
                                <a href="{{ route('admin.events.feedback') }}" class="btn btn-warning btn-block">
                                    <i class="fa fa-chart-line"></i> Feedback & Impact
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Upcoming Events -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Upcoming Events</div>
                    </div>
                    <div class="card-body">
                        @if($upcomingEvents->count() > 0)
                            @foreach($upcomingEvents as $event)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-online">
                                        @if($event->image)
                                            <img src="{{ asset('storage/' . $event->image) }}" alt="..." class="avatar-img rounded-circle">
                                        @else
                                            <span class="avatar-title rounded-circle bg-primary text-white">{{ substr($event->title, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 ml-3">
                                        <h6 class="mb-0">{{ $event->title }}</h6>
                                        <small class="text-muted">{{ $event->date->format('d/m/Y') }} at {{ $event->time }}</small>
                                        <br>
                                        <small class="text-muted">{{ $event->location }}</small>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                            {{ $event->status ? 'Active' : 'Inactive' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $event->total_participants_count }} participants</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No upcoming events</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Events -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Recent Events</div>
                    </div>
                    <div class="card-body">
                        @if($recentEvents->count() > 0)
                            @foreach($recentEvents as $event)
                                <div class="d-flex align-items-center mb-3">
                                    <div class="avatar avatar-online">
                                        @if($event->image)
                                            <img src="{{ asset('storage/' . $event->image) }}" alt="..." class="avatar-img rounded-circle">
                                        @else
                                            <span class="avatar-title rounded-circle bg-secondary text-white">{{ substr($event->title, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 ml-3">
                                        <h6 class="mb-0">{{ $event->title }}</h6>
                                        <small class="text-muted">{{ $event->created_at->format('d/m/Y H:i') }}</small>
                                        <br>
                                        <small class="text-muted">{{ $event->category }}</small>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                            {{ $event->status ? 'Active' : 'Inactive' }}
                                        </span>
                                        <br>
                                        <small class="text-muted">{{ $event->feedbacks->count() }} feedbacks</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No recent events</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'en',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listWeek'
        },
        events: '{{ route("admin.events.api") }}',
        eventClick: function(info) {
            window.location.href = info.event.url;
        }
    });
    calendar.render();
});
</script>
@endpush
@endsection
