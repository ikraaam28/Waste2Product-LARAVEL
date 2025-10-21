@extends('layouts.app')
@section('content')
<div class="container-fluid publication py-3 my-3">
    <div class="container py-3">
        {{-- ✅ Titre avec ID pour traduction --}}
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-6" id="pub-title" data-original="{{ $publication->titre }}">{{ $publication->titre }}</h1>
        </div>

        {{-- ✅ Publication Card (cadre réduit) --}}
        <div class="card mb-3 shadow-sm border-0 compact-card">
            <div class="card-header bg-light d-flex justify-content-between align-items-start p-2">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-user me-1"></i>By {{ $publication->user->full_name }}
                        <i class="fas fa-clock ms-2 me-1"></i>{{ $publication->created_at->diffForHumans() }}
                    </p>
                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-2 py-1">
                        {{ ucfirst($publication->categorie) }}
                    </span>
                    @if($publication->user->isBanned())
                        <span class="badge bg-danger ms-1">🚫 Banned</span>
                    @endif
                </div>

                <div class="d-flex align-items-center gap-2">
                    <div class="translation-controls">
                        <select id="lang-select" class="form-select form-select-sm modern-lang-select">
                            <option value="fr">🇫🇷 Français</option>
                            <option value="en" selected>🇬🇧 English</option>
                            <option value="es">🇪🇸 Español</option>
                            <option value="de">🇩🇪 Deutsch</option>
                            <option value="it">🇮🇹 Italiano</option>
                            <option value="pt">🇵🇹 Português</option>
                            <option value="ar">🇸🇦 العربية</option>
                        </select>
                    </div>
                    
                    {{-- Edit and PDF buttons container --}}
                    <div class="d-flex gap-1">
                        @if(auth()->id() === $publication->user_id)
                            <a href="{{ route('publications.edit', $publication->id) }}" class="btn btn-sm btn-outline-success" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                        @endif
                        {{-- PDF Export Button - visible to authenticated users --}}
                        @if(auth()->check())
                            <button type="button" id="export-pdf" class="btn btn-sm btn-outline-primary" title="Export PDF">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body p-2">
                <div class="publication-content" id="publication-content">
                    <p id="pub-content" class="lead" data-original="{{ $publication->contenu }}">{{ $publication->contenu }}</p>
                    @if($publication->image)
                        <img src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->titre }}"
                             class="img-fluid rounded shadow-sm mb-2 publication-image" style="max-height: 300px; object-fit: cover;">
                    @endif

                    <div id="translation-info" class="alert alert-info alert-dismissible fade show mt-2 p-2 d-none small" role="alert">
                        <i class="fas fa-language me-1"></i>
                        <strong>Translated automatically</strong>
                        <br><small>in <span id="lang-name"></span></small>
                        <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
                    </div>
                </div>

                <div id="translation-loading" class="text-center py-2 d-none">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">Translating...</span>
                    </div>
                    <p class="mt-1 small text-muted mb-0">Translating... ⏳</p>
                </div>
            </div>
        </div>

        {{-- ✅ Reactions Section --}}
        <div class="card mb-3 shadow-sm compact-reactions">
            <div class="card-body p-2">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="reaction-stats d-flex align-items-center gap-3 small">
                        <span class="d-flex align-items-center gap-1">
                            <i class="fas fa-thumbs-up text-success"></i>
                            <strong id="likes-count" class="text-success">{{ $publication->likes_count }}</strong>
                            <span class="text-muted">likes</span>
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="fas fa-thumbs-down text-danger"></i>
                            <strong id="dislikes-count" class="text-danger">{{ $publication->dislikes_count }}</strong>
                            <span class="text-muted">dislikes</span>
                        </span>
                    </div>
                    @if(auth()->check() && auth()->id() !== $publication->user_id && !auth()->user()->isBanned())
                        <div class="reaction-buttons d-flex gap-1">
                            <form method="POST" action="{{ route('publications.like', $publication) }}" class="like-form d-inline reaction-form">
                                @csrf
                                @if($publication->isLikedByAuthUser())
                                    <button type="submit" class="btn btn-success btn-sm reaction-btn px-2 py-1" title="Remove Like">
                                        <i class="fas fa-thumbs-up"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-outline-success btn-sm reaction-btn px-2 py-1" title="Like">
                                        <i class="fas fa-thumbs-up"></i>
                                    </button>
                                @endif
                            </form>
                            <form method="POST" action="{{ route('publications.dislike', $publication) }}" class="dislike-form d-inline reaction-form">
                                @csrf
                                @if($publication->isDislikedByAuthUser())
                                    <button type="submit" class="btn btn-danger btn-sm reaction-btn px-2 py-1" title="Remove Dislike">
                                        <i class="fas fa-thumbs-down"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-outline-danger btn-sm reaction-btn px-2 py-1" title="Dislike">
                                        <i class="fas fa-thumbs-down"></i>
                                    </button>
                                @endif
                            </form>
                        </div>
                    @elseif(auth()->check() && auth()->user()->isBanned())
                        <div class="alert alert-danger d-inline-block p-1 small mb-0">
                            <i class="fas fa-ban me-1"></i>Banned
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ✅ Comments Section (cadre réduit) --}}
        <div class="card shadow-sm border-0 compact-comments">
            <div class="card-header bg-light border-0 py-1 px-2">
                <h5 class="mb-0 d-flex align-items-center gap-2 small">
                    <i class="fas fa-comments text-success"></i>
                    Comments ({{ $publication->commentaires->count() }})
                </h5>
            </div>
            <div class="card-body p-0">
                @if($publication->commentaires->isNotEmpty())
                    <div class="comments-list" style="max-height: 300px; overflow-y: auto;">
                        @foreach($publication->commentaires->sortByDesc('created_at') as $commentaire)
                            <div class="comment-item border-bottom py-1 px-2" data-comment-id="{{ $commentaire->id }}">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="flex-shrink-0">
                                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 11px; font-weight: bold;">
                                            {{ substr($commentaire->user->full_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-0 fw-semibold small">{{ $commentaire->user->full_name }}</h6>
                                            <small class="text-muted">{{ $commentaire->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($commentaire->user->isBanned())
                                            <span class="badge bg-danger mb-1 small">🚫 Banned</span>
                                        @endif
                                        <p class="mb-0 comment-text small">{!! nl2br(e($commentaire->contenu)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-2">
                        <i class="fas fa-comment-slash fa-lg text-muted mb-2"></i>
                        <p class="text-muted small mb-0">No comments yet. Be the first!</p>
                    </div>
                @endif

                @if(auth()->check() && !auth()->user()->isBanned())
                    <div class="card-footer border-0 bg-white p-2">
                        <form method="POST" action="{{ route('commentaires.store', $publication->id) }}" class="comment-form" id="commentForm">
                            @csrf
                            <div class="input-group">
                                <div class="flex-grow-1 position-relative">
                                    <textarea name="contenu" class="form-control comment-textarea @error('contenu') is-invalid @enderror" rows="1" placeholder="Share your thoughts..." required style="resize: none; min-height: 40px;">{{ old('contenu') }}</textarea>
                                    @error('contenu')
                                        <div class="invalid-feedback d-block small">{{ $message }}</div>
                                    @enderror
                                    <button type="button" class="btn btn-outline-secondary emoji-btn position-absolute" style="right: 5px; top: 50%; transform: translateY(-50%); z-index: 10; width: 25px; height: 25px; padding: 0;" title="Add emojis">
                                        <i class="fas fa-smile fs-6"></i>
                                    </button>
                                    <div class="emoji-picker position-absolute d-none" id="emojiPicker" style="right: 0; top: 100%; margin-top: 5px; background: white; border: 1px solid #dee2e6; border-radius: 6px; padding: 6px; max-width: 200px; max-height: 120px; overflow-y: auto; box-shadow: 0 3px 10px rgba(0,0,0,0.1); z-index: 1000;">
                                        <div class="emoji-grid d-flex flex-wrap gap-1">
                                            @php $emojis = ['😀', '😂', '👍', '❤️', '🔥', '✨', '🎉']; @endphp
                                            @foreach($emojis as $emoji)
                                                <span class="emoji-item cursor-pointer p-1 rounded" data-emoji="{{ $emoji }}" title="{{ $emoji }}" style="font-size: 1.1em;">
                                                    {{ $emoji }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success" id="submitComment" style="border-radius: 0 6px 6px 0;">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="form-text mt-1 small">
                                <i class="fas fa-info-circle me-1"></i>Comments moderated
                            </div>
                        </form>
                    </div>
                @elseif(auth()->check() && auth()->user()->isBanned())
                    <div class="card-footer bg-light border-0 p-1 text-center">
                        <div class="alert alert-danger d-inline-block small p-1 mb-0">
                            <i class="fas fa-ban me-1"></i>Banned
                        </div>
                    </div>
                @else
                    <div class="card-footer bg-light border-0 p-1 text-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-sign-in-alt me-1"></i>Login
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- External script libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>
/* Your existing styles remain the same */
.compact-card, .compact-reactions, .compact-comments {
    border-radius: 10px !important;
    max-width: 700px !important;
    margin: 0 auto !important;
}
.compact-card .card-body { padding: 12px !important; }
.compact-reactions .card-body { padding: 8px !important; }
.compact-comments .card-body { padding: 0 !important; }
.compact-comments .card-header { padding: 6px 8px !important; }
.compact-comments .comment-item { padding: 6px 8px !important; }
.compact-comments .card-footer { padding: 8px !important; }

.reaction-btn {
    border-radius: 50% !important;
    width: 32px !important;
    height: 32px !important;
    padding: 0 !important;
    transition: all 0.2s ease;
}
.reaction-btn:hover { transform: scale(1.1); }
.comment-textarea {
    border-radius: 6px 0 0 6px !important;
    min-height: 40px !important;
    padding-right: 35px !important;
    font-size: 14px;
}
.emoji-btn {
    border-radius: 0 6px 6px 0 !important;
    width: 25px !important;
    height: 25px !important;
}
.modern-lang-select {
    border: 1px solid #dee2e6 !important;
    border-radius: 15px !important;
    padding: 4px 10px !important;
    font-size: 13px !important;
    min-width: 110px !important;
}
.modern-lang-select:focus {
    border-color: #6c757d !important;
    box-shadow: 0 0 0 0.1rem rgba(108,117,125,0.15) !important;
}
.publication-content.translating::after {
    border-radius: 6px;
}
#translation-info {
    border-radius: 6px !important;
    padding: 6px !important;
    font-size: 12px !important;
    margin-top: 8px !important;
}
.comments-list {
    background: #fafafa;
}
.avatar {
    width: 24px !important;
    height: 24px !important;
    font-size: 10px !important;
}
.card {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
}

#export-pdf {
    border-radius: 4px !important;
    transition: all 0.2s ease;
}

#export-pdf:hover {
    background-color: #0d6efd !important;
    color: white !important;
    transform: scale(1.05);
}

#export-pdf:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .compact-card, .compact-reactions, .compact-comments {
        max-width: 100% !important;
        margin: 0 !important;
    }
    .translation-controls { 
        order: -1; 
        width: 100%; 
        margin-bottom: 5px; 
    }
}
</style>

<script>
$(document).ready(function() {
    let currentLang = 'en', isTranslating = false, translationTimeout;
    
    // Store PHP data in JavaScript variables
    const publicationData = {
        id: {{ $publication->id }},
        author: "{{ addslashes($publication->user->full_name) }}",
        date: "{{ $publication->created_at->format('F d, Y') }}",
        category: "{{ ucfirst($publication->categorie) }}",
        hasImage: {{ $publication->image ? 'true' : 'false' }},
        imageUrl: @if($publication->image) "{{ asset('storage/' . $publication->image) }}" @else '' @endif,
        originalTitle: "{{ addslashes($publication->titre) }}",
        originalContent: "{{ addslashes($publication->contenu) }}"
    };
    
    // Your existing functions (unchanged)
    $('.comment-textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 80) + 'px';
    });

    async function translateText(text, targetLang) {
        const cleanText = text.trim();
        if (!cleanText) return cleanText;
        
        try {
            const res = await fetch('https://libretranslate.de/translate', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({q: cleanText, source: 'auto', target: targetLang, format: 'text'})
            });
            if (res.ok) {
                const data = await res.json();
                if (data.translatedText && data.translatedText !== cleanText) return data.translatedText;
            }
        } catch (e) {}
        
        try {
            const url = `https://translate.googleapis.com/translate_a/single?client=gtx&sl=auto&tl=${targetLang}&dt=t&q=${encodeURIComponent(cleanText)}`;
            const res = await fetch(url);
            const data = await res.json();
            return data[0]?.[0]?.[0] || cleanText;
        } catch (e) {}
        return cleanText;
    }

    async function translatePublication(lang) {
        if (isTranslating || lang === currentLang) return;
        isTranslating = true;
        
        const originalTitle = $('#pub-title').data('original') || publicationData.originalTitle;
        const originalContent = $('#pub-content').data('original') || publicationData.originalContent;
        
        if (!$('#pub-title').data('original')) {
            $('#pub-title').data('original', originalTitle);
            $('#pub-content').data('original', originalContent);
        }

        $('#publication-content').addClass('translating');
        $('#translation-loading').removeClass('d-none');
        
        try {
            if (lang === 'fr') {
                $('#pub-title').text(originalTitle);
                $('#pub-content').text(originalContent);
                $('#translation-info').addClass('d-none');
            } else {
                const [title, content] = await Promise.all([
                    translateText(originalTitle, lang),
                    translateText(originalContent, lang)
                ]);
                $('#pub-title').text(title);
                $('#pub-content').text(content);
                const langNames = {
                    'en':'English','es':'Español','de':'Deutsch','it':'Italiano',
                    'pt':'Português','ar':'العربية'
                };
                $('#lang-name').text(langNames[lang] || lang).parent().removeClass('d-none');
            }
            currentLang = lang;
        } catch (e) {
            console.error('Translation error:', e);
        } finally {
            $('#publication-content').removeClass('translating');
            $('#translation-loading').addClass('d-none');
            isTranslating = false;
        }
    }

    $('#lang-select').on('change', function() {
        const lang = $(this).val();
        if (lang === currentLang) return;
        clearTimeout(translationTimeout);
        translationTimeout = setTimeout(() => translatePublication(lang), 100);
    });

    $(window).on('load', () => translatePublication('en'));

    // Emoji functionality (unchanged)
    $('.emoji-btn').on('click', e => {
        e.stopPropagation();
        $('#emojiPicker').toggleClass('d-none');
    });

    $('.emoji-item').on('click', function(e) {
        e.stopPropagation();
        const emoji = $(this).data('emoji');
        const $ta = $('.comment-textarea');
        const pos = $ta[0].selectionStart;
        const text = $ta.val();
        $ta.val(text.substring(0, pos) + emoji + text.substring(pos)).focus();
        $('#emojiPicker').addClass('d-none');
        $ta[0].style.height = 'auto';
        $ta[0].style.height = Math.min($ta[0].scrollHeight, 80) + 'px';
    });

    $(document).on('click', e => {
        if (!$(e.target).closest('.emoji-btn, #emojiPicker').length) {
            $('#emojiPicker').addClass('d-none');
        }
    });

    // 🆕 MODERN SINGLE-PAGE PDF Export
   // 🆕 FIXED MODERN SINGLE-PAGE PDF Export
$('#export-pdf').on('click', async function() {
    if (typeof window.jspdf === 'undefined') {
        Swal.fire('Error', 'PDF library not loaded. Please refresh the page.', 'error');
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    const $btn = $(this);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

    try {
        const title = $('#pub-title').text().trim();
        const content = $('#pub-content').text().trim();
        const likes = $('#likes-count').text();
        const dislikes = $('#dislikes-count').text();
        
        // Modern color scheme
        const primaryColor = [33, 150, 243]; // Blue
        const secondaryColor = [76, 175, 80]; // Green
        const accentColor = [255, 193, 7]; // Yellow
        const textColor = [33, 33, 33]; // Dark gray
        
        // Clean header background
        doc.setFillColor(248, 249, 250);
        doc.rect(0, 0, 210, 45, 'F');
        
        // Title with rounded background
        doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
        doc.roundedRect(15, 10, 180, 25, 5, 5, 'F');
        
        // Title text (clean, no emojis)
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(16);
        doc.setFont('helvetica', 'bold');
        
        // Split long titles to prevent overflow
        const titleLines = doc.splitTextToSize(title, 160);
        doc.text(titleLines, 20, 22, { align: 'left' });
        
        // Decorative line
        doc.setDrawColor(primaryColor[0], primaryColor[1], primaryColor[2]);
        doc.setLineWidth(2);
        doc.line(15, 38, 195, 38);
        
        // Metadata section
        let yPos = 50;
        doc.setTextColor(textColor[0], textColor[1], textColor[2]);
        doc.setFontSize(10);
        doc.setFont('helvetica', 'normal');
        
        // Author info with text icons (no Unicode)
        doc.setFontSize(11);
        doc.setFont('helvetica', 'bold');
        doc.text('Author: ' + publicationData.author, 20, yPos);
        
        doc.setFont('helvetica', 'normal');
        doc.setFontSize(9);
        doc.text('Date: ' + publicationData.date, 20, yPos + 6);
        
        // Category badge
        doc.setFillColor(secondaryColor[0], secondaryColor[1], secondaryColor[2]);
        doc.roundedRect(20, yPos + 12, 50, 8, 3, 3, 'F');
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(8);
        doc.setFont('helvetica', 'bold');
        const categoryText = publicationData.category.length > 12 ? 
            publicationData.category.substring(0, 12) + '...' : publicationData.category;
        doc.text(' ' + categoryText + ' ', 23, yPos + 17, { align: 'center' });
        
        // Content section
        yPos = 80;
        doc.setTextColor(textColor[0], textColor[1], textColor[2]);
        doc.setFontSize(11);
        doc.setFont('helvetica', 'normal');
        
        // Clean content - remove problematic characters and truncate
        const cleanContent = content
            .replace(/[\x00-\x1F\x7F-\x9F]/g, '') // Remove control characters
            .replace(/[^\w\s.,!?-]/g, '') // Keep only basic characters
            .substring(0, 800); // Limit length
        
        const contentLines = doc.splitTextToSize(cleanContent, 170);
        const maxContentLines = 20; // Reserve space for other elements
        const displayLines = contentLines.slice(0, maxContentLines);
        
        if (contentLines.length > maxContentLines) {
            displayLines[maxContentLines - 1] = displayLines[maxContentLines - 1] + '...';
        }
        
        doc.setFontSize(10);
        doc.text(displayLines, 20, yPos);
        yPos += (displayLines.length * 5) + 8;
        
        // FIXED: High-quality image processing
        if (publicationData.hasImage && $('.publication-image').length) {
            try {
                // Temporarily hide image styles that cause blurriness
                const $img = $('.publication-image');
                const originalStyle = $img.attr('style');
                $img.css({
                    'max-height': 'none',
                    'width': 'auto',
                    'height': 'auto',
                    'object-fit': 'contain'
                });
                
                const canvas = await html2canvas($img[0], {
                    scale: 2, // Higher scale for better quality
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: false,
                    width: $img[0].naturalWidth || 400,
                    height: $img[0].naturalHeight || 300,
                    imageTimeout: 15000,
                    removeContainer: true
                });
                
                // Restore original style
                $img.attr('style', originalStyle);
                
                const imgData = canvas.toDataURL('image/jpeg', 0.95); // High quality JPEG
                const imgProps = doc.getImageProperties(imgData);
                const maxImgHeight = Math.min(60, 260 - yPos); // Max height for single page
                const pdfWidth = 160;
                const pdfHeight = Math.min((imgProps.height * pdfWidth) / imgProps.width, maxImgHeight);
                
                if (yPos + pdfHeight < 240) { // Ensure space remains
                    // Add image border
                    doc.setDrawColor(230, 230, 230);
                    doc.roundedRect(20, yPos, pdfWidth, pdfHeight, 3, 3, 'S');
                    doc.addImage(imgData, 'JPEG', 21, yPos + 1, pdfWidth - 2, pdfHeight - 2);
                    yPos += pdfHeight + 10;
                }
            } catch (imgError) {
                console.warn('Image processing failed:', imgError);
                // Continue without image
            }
        }
        
        // Reactions section with TEXT icons (no emojis)
        if (yPos < 220) {
            doc.setFillColor(249, 249, 249);
            doc.rect(15, yPos - 3, 180, 22, 'F');
            
            doc.setTextColor(secondaryColor[0], secondaryColor[1], secondaryColor[2]);
            doc.setFontSize(12);
            doc.setFont('helvetica', 'bold');
            doc.text('Reactions', 20, yPos);
            
            doc.setFontSize(10);
            doc.setTextColor(textColor[0], textColor[1], textColor[2]);
            doc.setFont('helvetica', 'normal');
            doc.text(`Likes: ${likes} | Dislikes: ${dislikes}`, 20, yPos + 6);
            yPos += 18;
        }
        
        // Modern footer
        const footerY = 275;
        doc.setFillColor(primaryColor[0], primaryColor[1], primaryColor[2]);
        doc.roundedRect(10, footerY, 190, 15, 3, 3, 'F');
        
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(9);
        doc.setFont('helvetica', 'bold');
        doc.text(`Publication #${publicationData.id}`, 15, footerY + 6);
        
        doc.setFontSize(7);
        doc.setFont('helvetica', 'normal');
        doc.setTextColor(255, 255, 255, 0.9);
        doc.text(`Generated on {{ now()->format('M d, Y H:i') }}`, 15, footerY + 11);
        
        // Page border for modern look
        doc.setDrawColor(220, 220, 220);
        doc.setLineWidth(0.5);
        doc.rect(5, 5, 200, 287, 'S');
        
        // Save with clean filename
        const cleanTitle = title
            .replace(/[^\w\s-]/g, '') // Remove special characters
            .trim()
            .substring(0, 50)
            .replace(/\s+/g, '-')
            .toLowerCase();
        
        doc.save(`publication-${publicationData.id}-${cleanTitle || 'document'}.pdf`);
        
        $btn.prop('disabled', false).html(originalHtml);
        Swal.fire({
            icon: 'success',
            title: 'PDF Exported!',
            text: 'High-quality single-page document created',
            timer: 2500,
            showConfirmButton: false
        });
        
    } catch (error) {
        console.error('PDF generation error:', error);
        $btn.prop('disabled', false).html(originalHtml);
        Swal.fire('Error', 'Failed to generate PDF: ' + error.message, 'error');
    }
});

    // Your existing reaction and comment handlers (unchanged)
    $('.reaction-form').on('submit', function(e) {
        e.preventDefault(); e.stopPropagation();
        const $form = $(this), $btn = $form.find('button'), orig = $btn.html();
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: $form.attr('action'), method: 'POST', data: $form.serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: data => {
                if (data.success) {
                    $('#likes-count').text(data.likes_count);
                    $('#dislikes-count').text(data.dislikes_count);
                    const $like = $('.like-form button'), $dislike = $('.dislike-form button');
                    if (data.user_reaction === 'like') {
                        $like.removeClass('btn-outline-success').addClass('btn-success');
                        $dislike.removeClass('btn-danger').addClass('btn-outline-danger');
                    } else if (data.user_reaction === 'dislike') {
                        $dislike.removeClass('btn-outline-danger').addClass('btn-danger');
                        $like.removeClass('btn-success').addClass('btn-outline-success');
                    }
                }
            },
            complete: () => $btn.prop('disabled', false).html(orig)
        });
    });

    $('#commentForm').on('submit', function(e) {
        e.stopPropagation();
        if ($('.comment-textarea').val().trim().length < 3) {
            e.preventDefault();
            Swal.fire('Error', 'Comment too short', 'error');
        }
    });

    $(document).on('click input', '#commentForm *', e => e.stopPropagation());
    
    $(document).on('submit', '.reaction-form', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const $form = $(this);
        const $btn = $form.find('button');
        const originalHtml = $btn.html();
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        $.ajax({
            url: $form.attr('action'),
            method: 'POST',
            data: $form.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    $('#likes-count').text(data.likes_count);
                    $('#dislikes-count').text(data.dislikes_count);
                    
                    const $likeBtn = $('.like-form button');
                    const $dislikeBtn = $('.dislike-form button');
                    
                    $likeBtn.removeClass('btn-success').addClass('btn-outline-success');
                    $dislikeBtn.removeClass('btn-danger').addClass('btn-outline-danger');
                    
                    if (data.user_reaction === 'like') {
                        $likeBtn.removeClass('btn-outline-success').addClass('btn-success');
                    } else if (data.user_reaction === 'dislike') {
                        $dislikeBtn.removeClass('btn-outline-danger').addClass('btn-danger');
                    }
                }
            },
            error: function(xhr) {
                let errorMsg = 'Something went wrong';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMsg = xhr.responseJSON.error;
                }
                Swal.fire('Error', errorMsg, 'error');
            },
            complete: function() {
                $btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
@endsection