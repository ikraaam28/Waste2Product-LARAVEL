@extends('layouts.app')

@section('content')
<!-- QR Code Hero Section -->
<div class="container-xxl py-5 bg-primary hero-header mb-5">
    <div class="container my-5 py-5 px-lg-5">
        <div class="row g-5 py-5">
            <div class="col-lg-6 text-center text-lg-start">
                <h1 class="text-white mb-4 animated slideInDown">Your Event QR Code</h1>
                <p class="text-white pb-3 animated slideInDown">
                    Present this QR code at the event for check-in. Keep it safe!
                </p>
                <div class="d-flex flex-wrap gap-3 animated slideInDown">
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-calendar-alt me-2"></i>
                        <span>{{ $event->title }}</span>
                    </div>
                    <div class="d-flex align-items-center text-white">
                        <i class="fa fa-map-marker-alt me-2"></i>
                        <span>{{ $event->location }}</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center text-lg-end">
                <div class="bg-white rounded-4 shadow-lg p-4 animated zoomIn">
                    <i class="fa fa-qrcode text-primary" style="font-size: 6rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Section -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5 text-center">
                        <h3 class="text-primary mb-4">Event Check-in QR Code</h3>
                        
                        <!-- QR Code Display -->
                        <div class="mb-4">
                            <div class="bg-white p-4 rounded-4 shadow-sm d-inline-block">
                                <div id="qrcode" class="mb-3"></div>
                                <p class="text-muted small mb-0">Scan this code at the event</p>
                            </div>
                        </div>
                        
                        <!-- Event Information -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-calendar-alt"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-1">Event</h6>
                                        <p class="text-muted mb-0">{{ $event->title }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-1">Date & Time</h6>
                                        <p class="text-muted mb-0">{{ $event->date->format('M j, Y') }} at {{ $event->time->format('g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-1">Location</h6>
                                        <p class="text-muted mb-0">{{ $event->location }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div class="text-start">
                                        <h6 class="mb-1">Participant</h6>
                                        <p class="text-muted mb-0">{{ auth()->user()->full_name }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Instructions -->
                        <div class="alert alert-info border-0 mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-info-circle me-3" style="font-size: 1.5rem;"></i>
                                <div class="text-start">
                                    <h6 class="mb-1">How to use your QR code:</h6>
                                    <ul class="mb-0 small">
                                        <li>Save this page or take a screenshot</li>
                                        <li>Present the QR code at the event check-in</li>
                                        <li>Keep your phone charged for the event</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <button onclick="downloadQR()" class="btn btn-outline-primary">
                                <i class="fa fa-download me-2"></i>Download QR Code
                            </button>
                            <button onclick="printQR()" class="btn btn-outline-secondary">
                                <i class="fa fa-print me-2"></i>Print QR Code
                            </button>
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-info">
                                <i class="fa fa-arrow-left me-2"></i>Back to Event
                            </a>
                            <a href="{{ route('my-events') }}" class="btn btn-primary">
                                <i class="fa fa-calendar me-2"></i>My Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Library -->
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR Code
    const qrData = JSON.stringify({
        event_id: {{ $event->id }},
        participant_id: '{{ $participantId }}',
        user_id: {{ auth()->user()->id }},
        event_title: '{{ $event->title }}',
        event_date: '{{ $event->date->format('Y-m-d') }}',
        event_time: '{{ $event->time->format('H:i') }}'
    });
    
    QRCode.toCanvas(document.getElementById('qrcode'), qrData, {
        width: 200,
        height: 200,
        color: {
            dark: '#28a745',
            light: '#ffffff'
        },
        margin: 2,
        errorCorrectionLevel: 'M'
    }, function (error) {
        if (error) console.error(error);
        console.log('QR Code generated successfully!');
    });
});

function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const link = document.createElement('a');
        link.download = 'event-qr-code-{{ $event->id }}.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}

function printQR() {
    const canvas = document.querySelector('#qrcode canvas');
    if (canvas) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
                <head>
                    <title>Event QR Code - {{ $event->title }}</title>
                    <style>
                        body { 
                            font-family: Arial, sans-serif; 
                            text-align: center; 
                            padding: 20px;
                        }
                        .qr-container { 
                            margin: 20px 0; 
                        }
                        .event-info { 
                            margin: 20px 0; 
                            text-align: left;
                            max-width: 400px;
                            margin-left: auto;
                            margin-right: auto;
                        }
                        .event-info h3 { 
                            color: #28a745; 
                            margin-bottom: 15px;
                        }
                        .event-info p { 
                            margin: 5px 0; 
                        }
                    </style>
                </head>
                <body>
                    <h1>Event QR Code</h1>
                    <div class="qr-container">
                        <img src="${canvas.toDataURL()}" alt="QR Code" style="max-width: 300px;">
                    </div>
                    <div class="event-info">
                        <h3>{{ $event->title }}</h3>
                        <p><strong>Date:</strong> {{ $event->date->format('F j, Y') }}</p>
                        <p><strong>Time:</strong> {{ $event->time->format('g:i A') }}</p>
                        <p><strong>Location:</strong> {{ $event->location }}</p>
                        <p><strong>Participant:</strong> {{ auth()->user()->full_name }}</p>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
}
</script>

<style>
.hero-header {
    background: linear-gradient(135deg, #28a745, #20c997);
}

.card {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.btn {
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
}

.btn-primary {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #218838, #1ea085);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.alert {
    border-radius: 15px;
}

#qrcode {
    display: flex;
    justify-content: center;
    align-items: center;
}

#qrcode canvas {
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
</style>
@endsection
