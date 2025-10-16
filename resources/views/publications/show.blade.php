@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-6">{{ $publication->titre }}</h1>
        </div>

        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-light d-flex justify-content-between align-items-start p-3">
                <div>
                    <p class="text-muted mb-1">
                        <i class="fas fa-user me-1"></i>By {{ $publication->user->full_name }} 
                        <i class="fas fa-clock ms-2 me-1"></i>{{ $publication->created_at->diffForHumans() }}
                    </p>
                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1">
                        {{ ucfirst($publication->categorie) }}
                    </span>
                    @if($publication->user->isBanned())
                        <span class="badge bg-danger ms-2">🚫 Banned</span>
                    @endif
                </div>
                @if(auth()->id() === $publication->user_id)
                    <a href="{{ route('publications.edit', $publication->id) }}" 
                       class="btn btn-sm btn-outline-success">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                @endif
            </div>
            <div class="card-body p-4">
                <div class="publication-content">
                    <p class="lead fs-5">{{ $publication->contenu }}</p>
                    @if($publication->image)
                        <img src="{{ asset('storage/' . $publication->image) }}" 
                             alt="{{ $publication->titre }}" 
                             class="img-fluid rounded shadow-sm mb-3" 
                             style="max-height: 400px; object-fit: cover;">
                    @endif
                </div>
            </div>
        </div>

        <!-- Reactions Section -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <div class="reaction-stats d-flex align-items-center gap-4">
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-thumbs-up text-success fs-5"></i>
                            <strong id="likes-count" class="text-success">{{ $publication->likes_count }}</strong>
                            <span class="text-muted">likes</span>
                        </span>
                        <span class="d-flex align-items-center gap-2">
                            <i class="fas fa-thumbs-down text-danger fs-5"></i>
                            <strong id="dislikes-count" class="text-danger">{{ $publication->dislikes_count }}</strong>
                            <span class="text-muted">dislikes</span>
                        </span>
                    </div>
                    
                    @if(auth()->check() && auth()->id() !== $publication->user_id && !auth()->user()->isBanned())
                    <div class="reaction-buttons d-flex gap-2">
                        <form method="POST" action="{{ route('publications.like', $publication) }}" class="like-form d-inline reaction-form">
                            @csrf
                            @if($publication->isLikedByAuthUser())
                                <button type="submit" class="btn btn-success reaction-btn px-3 py-2" title="Remove Like">
                                    <i class="fas fa-thumbs-up me-1"></i>Unlike
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-success reaction-btn px-3 py-2" title="Like">
                                    <i class="fas fa-thumbs-up me-1"></i>Like
                                </button>
                            @endif
                        </form>
                        
                        <form method="POST" action="{{ route('publications.dislike', $publication) }}" class="dislike-form d-inline reaction-form">
                            @csrf
                            @if($publication->isDislikedByAuthUser())
                                <button type="submit" class="btn btn-danger reaction-btn px-3 py-2" title="Remove Dislike">
                                    <i class="fas fa-thumbs-down me-1"></i>Undo
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-danger reaction-btn px-3 py-2" title="Dislike">
                                    <i class="fas fa-thumbs-down me-1"></i>Dislike
                                </button>
                            @endif
                        </form>
                    </div>
                    @elseif(auth()->check() && auth()->user()->isBanned())
                    <div class="alert alert-danger d-inline-block p-2">
                        <i class="fas fa-ban me-1"></i>
                        <small>You are banned and cannot react.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modern Comments Section -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-light border-0 py-3">
                <h3 class="mb-0 d-flex align-items-center gap-2">
                    <i class="fas fa-comments text-success"></i>
                    Comments ({{ $publication->commentaires->count() }})
                </h3>
            </div>
            
            <div class="card-body p-0">
                <!-- Comments List -->
                @if($publication->commentaires->isNotEmpty())
                    <div class="comments-list">
                        @foreach($publication->commentaires->sortByDesc('created_at') as $commentaire)
                            <div class="comment-item border-bottom py-3 px-4" data-comment-id="{{ $commentaire->id }}">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="flex-shrink-0">
                                        <div class="avatar bg-success text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ substr($commentaire->user->full_name, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                            <h6 class="mb-1 fw-semibold">{{ $commentaire->user->full_name }}</h6>
                                            <small class="text-muted">{{ $commentaire->created_at->diffForHumans() }}</small>
                                        </div>
                                        @if($commentaire->user->isBanned())
                                            <span class="badge bg-danger mb-1">🚫 Banned</span>
                                        @endif
                                        <p class="mb-0 comment-text">{!! nl2br(e($commentaire->contenu)) !!}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-comment-slash fa-2x text-muted mb-3"></i>
                        <p class="text-muted">No comments yet. Be the first!</p>
                    </div>
                @endif

                <!-- Modern Comment Form with Emojis -->
                @if(auth()->check() && !auth()->user()->isBanned())
                    <div class="card-footer border-0 bg-white p-4">
                        <form method="POST" action="{{ route('commentaires.store', $publication->id) }}" class="comment-form" id="commentForm">
                            @csrf
                            <div class="input-group">
                                <div class="flex-grow-1 position-relative">
                                    <textarea name="contenu" 
                                              class="form-control comment-textarea @error('contenu') is-invalid @enderror" 
                                              rows="2" 
                                              placeholder="Share your thoughts... Add emojis! 😊🎉"
                                              required>{{ old('contenu') }}</textarea>
                                    @error('contenu')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Emoji Picker Button -->
                                    <button type="button" class="btn btn-outline-secondary emoji-btn position-absolute" 
                                            style="right: 10px; top: 50%; transform: translateY(-50%); z-index: 10;" 
                                            title="Add emojis">
                                        <i class="fas fa-smile"></i>
                                    </button>
                                    
                                    <!-- Emoji Picker -->
                                    <div class="emoji-picker position-absolute d-none" id="emojiPicker" style="right: 0; top: 100%; margin-top: 5px; background: white; border: 1px solid #dee2e6; border-radius: 8px; padding: 10px; max-width: 300px; max-height: 200px; overflow-y: auto; box-shadow: 0 5px 15px rgba(0,0,0,0.1); z-index: 1000;">
                                        <div class="emoji-grid d-flex flex-wrap gap-1">
                                            @php
                                                $emojis = ['😀', '😂', '🤔', '👍', '👎', '❤️', '🔥', '✨', '🎉', '🚀', '🌟', '💡', '♻️', '🔧', '⚙️', '📝', '💚', '🌱', '🗑️', '♻️'];
                                            @endphp
                                            @foreach($emojis as $emoji)
                                                <span class="emoji-item cursor-pointer fs-4 p-1 rounded hover-bg-light" data-emoji="{{ $emoji }}" title="{{ $emoji }}"> {{ $emoji }} </span>
                                            @endforeach
                                        </div>
                                        <div class="mt-2">
                                            <small class="text-muted">Click on an emoji to add it.</small>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success ms-2" id="submitComment">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                            <div class="form-text mt-1">
                                <i class="fas fa-info-circle me-1"></i>Comments are moderated. Please stay respectful!
                            </div>
                        </form>
                    </div>
                @elseif(auth()->check() && auth()->user()->isBanned())
                    <div class="card-footer bg-light border-0 p-4 text-center">
                        <div class="alert alert-danger d-inline-block">
                            <i class="fas fa-ban me-2"></i>
                            You are banned and cannot comment.
                        </div>
                    </div>
                @else
                    <div class="card-footer bg-light border-0 p-4 text-center">
                        <a href="{{ route('login') }}" class="btn btn-outline-success">
                            <i class="fas fa-sign-in-alt me-2"></i>Log in to comment.
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.reaction-btn {
    transition: all 0.2s ease;
    border-radius: 25px;
    padding: 8px 16px;
    font-weight: 500;
}
.reaction-btn:hover {
    transform: scale(1.02);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}
.reaction-stats strong {
    font-size: 1.3em;
}
.active-like, .active-dislike {
    animation: pulse 0.5s ease-in-out;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.comment-textarea {
    resize: vertical;
    min-height: 60px;
    border-radius: 12px;
    padding-right: 60px;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}
.comment-textarea:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
}

.emoji-btn {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.emoji-btn:hover {
    background-color: #e9ecef;
}

.emoji-item {
    cursor: pointer;
    transition: all 0.2s ease;
    border-radius: 4px;
}
.emoji-item:hover {
    background-color: #e9ecef !important;
    transform: scale(1.2);
}

.comment-item {
    transition: all 0.2s ease;
}
.comment-item:hover {
    background-color: #f8f9fa;
}

.avatar {
    font-weight: bold;
}

.cursor-pointer {
    cursor: pointer;
}

.hover-bg-light:hover {
    background-color: #f8f9fa !important;
}

.comments-list {
    max-height: 500px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .reaction-buttons {
        flex-direction: column;
    }
    .reaction-btn {
        width: 100%;
    }
}
</style>

<script>
$(document).ready(function() {
    // Emoji Picker Functionality
    $('.emoji-btn').on('click', function() {
        const $picker = $('#emojiPicker');
        $picker.toggleClass('d-none');
    });

    // Add emoji to textarea
    $('.emoji-item').on('click', function() {
        const emoji = $(this).data('emoji');
        const $textarea = $('.comment-textarea');
        const cursorPos = $textarea[0].selectionStart;
        const text = $textarea.val();
        
        const newText = text.substring(0, cursorPos) + emoji + text.substring(cursorPos);
        $textarea.val(newText);
        $textarea.focus();
        
        // Move cursor after emoji
        const newPos = cursorPos + emoji.length;
        $textarea[0].setSelectionRange(newPos, newPos);
        
        // Hide picker
        $('#emojiPicker').addClass('d-none');
    });

    // Hide emoji picker when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.emoji-btn, #emojiPicker').length) {
            $('#emojiPicker').addClass('d-none');
        }
    });

    // Auto-resize textarea
    $('.comment-textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // AJAX Reaction Handling
    $('.reaction-form').on('submit', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const url = $form.attr('action');
        const $btn = $form.find('button');
        const originalHtml = $btn.html();
        const originalClass = $btn.attr('class');
        const isLikeForm = $form.hasClass('like-form');
        const wasActive = $btn.hasClass('active-like') || $btn.hasClass('active-dislike');
        
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Loading...');
        
        $.ajax({
            url: url,
            method: 'POST',
            data: $form.serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function(data) {
                if (data.success) {
                    $('#likes-count').text(data.likes_count);
                    $('#dislikes-count').text(data.dislikes_count);
                    
                    const $likeBtn = $('.like-form button');
                    const $dislikeBtn = $('.dislike-form button');
                    
                    if (data.user_reaction === 'like') {
                        $likeBtn.removeClass('btn-outline-success active-dislike').addClass('btn-success active-like');
                        $likeBtn.html('<i class="fas fa-thumbs-up me-1"></i>Unlike');
                        
                        $dislikeBtn.removeClass('btn-danger active-dislike').addClass('btn-outline-danger');
                        $dislikeBtn.html('<i class="fas fa-thumbs-down me-1"></i>Dislike');
                    } else if (data.user_reaction === 'dislike') {
                        $dislikeBtn.removeClass('btn-outline-danger active-like').addClass('btn-danger active-dislike');
                        $dislikeBtn.html('<i class="fas fa-thumbs-down me-1"></i>Undo');
                        
                        $likeBtn.removeClass('btn-success active-like').addClass('btn-outline-success');
                        $likeBtn.html('<i class="fas fa-thumbs-up me-1"></i>Like');
                    } else {
                        $likeBtn.removeClass('btn-success active-like').addClass('btn-outline-success');
                        $likeBtn.html('<i class="fas fa-thumbs-up me-1"></i>Like');
                        
                        $dislikeBtn.removeClass('btn-danger active-dislike').addClass('btn-outline-danger');
                        $dislikeBtn.html('<i class="fas fa-thumbs-down me-1"></i>Dislike');
                    }
                    
                    let message = '';
                    if (isLikeForm) {
                        message = wasActive ? 'Unlike removed' : '👍 Post liked!';
                    } else {
                        message = wasActive ? 'Dislike removed' : '👎 Post disliked!';
                    }
                    
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: message,
                        showConfirmButton: false,
                        timer: 2500,
                        timerProgressBar: true
                    });
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire('Error', errorMessage, 'error');
                $btn.attr('class', originalClass).html(originalHtml);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });

    // Real-time comment form validation
    $('#commentForm').on('submit', function(e) {
        const contenu = $('.comment-textarea').val().trim();
        if (contenu.length < 3) {
            e.preventDefault();
            Swal.fire('Error', 'Comment must be at least 3 characters.', 'error');
            return false;
        }
    });
});
</script>
@endsection