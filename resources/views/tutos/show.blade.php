@extends('layouts.app')

@section('content')
<style>
    /* Existing styles remain unchanged */
    .tuto-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .tuto-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.3);
    }

    .tuto-card .card-img-top {
        border-bottom: 1px solid rgba(255, 255, 255, 0.18);
    }

    .tuto-card .card-title a {
        color: #1a2035 !important;
        font-weight: 600;
    }

    .tuto-card .card-title a:hover {
        color: #1572E8 !important;
    }

    .tuto-card .card-footer {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    .question-card {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: 15px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.2);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .question-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.3);
    }

    .media-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }

    .media-item {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .media-item:hover {
        transform: scale(1.05);
    }

    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1572E8 0%, #0d47a1 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 18px;
    }

    .reply-avatar {
        width: 35px;
        height: 35px;
        font-size: 14px;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .step-number {
        background: linear-gradient(135deg, #1572E8 0%, #0d47a1 100%);
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 14px;
    }

    .reaction-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        transition: all 0.3s ease;
        border-radius: 25px;
    }

    .reaction-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
    }

    .reaction-btn.dislike {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
    }

    .reaction-btn.dislike:hover {
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
    }

    .form-control:focus {
        border-color: #1572E8;
        box-shadow: 0 0 0 0.2rem rgba(21, 114, 232, 0.25);
        background: rgba(255, 255, 255, 0.8);
    }

    .reply-section {
        background: rgba(255, 255, 255, 0.3);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
    }

    .step-item {
        background: rgba(255, 255, 255, 0.4);
        transition: all 0.3s ease;
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .step-item:hover {
        background: rgba(255, 255, 255, 0.6);
        transform: translateX(5px);
    }

    /* New styles for quiz list and progress */
    .quiz-list {
        margin-top: 2rem;
    }

    .quiz-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.6);
        border-radius: 10px;
        margin-bottom: 0.5rem;
        transition: all 0.3s ease;
    }

    .quiz-item:hover {
        background: rgba(255, 255, 255, 0.8);
        transform: translateY(-2px);
    }

    .quiz-percentage {
        color: #28a745;
        font-weight: bold;
    }

    .quiz-status-failed {
        color: #dc3545;
        font-weight: bold;
    }

    .quiz-status-succeeded {
        color: #28a745;
        font-weight: bold;
    }

    .progress-bar-container {
        margin-top: 1rem;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        padding: 1rem;
    }

    .progress-bar {
        height: 20px;
        background: linear-gradient(135deg, #1572E8 0%, #0d47a1 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
    }

    /* Updated styles for certificate section */
    .certificate-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 30px;
        margin: 2rem 0;
        text-align: center;
        color: white;
    }

    .certificate-icon {
        font-size: 3rem;
        color: #f1c40f;
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
    }

    .certificate-icon:hover {
        transform: scale(1.2);
    }

    .certificate-btn {
        background: linear-gradient(135deg, #f1c40f, #f39c12);
        border: none;
        padding: 12px 30px;
        font-size: 18px;
        border-radius: 50px;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(241, 196, 15, 0.4);
    }

    .certificate-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(241, 196, 15, 0.6);
        color: white;
    }

    .certificate-message {
        font-size: 1.2rem;
        margin-bottom: 1.5rem;
    }

    .achievement-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.2);
        padding: 8px 16px;
        border-radius: 20px;
        margin: 10px 0;
    }

    /* Loading animation */
    .loading-spinner {
        display: none;
    }

    .certificate-loading .loading-spinner {
        display: inline-block;
    }

    .certificate-loading .btn-text {
        display: none;
    }
</style>

<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        
        <!-- Tutorial Card -->
        <div class="row justify-content-center mb-5 wow fadeInUp" data-wow-delay="0.1s">
            <div class="col-lg-11">
                <div class="card h-100 tuto-card">
                    <!-- Header -->
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h1 class="display-5 fw-bold mb-2">{{ $tuto->title }}</h1>
                        <p class="fs-5 fw-medium fst-italic">Tutorial</p>
                    </div>

                    <!-- Content -->
                    <div class="card-body p-5">
                        <div class="row g-5">
                            
                            <!-- Media Section - Centralized -->
                            <div class="col-lg-6">
                                <div class="tuto-card p-4">
                                    @php
                                        $mediaItems = [];
                                        if ($tuto->media && is_array($tuto->media)) {
                                            $mediaItems = array_map(function ($media) {
                                                $media['url'] = asset('storage/' . $media['path']);
                                                return $media;
                                            }, $tuto->media);
                                        }
                                    @endphp
                                    @if (!empty($mediaItems))
                                        <div class="media-grid">
                                            @foreach ($mediaItems as $media)
                                                <div class="media-item">
                                                    @if (strpos($media['mime_type'], 'video') === 0)
                                                        <video class="w-100 h-100" style="height: 200px; object-fit: cover;" controls autoplay loop muted loading="lazy">
                                                            <source src="{{ $media['url'] }}" type="{{ $media['mime_type'] }}">
                                                            Your browser does not support the video tag.
                                                        </video>
                                                    @else
                                                        <img class="w-100 h-100" style="height: 200px; object-fit: cover;" src="{{ $media['url'] }}" alt="Media" loading="lazy">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <img class="img-fluid rounded-3" src="{{ asset('assets/img/placeholder.jpg') }}" alt="Placeholder" loading="lazy">
                                        </div>
                                    @endif
                                    <!-- Reactions -->
                                    <div class="d-flex gap-3 mt-3 justify-content-center">
                                        @auth
                                            <form action="{{ route('tutos.react', $tuto) }}" method="POST">
                                                @csrf
                                                <div class="d-flex gap-3">
                                                    <button name="type" value="like" class="btn reaction-btn text-white py-2 px-4 d-flex align-items-center gap-2" aria-label="Like tutorial">
                                                        <i class="fas fa-thumbs-up"></i>
                                                        <span class="fw-medium">{{ $tuto->likes_count }}</span>
                                                    </button>
                                                    <button name="type" value="dislike" class="btn reaction-btn dislike text-white py-2 px-4 d-flex align-items-center gap-2" aria-label="Dislike tutorial">
                                                        <i class="fas fa-thumbs-down"></i>
                                                        <span class="fw-medium">{{ $tuto->dislikes_count }}</span>
                                                    </button>
                                                </div>
                                            </form>
                                        @else
                                            <div class="d-flex gap-3">
                                                <div class="btn reaction-btn text-white py-2 px-4 d-flex align-items-center gap-2">
                                                    <i class="fas fa-thumbs-up"></i>
                                                    <span class="fw-medium">{{ $tuto->likes_count }}</span>
                                                </div>
                                                <div class="btn reaction-btn dislike text-white py-2 px-4 d-flex align-items-center gap-2">
                                                    <i class="fas fa-thumbs-down"></i>
                                                    <span class="fw-medium">{{ $tuto->dislikes_count }}</span>
                                                </div>
                                            </div>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <!-- Details Section -->
                            <div class="col-lg-6">
                                <!-- Meta Info and Description -->
                                <div class="tuto-card p-4 mb-4">
                                    @php
                                        $categories = [
                                            'plastique' => 'Plastic',
                                            'bois' => 'Wood',
                                            'papier' => 'Paper',
                                            'metal' => 'Metal',
                                            'verre' => 'Glass',
                                            'autre' => 'Other',
                                        ];
                                        $categoryKey = $tuto->category && is_object($tuto->category) && property_exists($tuto->category, 'slug') 
                                            ? strtolower(trim($tuto->category->slug)) 
                                            : null;
                                        $englishCategory = $categoryKey && array_key_exists($categoryKey, $categories) 
                                            ? $categories[$categoryKey] 
                                            : ($categoryKey ? ucfirst($categoryKey) : 'Unknown');
                                    @endphp
                                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-tag text-success"></i>
                                            <span>{{ $englishCategory }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-eye text-info"></i>
                                            <span>{{ number_format($tuto->views) }} views</span>
                                        </div>
                                    </div>
                                    <h3 class="h4 fw-bold text-dark mb-4 d-flex align-items-center gap-2">
                                        Description
                                    </h3>
                                    <p class="text-dark fs-6 lh-lg">{{ $tuto->description }}</p>
                                </div>
                                <!-- Steps -->
                                <div class="tuto-card p-4 mb-4">
                                    <h3 class="h4 fw-bold text-dark mb-4">Steps</h3>
                                    <div class="list-group list-group-flush">
                                        @foreach ($tuto->steps as $index => $step)
                                            <div class="step-item p-3 mb-2 d-flex align-items-start gap-3">
                                                <div class="step-number">{{ $index + 1 }}</div>
                                                <p class="mb-0 text-dark lh-lg">{{ $step }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <!-- Progress Bar (Always Displayed) -->
                                <div class="progress-bar-container">
                                    <p>Progress: {{ $completedQuizzes }}/{{ $totalQuizzes }} (Average: {{ number_format($averagePercentage, 2) }}%)</p>
                                    <div class="progress-bar" style="width: {{ ($totalQuizzes > 0 ? ($completedQuizzes / $totalQuizzes) * 100 : 0) }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificate Section -->
        @auth
            @php
                $allQuizzesCompleted = $completedQuizzes === $totalQuizzes;
                $averagePercentageThreshold = $averagePercentage >= 70;
            @endphp
            
            @if ($allQuizzesCompleted && $averagePercentageThreshold)
                <div class="certificate-section wow fadeInUp" data-wow-delay="0.2s">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <i class="fas fa-certificate certificate-icon"></i>
                            <h2 class="text-white mb-3">Congratulations! 🎉</h2>
                            <p class="certificate-message">
                                You've successfully completed all quizzes with an excellent average score of 
                                <strong>{{ number_format($averagePercentage, 2) }}%</strong>!
                            </p>
                            
                            <div class="achievement-badge">
                                <i class="fas fa-trophy me-2"></i>
                                <span>Certificate Unlocked!</span>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('certificates.show', $tuto) }}" class="btn certificate-btn me-3">
                                    <i class="fas fa-eye me-2"></i>
                                    View Certificate
                                </a>
                                
                                <!-- Client-side PDF export (fetch the certificate page and render #certificate) -->
                                <button type="button" class="btn certificate-btn" id="downloadClientBtn" aria-label="Download certificate as PDF">
                                    <span class="btn-text">
                                        <i class="fas fa-download me-2"></i>
                                        Télécharger PDF
                                    </span>
                                    <div class="loading-spinner">
                                        <div class="spinner-border spinner-border-sm me-2" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        Génération du PDF...
                                    </div>
                                </button>
                             </div>
                            
                            <p class="mt-3 mb-0 opacity-75">
                                <small>Your personalized certificate is ready for download</small>
                            </p>
                        </div>
                    </div>
                </div>
            @elseif ($allQuizzesCompleted && !$averagePercentageThreshold)
                <div class="alert alert-warning text-center wow fadeInUp" data-wow-delay="0.2s">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Almost there!</strong> You've completed all quizzes, but your average score 
                    ({{ number_format($averagePercentage, 2) }}%) is below the 70% required for certification.
                    <a href="{{ route('quizzes.index') }}" class="alert-link">Try again to improve your score!</a>
                </div>
            @else
                <div class="alert alert-info text-center wow fadeInUp" data-wow-delay="0.2s">
                    <i class="fas fa-info-circle me-2"></i>
                    Complete all <strong>{{ $totalQuizzes }}</strong> quizzes with an average score of 70% or higher to unlock your certificate. 
                    Currently completed: <strong>{{ $completedQuizzes }}/{{ $totalQuizzes }}</strong>
                </div>
            @endif
        @endauth

        <!-- Questions & Answers Section -->
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Questions & Answers</p>
            <h1 class="display-6">Ask Questions About This Tutorial</h1>
        </div>

        <!-- Question Form -->
        @auth
            <div class="row justify-content-center wow fadeInUp mb-5" data-wow-delay="0.3s">
                <div class="col-lg-8">
                    <div class="tuto-card p-4">
                        <form action="{{ route('tutos.question', $tuto) }}" method="POST">
                            @csrf
                            <textarea name="question_text" required placeholder="Ask your question..." class="form-control border-0 bg-white bg-opacity-75 p-3 mb-3" rows="4" style="resize: none; border-radius: 10px;"></textarea>
                            <button type="submit" class="btn btn-primary rounded-pill py-2 px-4 d-flex align-items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Ask a Question
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endauth

        <!-- Questions List -->
        @if ($tuto->questions->whereNull('parent_id')->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                <p class="text-muted fs-5">No questions for this tutorial.</p>
            </div>
        @else
            <div class="row g-4">
                @foreach ($tuto->questions->whereNull('parent_id') as $question)
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="question-card">
                            
                            <!-- Question -->
                            <div class="p-4">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="avatar-circle">
                                        {{ strtoupper(substr($question->user->full_name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <h5 class="fw-bold text-dark mb-0">{{ $question->user->full_name }}</h5>
                                            <span class="text-muted small d-flex align-items-center gap-1">
                                                <i class="fas fa-clock"></i>
                                                {{ $question->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <p class="text-dark lh-lg mb-3">{{ $question->question_text }}</p>
                                        
                                        @auth
                                            <button class="btn btn-sm btn-outline-primary d-flex align-items-center gap-2" onclick="toggleReplyForm({{ $question->id }})" style="border-radius: 20px;">
                                                <i class="fas fa-reply"></i>
                                                Reply
                                            </button>
                                        @endauth
                                    </div>
                                </div>
                            </div>

                            <!-- Replies -->
                            @if ($question->replies->count() > 0)
                                <div class="reply-section px-4 py-3">
                                    @foreach ($question->replies as $reply)
                                        <div class="d-flex align-items-start gap-3 mb-3 ms-4">
                                            <div class="reply-avatar avatar-circle">
                                                {{ strtoupper(substr($reply->user->full_name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-3 mb-1">
                                                    <h6 class="fw-medium text-dark mb-0 small">{{ $reply->user->full_name }}</h6>
                                                    <span class="text-muted small">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-dark small mb-0">{{ $reply->question_text }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Reply Form -->
                            @auth
                                <div id="reply-form-{{ $question->id }}" class="reply-section px-4 py-3" style="display: none;">
                                    <form action="{{ route('tutos.question', $tuto) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $question->id }}">
                                        <textarea name="question_text" required placeholder="Your reply..." class="form-control border-0 bg-white bg-opacity-75 p-3 mb-3 small" rows="3" style="resize: none; border-radius: 10px;"></textarea>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-sm btn-primary px-3 d-flex align-items-center gap-2 rounded-pill">
                                                <i class="fas fa-paper-plane"></i>
                                                Reply
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary px-3 rounded-pill" onclick="toggleReplyForm({{ $question->id }})">
                                                Cancel
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Quiz List Section -->
        @if ($quizzes->isNotEmpty())
            <div class="quiz-list">
                <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
                    <p class="fs-5 fw-medium fst-italic text-primary">Related Quizzes</p>
                    <h1 class="display-6">Test Your Knowledge</h1>
                </div>
                @foreach ($quizzes as $quiz)
                    <div class="quiz-item">
                        @php
                            $attempt = $quiz->attempts->firstWhere('user_id', Auth::id());
                        @endphp
                        @if ($attempt)
                            <a href="{{ route('quizzes.show', $quiz) }}" class="text-decoration-none">
                                {{ $quiz->title }} - <span class="quiz-percentage">{{ number_format($attempt->percentage, 2) }}%</span> 
                                <span class="{{ $attempt->percentage >= 70 ? 'quiz-status-succeeded' : 'quiz-status-failed' }}">
                                    {{ $attempt->percentage >= 70 ? 'Succeeded' : 'Failed' }}
                                </span>
                            </a>
                        @else
                            <a href="{{ route('quizzes.show', $quiz) }}" class="text-decoration-none">{{ $quiz->title }}</a>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<script>
function toggleReplyForm(questionId) {
    const form = document.getElementById('reply-form-' + questionId);
    if (form.style.display === 'none' || form.style.display === '') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}

// PDF download loading animation and client-side generation
document.addEventListener('DOMContentLoaded', function() {
    const downloadBtn = document.getElementById('downloadClientBtn');
    if (!downloadBtn) return;

    // URL of the certificate page (same origin)
    const certUrl = "{{ route('certificates.show', $tuto) }}";

    downloadBtn.addEventListener('click', function() {
        downloadBtn.classList.add('certificate-loading');

        fetch(certUrl, { credentials: 'same-origin', headers: { 'Accept': 'text/html' } })
            .then(res => {
                if (!res.ok) throw new Error('Network response was not ok');
                return res.text();
            })
            .then(html => {
                // parse returned HTML and extract certificate element + style tags
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const certEl = doc.querySelector('#certificate');
                if (!certEl) throw new Error('Certificate element not found in fetched page');

                // collect <style> content from fetched doc (to preserve certificate styles)
                const styles = Array.from(doc.querySelectorAll('style')).map(s => s.innerHTML).join('\n');

                // build printable wrapper
                const wrapper = document.createElement('div');
                wrapper.style.background = '#ffffff';
                wrapper.style.color = '#000';
                wrapper.style.boxSizing = 'border-box';
                wrapper.style.padding = '20px';
                wrapper.style.width = '800px';

                if (styles) {
                    const styleTag = document.createElement('style');
                    styleTag.innerHTML = styles;
                    wrapper.appendChild(styleTag);
                }

                // clone the certificate node from fetched doc into wrapper
                wrapper.appendChild(document.importNode(certEl, true));
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
                        downloadBtn.classList.remove('certificate-loading');
                    }).catch(function(err) {
                        console.error('PDF generation error', err);
                        downloadBtn.classList.remove('certificate-loading');
                    });
                }, 200);
            })
            .catch(err => {
                console.error('Failed to fetch certificate page:', err);
                downloadBtn.classList.remove('certificate-loading');
                alert('Impossible de récupérer le certificat pour génération PDF.');
            });
    });
});
</script>

<!-- html2pdf client-side library (no composer) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.3/html2pdf.bundle.min.js"></script>
</body>
</html>