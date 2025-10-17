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

    /* New style for certificate icon */
    .certificate-icon {
        font-size: 2rem;
        color: #28a745;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .certificate-icon:hover {
        transform: scale(1.2);
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
                                        $englishCategory = $categories[$tuto->category] ?? ucfirst($tuto->category);
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
                                    <div class="progress-bar" style="width: {{ ($completedQuizzes / $totalQuizzes) * 100 }}%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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

        <!-- Progress Bar (Always Displayed) -->
        @auth
            <div class="progress-bar-container">
                <p>Progress: {{ $completedQuizzes }}/{{ $totalQuizzes }} (Average: {{ number_format($averagePercentage, 2) }}%)</p>
                <div class="progress-bar" style="width: {{ ($completedQuizzes / $totalQuizzes) * 100 }}%;"></div>
            </div>

            <!-- Certificate Logic Based on Average Percentage -->
            @php
                $allQuizzesCompleted = $completedQuizzes === $totalQuizzes;
                $averagePercentageThreshold = $averagePercentage >= 70;
            @endphp
            @if ($allQuizzesCompleted && $averagePercentageThreshold)
                <div class="text-center mt-4">
                    <i class="fas fa-certificate certificate-icon" data-toggle="tooltip" title="Upload Certificate"></i>
                    <a href="{{ route('certificates.upload', $tuto) }}" class="btn btn-success mt-2">Upload Your Certificate</a>
                </div>
            @elseif ($allQuizzesCompleted && !$averagePercentageThreshold)
                <div class="text-center mt-4 text-danger">
                    <p>Failed: Average quiz score is below 70%. No certificate available.</p>
                </div>
            @endif
        @endauth
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
    //r
}
</script>

@endsection