@extends('layouts.admin')
@section('title', 'QR Code Scanner')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">QR Code Scanner</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">QR Scanner</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">QR Code Scanner</div>
                    </div>
                    <div class="card-body">
                        <!-- Camera Scanner -->
                        <div id="scanner-container" class="text-center mb-4">
                            <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                        </div>
                        
                        <!-- Manual Input -->
                        <div class="mt-4">
                            <h5>Or enter participant ID manually:</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" id="manual-qr" placeholder="Enter participant ID here">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" onclick="scanManual()">
                                        <i class="fa fa-search"></i> Validate
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Image Upload -->
                        <div class="mt-4">
                            <h5>Or upload QR code image:</h5>
                            <div class="input-group">
                                <input type="file" class="form-control" id="qr-image" accept="image/*" onchange="handleImageUpload(event)">
                                <div class="input-group-append">
                                    <button class="btn btn-info" onclick="processImage()" id="process-btn" disabled>
                                        <i class="fa fa-upload"></i> Process Image
                                    </button>
                                </div>
                            </div>
                            <small class="text-muted">Supported formats: JPG, PNG, GIF</small>
                            
                            <!-- Image Preview -->
                            <div id="image-preview" class="mt-3" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0">Image Preview:</h6>
                                    <button class="btn btn-sm btn-outline-danger" onclick="clearImage()">
                                        <i class="fa fa-times"></i> Clear
                                    </button>
                                </div>
                                <img id="preview-img" src="" alt="QR Code Preview" class="img-fluid" style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Scan Result</div>
                    </div>
                    <div class="card-body" id="scan-result">
                        <div class="text-center text-muted">
                            <i class="fa fa-qrcode fa-3x mb-3"></i>
                            <p>Scan a QR Code to see participant information</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Quick Actions</div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="startScanner()">
                                <i class="fa fa-play"></i> Start Scanner
                            </button>
                            <button class="btn btn-warning" onclick="stopScanner()">
                                <i class="fa fa-stop"></i> Stop Scanner
                            </button>
                            <a href="{{ route('admin.events.dashboard') }}" class="btn btn-info">
                                <i class="fa fa-arrow-left"></i> Back to Dashboard
                            </a>
                            <a href="{{ route('admin.events.feedback') }}" class="btn btn-warning">
                                <i class="fa fa-chart-line"></i> Feedback & Impact
                            </a>
                            <a href="{{ route('admin.events.badges') }}" class="btn btn-primary">
                                <i class="fa fa-medal"></i> Badges
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
let html5QrcodeScanner = null;
let uploadedImage = null;

function startScanner() {
    if (html5QrcodeScanner) {
        return;
    }

    html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader",
        { 
            fps: 10, 
            qrbox: { width: 250, height: 250 } 
        },
        false
    );

    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function stopScanner() {
    if (html5QrcodeScanner) {
        html5QrcodeScanner.clear().catch(err => {
            console.error("Error stopping scanner:", err);
        });
        html5QrcodeScanner = null;
        document.getElementById('qr-reader').innerHTML = '';
    }
}

function onScanSuccess(decodedText, decodedResult) {
    console.log(`Code scanned = ${decodedText}`, decodedResult);
    
    // Stop the scanner
    stopScanner();
    
    // Process the QR code
    processQRCode(decodedText);
}

function onScanFailure(error) {
    // console.warn(`Code scan error = ${error}`);
}

function scanManual() {
    const participantId = document.getElementById('manual-qr').value;
    if (participantId.trim()) {
        processQRCode(participantId.trim());
    } else {
        showPopup('Please enter a participant ID', 'warning');
    }
}

function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        uploadedImage = file;
        document.getElementById('process-btn').disabled = false;
        
        // Show image preview
        const reader = new FileReader();
        reader.onload = function(e) {
            const previewImg = document.getElementById('preview-img');
            const previewDiv = document.getElementById('image-preview');
            
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        // Hide preview if no file selected
        document.getElementById('image-preview').style.display = 'none';
        document.getElementById('process-btn').disabled = true;
        uploadedImage = null;
    }
}

function processImage() {
    if (!uploadedImage) {
        showPopup('Please select an image first', 'warning');
        return;
    }
    
    // Show loading
    showPopup('Processing QR code image...', 'info');
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const img = new Image();
        img.onload = function() {
            try {
                // Create canvas to process the image
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                
                // Set canvas size to image size
                canvas.width = img.width;
                canvas.height = img.height;
                
                // Draw image on canvas
                ctx.drawImage(img, 0, 0);
                
                // Get image data
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                
                // Use jsQR to decode QR code
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                
                if (code) {
                    console.log('QR Code detected:', code.data);
                    // Process the decoded QR code
                    processQRCode(code.data);
                } else {
                    showPopup('No QR code found in the image. Please try a clearer image.', 'error');
                }
            } catch (error) {
                console.error('Error processing image:', error);
                showPopup('Error processing image. Please try again.', 'error');
            }
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(uploadedImage);
}

function clearImage() {
    // Clear file input
    document.getElementById('qr-image').value = '';
    
    // Hide preview
    document.getElementById('image-preview').style.display = 'none';
    
    // Disable process button
    document.getElementById('process-btn').disabled = true;
    
    // Clear uploaded image
    uploadedImage = null;
}

function processQRCode(participantId) {
    // Extract participant ID from QR code text if it contains formatted text
    let extractedId = participantId;
    
    // Check if it's a formatted QR code text and extract the ID
    if (participantId.includes('🆔 ID:')) {
        const idMatch = participantId.match(/🆔 ID:\s*([^\s\n]+)/);
        if (idMatch) {
            extractedId = idMatch[1];
            console.log('Extracted participant ID:', extractedId);
        }
    }
    
    // Show loading
    document.getElementById('scan-result').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
            <p class="mt-2">Processing participant ID...</p>
        </div>
    `;

    // Get CSRF token safely
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                     document.querySelector('input[name="_token"]')?.value ||
                     '{{ csrf_token() }}';

    // Make AJAX request
    fetch('{{ route("admin.events.scan-qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            participant_id: extractedId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayScanResult(data);
            // Always show success popup regardless of scan status
            showPopup('QR Code validated successfully - Participant information displayed', 'success');
        } else {
            displayError(data.message, data.type || 'error');
            showPopup(data.message, data.type || 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const errorMsg = 'Error processing participant ID';
        displayError(errorMsg, 'error');
        showPopup(errorMsg, 'error');
    });
}

function displayScanResult(data) {
    const resultDiv = document.getElementById('scan-result');
    
    // Always show participant information as if it's the first time
    resultDiv.innerHTML = `
        <div class="alert alert-success">
            <h5><i class="fa fa-check-circle"></i> Validation Successful!</h5>
            <div class="mt-3">
                <h6>Participant Information:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Name:</strong> ${data.participant.first_name} ${data.participant.last_name}</p>
                        <p><strong>Email:</strong> ${data.participant.email}</p>
                        <p><strong>Participant ID:</strong> ${data.participant_id || 'N/A'}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Event:</strong> ${data.event.title}</p>
                        <p><strong>Date:</strong> ${new Date(data.event.date).toLocaleDateString()}</p>
                        <p><strong>Location:</strong> ${data.event.location}</p>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-success btn-sm" onclick="exportPDF()">
                    <i class="fa fa-file-pdf"></i> Export PDF
                </button>
                <button class="btn btn-info btn-sm" onclick="exportCSV()">
                    <i class="fa fa-file-csv"></i> Export CSV
                </button>
                <button class="btn btn-primary btn-sm" onclick="markAsScanned()">
                    <i class="fa fa-check"></i> Mark as Scanned
                </button>
            </div>
        </div>
    `;
}

function displayError(message, type = 'error') {
    const alertClass = type === 'warning' ? 'alert-warning' : 'alert-danger';
    const icon = type === 'warning' ? 'fa-exclamation-triangle' : 'fa-times-circle';
    
    document.getElementById('scan-result').innerHTML = `
        <div class="alert ${alertClass}">
            <h5><i class="fa ${icon}"></i> ${type === 'warning' ? 'Warning' : 'Error'}</h5>
            <p>${message}</p>
        </div>
    `;
}

// Popup system for validation results
function showPopup(message, type = 'info') {
    // Remove existing popups
    const existingPopups = document.querySelectorAll('.validation-popup');
    existingPopups.forEach(popup => popup.remove());
    
    // Create popup
    const popup = document.createElement('div');
    popup.className = `validation-popup alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} alert-dismissible fade show`;
    popup.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        max-width: 400px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        border: none;
        border-radius: 8px;
    `;
    
    const icon = type === 'success' ? 'fa-check-circle' : 
                 type === 'error' ? 'fa-times-circle' : 
                 type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
    
    popup.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fa ${icon} fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1">${type === 'success' ? 'Valid' : type === 'error' ? 'Invalid' : type === 'warning' ? 'Warning' : 'Info'}</h6>
                <p class="mb-0">${message}</p>
            </div>
            <button type="button" class="btn-close" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (popup.parentNode) {
            popup.remove();
        }
    }, 5000);
}

function exportPDF() {
    // Implement PDF export
    alert('PDF export functionality to be implemented');
}

function exportCSV() {
    // Implement CSV export
    alert('CSV export functionality to be implemented');
}

// Start scanner automatically when page loads
document.addEventListener('DOMContentLoaded', function() {
    startScanner();
});
</script>
@endpush
@endsection
