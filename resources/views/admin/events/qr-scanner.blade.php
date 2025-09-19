@extends('layouts.admin')
@section('title', 'Scanner QR Code')
@section('content')
<div class="container">
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Scanner QR Code</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="#">Events</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Scanner QR</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Scanner QR Code</div>
                    </div>
                    <div class="card-body">
                        <div id="scanner-container" class="text-center">
                            <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                        </div>
                        
                        <div class="mt-4">
                            <h5>Ou saisir manuellement le QR Code :</h5>
                            <div class="input-group">
                                <input type="text" class="form-control" id="manual-qr" placeholder="Entrez le QR Code ici">
                                <div class="input-group-append">
                                    <button class="btn btn-primary" onclick="scanManual()">
                                        <i class="fa fa-search"></i> Scanner
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Résultat du Scan</div>
                    </div>
                    <div class="card-body" id="scan-result">
                        <div class="text-center text-muted">
                            <i class="fa fa-qrcode fa-3x mb-3"></i>
                            <p>Scannez un QR Code pour voir les informations du participant</p>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="card-title">Actions Rapides</div>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-success" onclick="startScanner()">
                                <i class="fa fa-play"></i> Démarrer Scanner
                            </button>
                            <button class="btn btn-warning" onclick="stopScanner()">
                                <i class="fa fa-stop"></i> Arrêter Scanner
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
<script>
let html5QrcodeScanner = null;

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
    
    // Arrêter le scanner
    stopScanner();
    
    // Traiter le QR code
    processQRCode(decodedText);
}

function onScanFailure(error) {
    // console.warn(`Code scan error = ${error}`);
}

function scanManual() {
    const qrCode = document.getElementById('manual-qr').value;
    if (qrCode.trim()) {
        processQRCode(qrCode.trim());
    } else {
        alert('Veuillez entrer un QR Code');
    }
}

function processQRCode(qrCode) {
    // Afficher un loader
    document.getElementById('scan-result').innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Chargement...</span>
            </div>
            <p class="mt-2">Traitement du QR Code...</p>
        </div>
    `;

    // Simuler une requête AJAX (remplacer par une vraie requête)
    fetch('{{ route("admin.events.scan-qr") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            qr_code: qrCode,
            user_id: 1 // À remplacer par l'ID de l'utilisateur connecté
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayScanResult(data);
        } else {
            displayError(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayError('Erreur lors du traitement du QR Code');
    });
}

function displayScanResult(data) {
    const resultDiv = document.getElementById('scan-result');
    
    if (data.scanned_at) {
        // QR Code déjà scanné
        resultDiv.innerHTML = `
            <div class="alert alert-warning">
                <h5><i class="fa fa-exclamation-triangle"></i> QR Code déjà scanné</h5>
                <p>Ce QR Code a déjà été scanné le ${new Date(data.scanned_at).toLocaleString('fr-FR')}</p>
            </div>
        `;
    } else {
        // Scan réussi
        resultDiv.innerHTML = `
            <div class="alert alert-success">
                <h5><i class="fa fa-check-circle"></i> Scan réussi !</h5>
                <div class="mt-3">
                    <h6>Informations du participant :</h6>
                    <p><strong>Nom :</strong> ${data.participant.first_name} ${data.participant.last_name}</p>
                    <p><strong>Email :</strong> ${data.participant.email}</p>
                    <p><strong>Événement :</strong> ${data.event.title}</p>
                    <p><strong>Date :</strong> ${new Date(data.event.date).toLocaleDateString('fr-FR')}</p>
                    <p><strong>Lieu :</strong> ${data.event.location}</p>
                </div>
                <div class="mt-3">
                    <button class="btn btn-success btn-sm" onclick="exportPDF()">
                        <i class="fa fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="btn btn-info btn-sm" onclick="exportCSV()">
                        <i class="fa fa-file-csv"></i> Export CSV
                    </button>
                </div>
            </div>
        `;
    }
}

function displayError(message) {
    document.getElementById('scan-result').innerHTML = `
        <div class="alert alert-danger">
            <h5><i class="fa fa-times-circle"></i> Erreur</h5>
            <p>${message}</p>
        </div>
    `;
}

function exportPDF() {
    // Implémenter l'export PDF
    alert('Fonctionnalité d\'export PDF à implémenter');
}

function exportCSV() {
    // Implémenter l'export CSV
    alert('Fonctionnalité d\'export CSV à implémenter');
}

// Démarrer automatiquement le scanner au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    startScanner();
});
</script>
@endpush
@endsection
