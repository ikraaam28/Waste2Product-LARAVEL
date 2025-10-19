@extends('layouts.admin')

@section('title', 'View Tutorial')

@section('content')
<style>
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

    .tuto-card .card-header {
        background: linear-gradient(135deg, #1572E8 0%, #0d47a1 100%);
        color: white;
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

    .reaction-info {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        border-radius: 25px;
        padding: 8px 16px;
        color: white;
        font-weight: medium;
    }

    .reaction-info.dislike {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
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

    .admin-info {
        background: rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>

<div class="container">
    <div class="page-inner">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="page-header">
            <h3 class="fw-bold mb-3">View Tutorial</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('admin') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.tutos.index') }}">Tutorials</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">View</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card tuto-card">
                    <!-- Header -->
                    <div class="card-header text-center py-4">
                        <h1 class="display-5 fw-bold mb-2">{{ $tuto->title }}</h1>
                        <p class="fs-5 fw-medium fst-italic">Tutorial</p>
                    </div>

                    <!-- Content -->
                    <div class="card-body p-5">
                        <div class="row g-5">
                            <!-- Media Section -->
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
                                        <div class="reaction-info text-white py-2 px-4 d-flex align-items-center gap-2">
                                            <i class="fas fa-thumbs-up"></i>
                                            <span class="fw-medium">{{ $tuto->likes_count }}</span>
                                        </div>
                                        <div class="reaction-info dislike text-white py-2 px-4 d-flex align-items-center gap-2">
                                            <i class="fas fa-thumbs-down"></i>
                                            <span class="fw-medium">{{ $tuto->dislikes_count }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Details Section -->
                            <div class="col-lg-6">
                                <!-- Meta Info and Description -->
                                <div class="tuto-card p-4 mb-4">
                                    @php
                                        $categories = [
                                            'plastic' => 'Plastic',
                                            'wood' => 'Wood',
                                            'paper' => 'Paper',
                                            'metal' => 'Metal',
                                            'glass' => 'Glass',
                                            'other' => 'Other',
                                        ];
                                        $englishCategory = $tuto->category ? ($categories[$tuto->category->name] ?? ucfirst($tuto->category->name)) : 'Uncategorized';
                                    @endphp
                                    <div class="d-flex flex-wrap align-items-center gap-3 text-muted mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fas fa-user text-primary"></i>
                                            <span class="fw-medium">{{ $tuto->user->full_name }}</span>
                                        </div>
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

                                <!-- Admin Info -->
                                <div class="admin-info p-4">
                                    <h3 class="h4 fw-bold text-dark mb-4">Admin Information</h3>
                                    <div class="mb-3">
                                        <strong>Publication Status:</strong>
                                        <span class="{{ $tuto->is_published ? 'text-success' : 'text-warning' }}">
                                            {{ $tuto->is_published ? 'Published' : 'Unpublished' }}
                                        </span>
                                    </div>
                                    <div>
                                        <strong>Admin Notes:</strong>
                                        <p class="text-dark mb-0">{{ $tuto->admin_notes ?? 'No notes provided.' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer with Back Button -->
                    <div class="card-footer text-center">
                        <a href="{{ route('admin.tutos.index') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left"></i> Back to Tutorials
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Questions & Answers Section -->
        <div class="section-title text-center mx-auto mt-5" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Questions & Answers</p>
            <h1 class="display-6">Questions About This Tutorial</h1>
        </div>

        <!-- Questions List -->
        @if ($tuto->questions->whereNull('parent_id')->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                <p class="text-muted fs-5">No questions for this tutorial.</p>
            </div>
        @else
            <div class="row g-4 mt-3">
                @foreach ($tuto->questions->whereNull('parent_id') as $question)
                    <div class="col-12">
                        <div class="question-card" id="question-{{ $question->id }}">
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
                                        <!-- Admin Actions -->
                                        @if (Auth::check() && Auth::user()->isAdmin())
                                            <div class="d-flex gap-2">
                                                <button onclick="deleteQuestion({{ $question->id }})" class="btn btn-danger btn-sm rounded-pill">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                                @if ($question->user->is_active)
                                                    <button onclick="banUser({{ $question->user->id }}, 'question', {{ $question->id }})" class="btn btn-warning btn-sm rounded-pill">
                                                        <i class="fas fa-ban"></i> Ban User
                                                    </button>
                                                @else
                                                    <span class="badge bg-secondary">User Banned</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Replies -->
                            @if ($question->replies->count() > 0)
                                <div class="reply-section px-4 py-3">
                                    @foreach ($question->replies as $reply)
                                        <div class="d-flex align-items-start gap-3 mb-3 ms-4" id="reply-{{ $reply->id }}">
                                            <div class="reply-avatar avatar-circle">
                                                {{ strtoupper(substr($reply->user->full_name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-3 mb-1">
                                                    <h6 class="fw-medium text-dark mb-0 small">{{ $reply->user->full_name }}</h6>
                                                    <span class="text-muted small">{{ $reply->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-dark small mb-0">{{ $reply->question_text }}</p>
                                                <!-- Admin Actions for Replies -->
                                                @if (Auth::check() && Auth::user()->isAdmin())
                                                    <div class="d-flex gap-2 mt-2">
                                                        <button onclick="deleteQuestion({{ $reply->id }})" class="btn btn-danger btn-sm rounded-pill">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                        @if ($reply->user->is_active)
                                                            <button onclick="banUser({{ $reply->user->id }}, 'reply', {{ $reply->id }})" class="btn btn-warning btn-sm rounded-pill">
                                                                <i class="fas fa-ban"></i> Ban User
                                                            </button>
                                                        @else
                                                            <span class="badge bg-secondary">User Banned</span>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- AJAX Script -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    function deleteQuestion(questionId) {
        if (confirm('Are you sure you want to delete this question or reply?')) {
            axios.delete('/admin/questions/' + questionId, {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).then(response => {
                document.getElementById('question-' + questionId)?.remove();
                document.getElementById('reply-' + questionId)?.remove();
                showAlert('success', response.data.message);
            }).catch(error => {
                showAlert('danger', error.response?.data?.message || 'Failed to delete question.');
            });
        }
    }

    function banUser(userId, type, elementId) {
        if (confirm('Are you sure you want to ban this user?')) {
            axios.post('/admin/users/' + userId + '/ban', {
                _token: '{{ csrf_token() }}',
                ban_reason: 'Inappropriate ' + type
            }).then(response => {
                const button = document.querySelector(`#${type}-${elementId} .btn-warning`);
                if (button) {
                    button.outerHTML = '<span class="badge bg-secondary">User Banned</span>';
                }
                showAlert('success', response.data.message);
            }).catch(error => {
                showAlert('danger', error.response?.data?.message || 'Failed to ban user.');
            });
        }
    }

    function showAlert(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show`;
        alert.role = 'alert';
        alert.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.querySelector('.page-inner').prepend(alert);
        setTimeout(() => alert.remove(), 5000);
    }
</script>
@endsection