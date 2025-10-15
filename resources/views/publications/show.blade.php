@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-6">{{ $publication->titre }}</h1>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted mb-1">Par {{ $publication->user->full_name }} 
                        | {{ $publication->created_at->diffForHumans() }}</p>
                    <span class="badge bg-success">{{ ucfirst($publication->categorie) }}</span>
                    @if($publication->user->isBanned())
                        <span class="badge bg-danger ms-2">Banni</span>
                    @endif
                </div>
                @if(auth()->id() === $publication->user_id)
                    <a href="{{ route('publications.edit', $publication->id) }}" 
                       class="btn btn-sm btn-outline-primary">Modifier</a>
                @endif
            </div>
            <div class="card-body">
                <p>{{ $publication->contenu }}</p>
                @if($publication->image)
                    <img src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->titre }}" class="img-fluid mb-3" style="max-height: 300px;">
                @endif
            </div>
        </div>

        <!-- Reactions Section -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="reaction-stats">
                        <span class="me-4">
                            <i class="fas fa-thumbs-up text-success"></i>
                            <strong id="likes-count">{{ $publication->likes_count }}</strong> likes
                        </span>
                        <span>
                            <i class="fas fa-thumbs-down text-danger"></i>
                            <strong id="dislikes-count">{{ $publication->dislikes_count }}</strong> dislikes
                        </span>
                    </div>
                    
                    @if(auth()->check() && auth()->id() !== $publication->user_id && !auth()->user()->isBanned())
                    <div class="reaction-buttons d-flex gap-2">
                        {{-- Like Toggle Button --}}
                        <form method="POST" action="{{ route('publications.like', $publication) }}" class="like-form d-inline reaction-form">
                            @csrf
                            @if($publication->isLikedByAuthUser())
                                <button type="submit" class="btn btn-success btn-sm reaction-btn active-like" title="Remove Like">
                                    <i class="fas fa-thumbs-up"></i> Unlike
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-success btn-sm reaction-btn" title="Like">
                                    <i class="fas fa-thumbs-up"></i> Like
                                </button>
                            @endif
                        </form>
                        
                        {{-- Dislike Toggle Button --}}
                        <form method="POST" action="{{ route('publications.dislike', $publication) }}" class="dislike-form d-inline reaction-form">
                            @csrf
                            @if($publication->isDislikedByAuthUser())
                                <button type="submit" class="btn btn-danger btn-sm reaction-btn active-dislike" title="Remove Dislike">
                                    <i class="fas fa-thumbs-down"></i> Undo
                                </button>
                            @else
                                <button type="submit" class="btn btn-outline-danger btn-sm reaction-btn" title="Dislike">
                                    <i class="fas fa-thumbs-down"></i> Dislike
                                </button>
                            @endif
                        </form>
                    </div>
                    @elseif(auth()->check() && auth()->user()->isBanned())
                    <div class="alert alert-danger">
                        <small>Vous êtes banni et ne pouvez pas réagir aux publications.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <h2>Commentaires</h2>
        @if($publication->commentaires->isNotEmpty())
            @foreach($publication->commentaires as $commentaire)
                <div class="card mb-2">
                    <div class="card-body">
                        <p>{{ $commentaire->contenu }}</p>
                        <small>Par : {{ $commentaire->user->full_name }} | {{ $commentaire->created_at->diffForHumans() }}</small>
                        @if($commentaire->user->isBanned())
                            <span class="badge bg-danger ms-2">Banni</span>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center">Aucun commentaire pour le moment.</p>
        @endif

        <!-- Formulaire d'ajout de commentaire -->
        @if(auth()->check() && !auth()->user()->isBanned())
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-comment"></i> Ajouter un commentaire</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commentaires.store', $publication->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Votre commentaire</label>
                            <textarea name="contenu" class="form-control @error('contenu') is-invalid @enderror" 
                                      rows="4" placeholder="Partagez vos pensées..." required>{{ old('contenu') }}</textarea>
                            @error('contenu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Commenter
                        </button>
                    </form>
                </div>
            </div>
        @elseif(auth()->check() && auth()->user()->isBanned())
            <div class="alert alert-danger text-center">
                <i class="fas fa-ban"></i> Vous êtes banni et ne pouvez pas ajouter de commentaires.
            </div>
        @endif
    </div>
</div>

<style>
.reaction-btn {
    transition: all 0.2s ease;
    padding: 8px 15px;
    border-radius: 25px;
}
.reaction-btn:hover {
    transform: scale(1.05);
}
.reaction-stats strong {
    font-size: 1.2em;
}
.active-like, .active-dislike {
    animation: pulse 0.5s ease-in-out;
}
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Ensure CSRF token is available
    if (!$('meta[name="csrf-token"]').length) {
        $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
    }

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
        
        // Show loading state
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
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
                    // Update counts
                    $('#likes-count').text(data.likes_count);
                    $('#dislikes-count').text(data.dislikes_count);
                    
                    // Toggle button states
                    const $likeBtn = $('.like-form button');
                    const $dislikeBtn = $('.dislike-form button');
                    
                    if (data.user_reaction === 'like') {
                        // Active like, inactive dislike
                        $likeBtn.removeClass('btn-outline-success active-dislike').addClass('btn-success active-like');
                        $likeBtn.html('<i class="fas fa-thumbs-up"></i> Unlike');
                        $likeBtn.prop('title', 'Remove Like');
                        
                        $dislikeBtn.removeClass('btn-danger active-dislike').addClass('btn-outline-danger');
                        $dislikeBtn.html('<i class="fas fa-thumbs-down"></i> Dislike');
                        $dislikeBtn.prop('title', 'Dislike');
                    } else if (data.user_reaction === 'dislike') {
                        // Active dislike, inactive like
                        $dislikeBtn.removeClass('btn-outline-danger active-like').addClass('btn-danger active-dislike');
                        $dislikeBtn.html('<i class="fas fa-thumbs-down"></i> Undo');
                        $dislikeBtn.prop('title', 'Remove Dislike');
                        
                        $likeBtn.removeClass('btn-success active-like').addClass('btn-outline-success');
                        $likeBtn.html('<i class="fas fa-thumbs-up"></i> Like');
                        $likeBtn.prop('title', 'Like');
                    } else {
                        // No reaction - both inactive
                        $likeBtn.removeClass('btn-success active-like').addClass('btn-outline-success');
                        $likeBtn.html('<i class="fas fa-thumbs-up"></i> Like');
                        $likeBtn.prop('title', 'Like');
                        
                        $dislikeBtn.removeClass('btn-danger active-dislike').addClass('btn-outline-danger');
                        $dislikeBtn.html('<i class="fas fa-thumbs-down"></i> Dislike');
                        $dislikeBtn.prop('title', 'Dislike');
                    }
                    
                    // Success notification
                    let message = '';
                    if (isLikeForm) {
                        message = wasActive ? 'Unlike retiré' : '👍 Publication aimée !';
                    } else {
                        message = wasActive ? 'Dislike retiré' : '👎 Publication détestée !';
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
                let errorMessage = 'Une erreur est survenue';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                Swal.fire('Erreur', errorMessage, 'error');
                
                // Revert button state on error
                $btn.attr('class', originalClass).html(originalHtml);
            },
            complete: function() {
                $btn.prop('disabled', false);
            }
        });
    });
});
</script>
@endsection