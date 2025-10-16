@extends('layouts.app')

@section('content')
<!-- QR Code Hero Section -->
<div class="container-xxl py-2 mb-2">
    <div class="container py-2 px-lg-5">
        <div class="row g-2 py-2">
            <div class="col-12" style="margin-top: 80px;">
                <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary">
                    <i class="fa fa-arrow-left me-2"></i>Back to Event
                </a>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Section -->
<div class="container-xxl py-3">
    <div class="container">
        <div class="row g-5">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- QR Code Information -->
                <div class="card shadow-lg border-0 mb-5">
                    <div class="card-body p-5">
                        <h3 class="text-primary mb-4">Your Event QR Code</h3>
                        
                        <!-- Participant ID -->
                        <div class="alert alert-info mb-4">
                            <div class="d-flex align-items-center">
                                <i class="fa fa-id-card me-2"></i>
                                <strong>Participant ID:</strong>
                                <span class="ms-2 font-monospace">{{ $participantId }}</span>
                            </div>
                        </div>
                        
                        <!-- QR Code Display -->
                        <div class="text-center mb-4">
                            <div id="qrcode" class="d-inline-block">
                                <div class="text-center text-muted">
                                    <i class="fa fa-spinner fa-spin fa-2x mb-2"></i>
                                    <p>Loading QR Code...</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Event Information -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Date</h6>
                                        <p class="text-muted mb-0">{{ $event->date->format('l, F j, Y') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Time</h6>
                                        <p class="text-muted mb-0">{{ $event->time->format('g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Location</h6>
                                        <p class="text-muted mb-0">{{ $event->location }}, {{ $event->city }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                        <i class="fa fa-user"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Participant</h6>
                                        <p class="text-muted mb-0">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                                    </div>
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
                            <a href="{{ route('my-events') }}" class="btn btn-primary">
                                <i class="fa fa-calendar me-2"></i>My Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Event Statistics -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body p-4">
                        <h5 class="text-primary mb-4">Event Statistics</h5>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 btn-lg-square bg-success text-white rounded-circle me-3">
                                <i class="fa fa-users"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Participants</h6>
                                <p class="text-muted mb-0">{{ $event->participants()->count() }} / {{ $event->max_participants ?? '∞' }}</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0 btn-lg-square bg-info text-white rounded-circle me-3">
                                <i class="fa fa-tag"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Category</h6>
                                <p class="text-muted mb-0">{{ $event->category }}</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0 btn-lg-square bg-warning text-white rounded-circle me-3">
                                <i class="fa fa-calendar-check"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Status</h6>
                                <p class="text-muted mb-0">{{ $event->status ? 'Active' : 'Inactive' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- QR Code Instructions -->
                <div class="card shadow-lg border-0">
                    <div class="card-body p-4">
                        <h5 class="text-primary mb-4">QR Code Instructions</h5>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 btn-sm-square bg-primary text-white rounded-circle me-3 mt-1">
                                    <i class="fa fa-mobile-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Scan with Phone</h6>
                                    <p class="text-muted mb-0 small">Use your phone's camera to scan this QR code</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 btn-sm-square bg-primary text-white rounded-circle me-3 mt-1">
                                    <i class="fa fa-info-circle"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Event Information</h6>
                                    <p class="text-muted mb-0 small">The QR code contains all event details</p>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 btn-sm-square bg-primary text-white rounded-circle me-3 mt-1">
                                    <i class="fa fa-shield-alt"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Secure</h6>
                                    <p class="text-muted mb-0 small">Your participant ID is unique and secure</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Convert image to canvas for better download support
function convertImageToCanvas(img, container) {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = img.naturalWidth || img.width;
    canvas.height = img.naturalHeight || img.height;
    
    // Set crossOrigin to avoid tainted canvas
    img.crossOrigin = 'anonymous';
    
    // Draw the image on canvas
    ctx.drawImage(img, 0, 0);
    
    // Apply the same styling as the image
    canvas.style.cssText = img.style.cssText;
    
    // Replace the image with canvas
    container.innerHTML = '';
    container.appendChild(canvas);
}

// Generate QR code using QRCode.js library (creates canvas directly)
function generateQRCodeWithLibrary() {
    const qrData = {!! json_encode($qrData) !!};
    const qrElement = document.getElementById('qrcode');
    
    console.log('QR Data:', qrData);
    
    if (qrElement && typeof QRCode !== 'undefined') {
        qrElement.innerHTML = '';
        
        // Create QR code with formatted text (simple and readable)
        const qrText = `🎫 EVENT: ${qrData.event_title || 'N/A'}
📅 DATE: ${qrData.event_date || 'N/A'} at ${qrData.event_time || 'N/A'}
📍 LOCATION: ${qrData.event_location || 'N/A'}, ${qrData.event_city || 'N/A'}
🏷️ CATEGORY: ${qrData.event_category || 'N/A'}
👤 PARTICIPANT: ${qrData.participant_name || 'N/A'}
🆔 ID: ${qrData.participant_id || 'N/A'}

Scanned from TeaHouse Event Manager`;
        
        QRCode.toCanvas(qrElement, qrText, {
            width: 300,
            height: 300,
            color: {
                dark: '#000000',
                light: '#ffffff'
            },
            margin: 2,
            errorCorrectionLevel: 'M'
        }, function (error) {
            if (error) {
                console.error('QR Code Error:', error);
                generateFallbackQR();
            } else {
                console.log('QR Code generated successfully!');
                // Add styling to the canvas
                const canvas = qrElement.querySelector('canvas');
                if (canvas) {
                    canvas.style.cssText = `
                        border: 2px solid #000;
                        border-radius: 10px;
                        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                    `;
                }
            }
        });
    } else {
        generateFallbackQR();
    }
}

// Generate real scannable QR code using API (fallback)
function generateScannableQRCode() {
    const qrData = {!! json_encode($qrData) !!};
    const qrElement = document.getElementById('qrcode');
    
    console.log('QR Data:', qrData);
    
    if (qrElement) {
        // Clear loading content
        qrElement.innerHTML = '';
        
        // Create QR code with formatted text (simple and readable)
        const qrText = `🎫 EVENT: ${qrData.event_title || 'N/A'}
📅 DATE: ${qrData.event_date || 'N/A'} at ${qrData.event_time || 'N/A'}
📍 LOCATION: ${qrData.event_location || 'N/A'}, ${qrData.event_city || 'N/A'}
🏷️ CATEGORY: ${qrData.event_category || 'N/A'}
👤 PARTICIPANT: ${qrData.participant_name || 'N/A'}
🆔 ID: ${qrData.participant_id || 'N/A'}

Scanned from TeaHouse Event Manager`;
        
        const qrSize = 300;
        const qrUrl = `https://chart.googleapis.com/chart?chs=${qrSize}x${qrSize}&cht=qr&chl=${encodeURIComponent(qrText)}&choe=UTF-8`;
        
        // Create image element (keep as image, don't convert to canvas)
        const img = document.createElement('img');
        img.src = qrUrl;
        img.alt = 'Event QR Code';
        img.style.cssText = `
            width: ${qrSize}px;
            height: ${qrSize}px;
            border: 2px solid #000;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        `;
        
        // Add loading state
        img.onload = function() {
            console.log('Scannable QR Code loaded successfully');
        };
        
        img.onerror = function() {
            console.error('Failed to load QR code from Google Charts API');
            generateFallbackQR();
        };
        
        qrElement.appendChild(img);
    }
}

// Fallback QR code using QR Server API
function generateFallbackQR() {
    const qrData = {!! json_encode($qrData) !!};
    const qrElement = document.getElementById('qrcode');
    
    if (qrElement) {
        qrElement.innerHTML = '';
        
        // Create QR code with formatted text (simple and readable)
        const qrText = `🎫 EVENT: ${qrData.event_title || 'N/A'}
📅 DATE: ${qrData.event_date || 'N/A'} at ${qrData.event_time || 'N/A'}
📍 LOCATION: ${qrData.event_location || 'N/A'}, ${qrData.event_city || 'N/A'}
🏷️ CATEGORY: ${qrData.event_category || 'N/A'}
👤 PARTICIPANT: ${qrData.participant_name || 'N/A'}
🆔 ID: ${qrData.participant_id || 'N/A'}

Scanned from TeaHouse Event Manager`;
        
        const qrSize = 300;
        const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=${qrSize}x${qrSize}&data=${encodeURIComponent(qrText)}`;
        
        const img = document.createElement('img');
        img.src = qrUrl;
        img.alt = 'Event QR Code';
        img.style.cssText = `
            width: ${qrSize}px;
            height: ${qrSize}px;
            border: 2px solid #000;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        `;
        
        img.onload = function() {
            console.log('Fallback QR Code loaded successfully');
            // Convert to canvas for better download support
            convertImageToCanvas(img, qrElement);
        };
        
        img.onerror = function() {
            console.error('All QR APIs failed, using text representation');
            generateTextRepresentation();
        };
        
        qrElement.appendChild(img);
    }
}

// Text representation as last resort
function generateTextRepresentation() {
    const qrData = {!! json_encode($qrData) !!};
    const qrElement = document.getElementById('qrcode');
    
    if (qrElement) {
        qrElement.innerHTML = '';
        
        const qrText = JSON.stringify(qrData);
        
        const container = document.createElement('div');
        container.style.cssText = `
            width: 300px;
            height: 300px;
            border: 2px solid #000;
            background: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: monospace;
            font-size: 10px;
            padding: 10px;
            box-sizing: border-box;
            word-break: break-all;
            text-align: center;
        `;
        
        container.innerHTML = `
            <div style="margin-bottom: 10px; font-weight: bold;">QR CODE DATA</div>
            <div style="font-size: 8px; color: #666; max-width: 100%; overflow: hidden;">
                ${qrText.substring(0, 200)}...
            </div>
            <div style="margin-top: 10px; font-size: 8px; color: #999;">
                (Not scannable - use download button)
            </div>
        `;
        
        qrElement.appendChild(container);
    }
}

// Try to load QR Code library, fallback to API-based QR
function loadQRCodeLibrary() {
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
    script.onload = function() {
        console.log('QR Code library loaded successfully');
        generateQRCodeWithLibrary();
    };
    script.onerror = function() {
        console.log('QR Code library failed to load, using API-based QR');
        generateScannableQRCode();
    };
    document.head.appendChild(script);
}

document.addEventListener('DOMContentLoaded', function() {
    loadQRCodeLibrary();
});

function downloadQR() {
    const canvas = document.querySelector('#qrcode canvas');
    const img = document.querySelector('#qrcode img');
    const qrContainer = document.querySelector('#qrcode > div');
    
    if (canvas) {
        // Canvas QR code - download directly
        try {
            const link = document.createElement('a');
            link.download = 'event-qr-code-{{ $event->id }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
            console.log('QR Code downloaded successfully');
        } catch (error) {
            console.error('Download error:', error);
            alert('Unable to download QR code. Please try again.');
        }
    } else if (img) {
        // Image QR code - open in new tab for download
        try {
            const link = document.createElement('a');
            link.href = img.src;
            link.download = 'event-qr-code-{{ $event->id }}.png';
            link.target = '_blank';
            link.click();
            console.log('QR Code image opened for download');
        } catch (error) {
            console.error('Download error:', error);
            // Fallback: open image in new tab
            window.open(img.src, '_blank');
        }
    } else if (qrContainer) {
        // Text representation - download as JSON
        const qrData = {!! json_encode($qrData) !!};
        const blob = new Blob([JSON.stringify(qrData, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'event-qr-data-{{ $event->id }}.json';
        a.click();
        URL.revokeObjectURL(url);
        console.log('QR Data downloaded as JSON');
    } else {
        // Fallback: show error message
        alert('QR Code not ready yet. Please wait a moment and try again.');
    }
}

function printQR() {
    const canvas = document.querySelector('#qrcode canvas');
    const img = document.querySelector('#qrcode img');
    const qrContainer = document.querySelector('#qrcode > div');
    
    const printWindow = window.open('', '_blank');
    
    let qrContent = '';
    if (canvas) {
        qrContent = `<img src="${canvas.toDataURL()}" alt="QR Code" style="max-width: 300px;">`;
    } else if (img) {
        qrContent = `<img src="${img.src}" alt="QR Code" style="max-width: 300px;">`;
    } else if (qrContainer) {
        qrContent = qrContainer.outerHTML;
    } else {
        qrContent = '<div style="width: 300px; height: 300px; border: 2px solid #000; display: flex; align-items: center; justify-content: center; font-family: monospace;">QR Code Data</div>';
    }
    
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
                    .participant-id {
                        background: #e3f2fd;
                        padding: 10px;
                        border-radius: 5px;
                        margin: 10px 0;
                        font-family: monospace;
                    }
                </style>
            </head>
            <body>
                <h1>Your Event QR Code</h1>
                <div class="participant-id">
                    <strong>Participant ID:</strong> {{ $participantId }}
                </div>
                <div class="qr-container">
                    ${qrContent}
                </div>
                <div class="event-info">
                    <h3>{{ $event->title }}</h3>
                    <p><strong>Date:</strong> {{ $event->date->format('F j, Y') }}</p>
                    <p><strong>Time:</strong> {{ $event->time->format('g:i A') }}</p>
                    <p><strong>Location:</strong> {{ $event->location }}, {{ $event->city }}</p>
                    <p><strong>Participant:</strong> {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                </div>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>

<style>
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

.font-monospace {
    font-family: 'Courier New', Courier, monospace;
    font-size: 0.9rem;
    letter-spacing: 1px;
}

.alert-info {
    background-color: #e3f2fd;
    border-color: #bbdefb;
    color: #1565c0;
}

.btn-lg-square {
    width: 3rem;
    height: 3rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-sm-square {
    width: 1.5rem;
    height: 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
}
</style>
@endsection