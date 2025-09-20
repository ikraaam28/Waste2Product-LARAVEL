@extends('layouts.admin')
@section('title', 'Tous les Événements')
@section('content')
  <div class="container">
    <div class="page-inner">
      <div class="page-header">
            <h3 class="fw-bold mb-3">Tous les Événements</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Tous les Événements</li>
        </ul>
      </div>

        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Rechercher par titre ou description...">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" onclick="applyFilters()">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <select class="form-control" id="categoryFilter">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->value }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="statusFilter">
                                    <option value="">Tous les statuts</option>
                                    <option value="1">Actifs</option>
                                    <option value="0">Inactifs</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="date" class="form-control" id="dateFilter" placeholder="Filtrer par date">
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary" onclick="applyFilters()">
                                    <i class="fa fa-filter"></i> Filtrer
                                </button>
                                <button class="btn btn-secondary" onclick="clearFilters()">
                                    <i class="fa fa-times"></i> Effacer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue Calendrier -->
        <div class="row mb-4">
        <div class="col-md-12">
          <div class="card">
                    <div class="card-header">
                        <div class="card-title">Calendar View</div>
                        <div class="card-tools">
                            <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#qrScannerModal">
                                <i class="fa fa-qrcode"></i> Scan QR Code
                            </button>
                            <button class="btn btn-sm btn-primary" onclick="toggleView('calendar')">
                                <i class="fa fa-calendar"></i> Calendar
                            </button>
                            <button class="btn btn-sm btn-secondary" onclick="toggleView('grid')">
                                <i class="fa fa-th"></i> Grid
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="calendarView" style="display: block;">
                            <div id="calendar"></div>
                        </div>
                        <div id="gridView" style="display: none;">
                            <div class="row" id="eventsGrid">
                                @foreach($events as $event)
                                    <div class="col-md-4 mb-4 event-card" data-category="{{ $event->category }}" data-status="{{ $event->status ? '1' : '0' }}" data-date="{{ $event->date->format('Y-m-d') }}" data-title="{{ strtolower($event->title) }}" data-description="{{ strtolower($event->description) }}">
                                        <div class="card">
                                            @if($event->image)
                                                <img class="card-img-top" src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                                            @else
                                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                    <i class="fa fa-calendar fa-3x text-muted"></i>
                                                </div>
                                            @endif
            <div class="card-body">
                                                <h5 class="card-title">{{ $event->title }}</h5>
                                                <p class="card-text text-muted">{{ Str::limit($event->description, 100) }}</p>
                                                <div class="mb-2">
                                                    <span class="badge badge-{{ $event->status ? 'success' : 'danger' }}">
                                                        {{ $event->status ? 'Actif' : 'Inactif' }}
                                                    </span>
                                                    <span class="badge badge-info">{{ $event->category }}</span>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fa fa-calendar"></i> {{ $event->date->format('d/m/Y') }}
                                                        <i class="fa fa-clock ml-2"></i> {{ $event->time }}
                                                    </small>
                                                </div>
                                                <div class="mb-2">
                                                    <small class="text-muted">
                                                        <i class="fa fa-map-marker"></i> {{ $event->location }}
                                                    </small>
                                                </div>
                                                <div class="mb-3">
                                                    <small class="text-muted">
                                                        <i class="fa fa-users"></i> {{ $event->total_participants_count }} participants
                                                    </small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <a href="{{ route('admin.events.show', $event) }}" class="btn btn-sm btn-primary">
                                                        <i class="fa fa-eye"></i> Voir
                                                    </a>
                                                    <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-sm btn-warning">
                                                        <i class="fa fa-edit"></i> Modifier
                                                    </a>
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
      </div>
    </div>
  </div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrScannerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code Scanner</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal-qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                
                <div class="mt-4">
                    <h5>Or enter QR Code manually:</h5>
                    <div class="input-group">
                        <input type="text" class="form-control" id="modal-manual-qr" placeholder="Enter QR Code here">
                        <div class="input-group-append">
                            <button class="btn btn-primary" onclick="scanManualModal()">
                                <i class="fa fa-search"></i> Scan
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="modal-scan-result" class="mt-3">
                    <div class="text-center text-muted">
                        <i class="fa fa-qrcode fa-2x mb-2"></i>
                        <p>Scan a QR Code to see participant information</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="startModalScanner()">
                    <i class="fa fa-play"></i> Start Scanner
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
let modalHtml5QrcodeScanner = null;

let calendar = null;

// Helper function to get category name from value
function getCategoryName(value) {
    const categories = {
        'Recycling': 'Recyclage',
        'Education': 'Éducation', 
        'Awareness': 'Sensibilisation',
        'Collection': 'Collecte',
        'Workshop': 'Atelier'
    };
    return categories[value] || value;
}

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridYear,listWeek'
        },
        events: function(info, successCallback, failureCallback) {
            loadFilteredEvents(successCallback, failureCallback);
        },
        eventClick: function(info) {
            window.location.href = info.event.url;
        },
        height: 'auto',
        aspectRatio: 1.8
    });
    calendar.render();
    
    // Add real-time search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            applyFilters();
        });
    }
    
    // Add event listeners for existing filters
    const categoryFilter = document.getElementById('categoryFilter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
    
    const dateFilter = document.getElementById('dateFilter');
    if (dateFilter) {
        dateFilter.addEventListener('change', function() {
            applyFilters();
        });
    }
});

function toggleView(view) {
    if (view === 'calendar') {
        document.getElementById('calendarView').style.display = 'block';
        document.getElementById('gridView').style.display = 'none';
    } else {
        document.getElementById('calendarView').style.display = 'none';
        document.getElementById('gridView').style.display = 'block';
    }
}

function loadFilteredEvents(successCallback, failureCallback) {
    const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';
    const category = document.getElementById('categoryFilter') ? document.getElementById('categoryFilter').value : '';
    const status = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : '';
    const date = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : '';
    
    // Get all events from the server
    fetch('{{ route("admin.events.api") }}')
        .then(response => response.json())
        .then(events => {
            // Filter events based on current filters
            const filteredEvents = events.filter(event => {
                let show = true;
                
                // Search filter
                if (search) {
                    const title = (event.title || '').toLowerCase();
                    const description = (event.description || '').toLowerCase();
                    if (!title.includes(search) && !description.includes(search)) {
                        show = false;
                    }
                }
                
                // Category filter - try multiple matching strategies
                if (category) {
                    const eventCategory = event.category || '';
                    
                    // Try exact match first
                    let categoryMatch = eventCategory === category;
                    
                    // If no exact match, try with the category name mapping
                    if (!categoryMatch) {
                        const categoryName = getCategoryName(category);
                        categoryMatch = eventCategory === categoryName;
                    }
                    
                    // If still no match, try case-insensitive
                    if (!categoryMatch) {
                        categoryMatch = eventCategory.toLowerCase() === category.toLowerCase();
                    }
                    
                    if (!categoryMatch) {
                        show = false;
                    }
                }
                
                // Status filter
                if (status && event.status !== (status === '1')) {
                    show = false;
                }
                
                // Date filter
                if (date) {
                    const eventDate = new Date(event.date).toISOString().split('T')[0];
                    if (eventDate !== date) {
                        show = false;
                    }
                }
                
                return show;
            });
            
            successCallback(filteredEvents);
        })
        .catch(error => {
            console.error('Error loading events:', error);
            failureCallback(error);
        });
}

// Removed adjustCalendarView function to prevent infinite loop

function applyFilters() {
    const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value.toLowerCase() : '';
    const category = document.getElementById('categoryFilter') ? document.getElementById('categoryFilter').value : '';
    const status = document.getElementById('statusFilter') ? document.getElementById('statusFilter').value : '';
    const date = document.getElementById('dateFilter') ? document.getElementById('dateFilter').value : '';
    
    const eventCards = document.querySelectorAll('.event-card');
    
    eventCards.forEach(card => {
        let show = true;
        
        // Search filter
        if (search) {
            const title = card.dataset.title || '';
            const description = card.dataset.description || '';
            if (!title.includes(search) && !description.includes(search)) {
                show = false;
            }
        }
        
        // Category filter - use the same matching logic as calendar
        if (category) {
            const eventCategory = card.dataset.category || '';
            let categoryMatch = eventCategory === category;
            
            if (!categoryMatch) {
                const categoryName = getCategoryName(category);
                categoryMatch = eventCategory === categoryName;
            }
            
            if (!categoryMatch) {
                categoryMatch = eventCategory.toLowerCase() === category.toLowerCase();
            }
            
            if (!categoryMatch) {
                show = false;
            }
        }
        
        // Status filter
        if (status && card.dataset.status !== status) {
            show = false;
        }
        
        // Date filter
        if (date && card.dataset.date !== date) {
            show = false;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
    
    // Update calendar if it exists - but only refetch, don't adjust view
    if (calendar) {
        calendar.refetchEvents();
    }
}

function clearFilters() {
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    
    if (searchInput) searchInput.value = '';
    if (categoryFilter) categoryFilter.value = '';
    if (statusFilter) statusFilter.value = '';
    if (dateFilter) dateFilter.value = '';
    
    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach(card => {
        card.style.display = 'block';
    });
    
    // Update calendar if it exists
    if (calendar) {
        // Reset calendar to current month view
        calendar.changeView('dayGridMonth');
        calendar.gotoDate(new Date());
        calendar.refetchEvents();
    }
}

// Modal QR Scanner functions
function startModalScanner() {
    if (modalHtml5QrcodeScanner) {
        return;
    }

    modalHtml5QrcodeScanner = new Html5QrcodeScanner(
        "modal-qr-reader",
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        },
        false
    );

    modalHtml5QrcodeScanner.render(onModalScanSuccess, onModalScanFailure);
}

function onModalScanSuccess(decodedText, decodedResult) {
    console.log(`Code scanned = ${decodedText}`, decodedResult);
    stopModalScanner();
    processQRCodeModal(decodedText);
}

function onModalScanFailure(error) {
    // console.warn(`Code scan error = ${error}`);
}

function stopModalScanner() {
    if (modalHtml5QrcodeScanner) {
        modalHtml5QrcodeScanner.clear().catch(err => {
            console.error("Error stopping scanner:", err);
        });
        modalHtml5QrcodeScanner = null;
        document.getElementById('modal-qr-reader').innerHTML = '';
    }
}

function scanManualModal() {
    const qrCode = document.getElementById('modal-manual-qr').value;
    if (qrCode.trim()) {
        processQRCodeModal(qrCode.trim());
    } else {
        alert('Please enter a QR Code');
    }
}

function processQRCodeModal(qrCode) {
    document.getElementById('modal-scan-result').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Processing QR Code...</p>
        </div>
    `;

    fetch('{{ route("admin.events.scan-qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            qr_code: qrCode,
            user_id: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayModalScanResult(data);
        } else {
            displayModalError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayModalError('Error processing QR Code');
    });
}

function displayModalScanResult(data) {
    const resultDiv = document.getElementById('modal-scan-result');
    
    if (data.scanned_at) {
        resultDiv.innerHTML = `
            <div class="alert alert-warning">
                <h5><i class="fa fa-exclamation-triangle"></i> QR Code Already Scanned</h5>
                <p>This QR Code was already scanned on ${new Date(data.scanned_at).toLocaleString('en-US')}</p>
            </div>
        `;
    } else {
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <h5><i class="fa fa-check-circle"></i> Scan Successful!</h5>
                <div class="mt-3">
                    <h6>Participant Information:</h6>
                    <p><strong>Name:</strong> ${data.participant.first_name} ${data.participant.last_name}</p>
                    <p><strong>Email:</strong> ${data.participant.email}</p>
                    <p><strong>Event:</strong> ${data.event.title}</p>
                    <p><strong>Date:</strong> ${new Date(data.event.date).toLocaleDateString('en-US')}</p>
                    <p><strong>Location:</strong> ${data.event.location}</p>
                </div>
            </div>
        `;
    }
}

function displayModalError(message) {
    document.getElementById('modal-scan-result').innerHTML = `
        <div class="alert alert-danger">
            <h5><i class="fa fa-times-circle"></i> Error</h5>
            <p>${message}</p>
        </div>
    `;
}

// Clean up scanner when modal is closed
$('#qrScannerModal').on('hidden.bs.modal', function () {
    stopModalScanner();
});
</script>
@endpush
@endsection