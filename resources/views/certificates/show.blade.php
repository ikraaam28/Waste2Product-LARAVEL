@extends('layouts.app')

@section('content')
<style>
    .certificate-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 40px 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .certificate {
        background: white;
        border: 20px solid #f1c40f;
        border-radius: 15px;
        padding: 50px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
        position: relative;
        max-width: 900px;
        width: 100%;
    }
    
    .certificate::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        bottom: 20px;
        border: 2px solid #3498db;
        pointer-events: none;
    }
    
    .certificate-badge {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: #e74c3c;
        color: white;
        padding: 10px 30px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 18px;
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
    }
    
    .certificate-header {
        margin-bottom: 40px;
    }
    
    .certificate-title {
        font-size: 48px;
        color: #2c3e50;
        margin-bottom: 10px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 3px;
    }
    
    .certificate-subtitle {
        font-size: 24px;
        color: #7f8c8d;
        margin-bottom: 30px;
    }
    
    .certificate-body {
        margin: 40px 0;
    }
    
    .certificate-text {
        font-size: 20px;
        color: #34495e;
        margin: 15px 0;
        line-height: 1.6;
    }
    
    .user-name {
        font-size: 42px;
        color: #e74c3c;
        font-weight: bold;
        margin: 25px 0;
        text-transform: uppercase;
        letter-spacing: 2px;
    }
    
    .tutorial-title {
        font-size: 28px;
        color: #2980b9;
        font-weight: bold;
        margin: 20px 0;
        font-style: italic;
    }
    
    .score-badge {
        display: inline-block;
        background: linear-gradient(135deg, #27ae60, #2ecc71);
        color: white;
        padding: 15px 30px;
        border-radius: 50px;
        font-size: 24px;
        font-weight: bold;
        margin: 20px 0;
        box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
    }
    
    .certificate-footer {
        margin-top: 50px;
        border-top: 2px solid #bdc3c7;
        padding-top: 30px;
    }
    
    .signature-section {
        display: flex;
        justify-content: space-around;
        margin-top: 30px;
    }
    
    .signature {
        text-align: center;
    }
    
    .signature-line {
        width: 200px;
        height: 1px;
        background: #34495e;
        margin: 10px auto;
    }
    
    .download-btn {
        background: linear-gradient(135deg, #3498db, #2980b9);
        color: white;
        border: none;
        padding: 15px 40px;
        font-size: 18px;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 30px;
        box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
    }
    
    .download-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.6);
    }
    
    .decoration {
        position: absolute;
        font-size: 100px;
        opacity: 0.1;
        color: #3498db;
    }
    
    .decoration-1 { top: 50px; left: 50px; }
    .decoration-2 { top: 50px; right: 50px; }
    .decoration-3 { bottom: 50px; left: 50px; }
    .decoration-4 { bottom: 50px; right: 50px; }
    
    .certificate-date {
        font-size: 16px;
        color: #7f8c8d;
        margin-top: 20px;
    }
</style>

<div class="certificate-container">
    <div class="certificate" id="certificate">
        <!-- Badge -->
        <div class="certificate-badge">
            <i class="fas fa-award"></i> CERTIFICATE OF ACHIEVEMENT
        </div>
        
        <!-- Decorations -->
        <div class="decoration decoration-1">★</div>
        <div class="decoration decoration-2">★</div>
        <div class="decoration decoration-3">★</div>
        <div class="decoration decoration-4">★</div>
        
        <!-- Header -->
        <div class="certificate-header">
            <h1 class="certificate-title">Certificate of Excellence</h1>
            <p class="certificate-subtitle">Awarded with Honors</p>
        </div>
        
        <!-- Body -->
        <div class="certificate-body">
            <p class="certificate-text">This certificate is proudly awarded to</p>
            
            <div class="user-name">
                {{ $userName ?? Auth::user()->full_name }}
            </div>
            
            <p class="certificate-text">for successfully completing the tutorial</p>
            
            <div class="tutorial-title">
                "{{ $tuto->title }}"
            </div>
            
            <p class="certificate-text">with an outstanding average score of</p>
            
            <div class="score-badge">
                {{ number_format($averagePercentage, 2) }}%
            </div>
            
            <p class="certificate-text">
                Quizzes completed: <strong>{{ $completedQuizzes }}/{{ $totalQuizzes }}</strong>
            </p>
        </div>
        
        <!-- Footer -->
        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>Educational Director</p>
                    <p><strong>RecycleVerse Academy</strong></p>
                </div>
                
                <div class="signature">
                    <div class="signature-line"></div>
                    <p>Date Issued</p>
                    <p><strong>{{ now()->format('F d, Y') }}</strong></p>
                </div>
            </div>
            
            <p class="certificate-date">
                Certificate ID: RV{{ Auth::id() }}-{{ $tuto->id }}-{{ time() }}
            </p>
        </div>
    </div>
</div>

<!-- Download Button -->
<div class="text-center mt-4 mb-5">
    <!-- Client-side download: generate PDF from #certificate -->
    <button type="button" id="downloadCertificateBtn" class="download-btn">
        <i class="fas fa-download me-2"></i>Download PDF
    </button>
    
    <a href="{{ route('tutos.show', $tuto) }}" class="btn btn-outline-primary mt-3">
        <i class="fas fa-arrow-left me-2"></i>Back to Tutorial
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('downloadCertificateBtn');
    if (!btn) return;

    btn.addEventListener('click', function() {
        btn.classList.add('disabled');
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';

        // target the certificate element already on this page
        const cert = document.getElementById('certificate');
        if (!cert) {
            alert('Certificat introuvable.');
            btn.classList.remove('disabled');
            btn.innerHTML = '<i class="fas fa-download me-2"></i>Download PDF';
            return;
        }

        // prepare wrapper with inline styles to ensure correct rendering
        const wrapper = document.createElement('div');
        wrapper.style.padding = '20px';
        wrapper.style.background = '#ffffff';
        wrapper.style.color = '#000';
        wrapper.style.width = '800px';
        wrapper.style.boxSizing = 'border-box';

        // clone certificate and remove interactive elements
        wrapper.appendChild(cert.cloneNode(true));
        wrapper.querySelectorAll('button, a.btn, form').forEach(el => el.remove());

        const title = ("{{ $tuto->title ?? 'certificate' }}").replace(/\s+/g, '_').replace(/[^\w\-\.]/g, '');
        const filename = 'certificate_' + title + '.pdf';

        const opt = {
            margin:       10,
            filename:     filename,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, logging: false },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        setTimeout(function() {
            html2pdf().set(opt).from(wrapper).save().then(function() {
                btn.classList.remove('disabled');
                btn.innerHTML = '<i class="fas fa-download me-2"></i>Download PDF';
            }).catch(function(err) {
                console.error('PDF generation error', err);
                btn.classList.remove('disabled');
                btn.innerHTML = '<i class="fas fa-download me-2"></i>Download PDF';
                alert('Erreur lors de la génération du PDF.');
            });
        }, 200);
    });
});
</script>

<!-- html2pdf client-side library (no composer) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
@endsection