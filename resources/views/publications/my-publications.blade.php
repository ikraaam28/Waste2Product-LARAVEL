@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">My Publications</p>
            <h1 class="display-6">Manage and explore your recycling ideas</h1>
        </div>

        <!-- Messages flash -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <!-- Form to create a new publication -->
        @if(auth()->check())
        <div class="card mb-4 shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="mb-0">Add a New Publication</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('publications.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label fw-medium">Title</label>
                        <input type="text" name="titre" class="form-control rounded-pill py-2" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-medium">Content</label>
                        <textarea name="contenu" class="form-control rounded-3" rows="5" required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-medium">Category</label>
                        <select name="categorie" class="form-control rounded-pill py-2" required>
                            <option value="reemployment">Reemployment</option>
                            <option value="repair">Repair</option>
                            <option value="transformation">Transformation</option>
                        </select>
                    </div>
                    <div class="form-group mb-4">
                        <label class="form-label fw-medium">Image</label>
                        <input type="file" name="image" class="form-control rounded-pill">
                    </div>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2">Publish</button>
                </form>
            </div>
        </div>
        @endif

        <!-- Your publications -->
        @if($myPublications->isNotEmpty())
        <div class="mb-5">
            <h2 class="mb-4">My Publications</h2>
            <div class="owl-carousel publication-carousel mes-publications-carousel wow fadeInUp" data-wow-delay="0.3s">
                @foreach($myPublications as $publication)
                <div class="publication-item p-4 p-lg-5">
                    <h4 class="text-primary">{{ $publication->titre }}</h4>
                    <p class="mb-4">{{ \Illuminate\Support\Str::limit($publication->contenu, 100) }}</p>
                    @if($publication->image)
                        <img class="img-fluid flex-shrink-0 mb-3" src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->titre }}" style="max-height: 150px;">
                    @endif
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="text-start ms-3">
                            <h5>{{ $publication->user->full_name }}</h5>
                            <span class="text-primary">{{ ucfirst($publication->categorie) }}</span>
                        </div>
                    </div>
                    
                    <!-- Reaction Counts for My Publications -->
                    <div class="reaction-counts mt-2 mb-3 d-flex justify-content-center gap-3">
                        <small class="text-success">
                            <i class="fas fa-thumbs-up me-1"></i>
                            {{ $publication->likes_count }} likes
                        </small>
                        <small class="text-danger">
                            <i class="fas fa-thumbs-down me-1"></i>
                            {{ $publication->dislikes_count }} dislikes
                        </small>
                    </div>
                    
                    <div class="d-flex gap-3 mt-3 action-icons">
                        <a href="{{ route('publications.show', $publication->id) }}" class="action-icon">
                            <span class="material-icons">visibility</span>
                        </a>
                        @if(auth()->check() && auth()->id() === $publication->user_id)
                            <a href="{{ route('publications.edit', $publication->id) }}" class="action-icon">
                                <span class="material-icons">edit</span>
                            </a>
                            <button type="button" class="action-icon delete-btn" 
                                    data-id="{{ $publication->id }}" 
                                    data-title="{{ $publication->titre }}">
                                <span class="material-icons">delete</span>
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <p class="text-center mb-5">You have no publications at the moment.</p>
        @endif

        <!-- Publications from others -->
        @if($otherPublications->isNotEmpty())
        <div>
            <h2 class="mb-4">Publications from Others</h2>
            <div class="owl-carousel publication-carousel other-publications-carousel wow fadeInUp" data-wow-delay="0.5s">
                @foreach($otherPublications as $publication)
                <div class="publication-item p-4 p-lg-5" data-publication-id="{{ $publication->id }}">
                    <h4 class="text-primary">{{ $publication->titre }}</h4>
                    <p class="mb-4">{{ \Illuminate\Support\Str::limit($publication->contenu, 100) }}</p>
                    @if($publication->image)
                        <img class="img-fluid flex-shrink-0 mb-3" src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->titre }}" style="max-height: 150px;">
                    @endif
                    <div class="d-flex align-items-center justify-content-center">
                        <div class="text-start ms-3">
                            <h5>{{ $publication->user->full_name }}</h5>
                            <span class="text-primary">{{ ucfirst($publication->categorie) }}</span>
                        </div>
                    </div>
                    
                    <!-- Reaction Counts for Others' Publications -->
                    <div class="reaction-counts mt-2 mb-3 d-flex justify-content-center gap-3">
                        <small class="text-success likes-count-{{ $publication->id }}">
                            <i class="fas fa-thumbs-up me-1"></i>
                            {{ $publication->likes_count }} likes
                        </small>
                        <small class="text-danger dislikes-count-{{ $publication->id }}">
                            <i class="fas fa-thumbs-down me-1"></i>
                            {{ $publication->dislikes_count }} dislikes
                        </small>
                    </div>
                    
                    <div class="d-flex gap-3 mt-3 action-icons">
                        <a href="{{ route('publications.show', $publication->id) }}" class="action-icon">
                            <span class="material-icons">visibility</span>
                        </a>
                        
                        <!-- Reaction Buttons (Only for other users' publications) -->
                        @if(auth()->check() && auth()->id() !== $publication->user_id)
                        <div class="reaction-buttons d-flex gap-2 ms-2">
                            {{-- Like Toggle Button --}}
                            <form method="POST" action="{{ route('publications.like', $publication) }}" 
                                  class="d-inline like-form reaction-form" 
                                  data-publication-id="{{ $publication->id }}">
                                @csrf
                                @if($publication->isLikedByAuthUser())
                                    <button type="submit" class="btn btn-sm btn-success reaction-btn active-like" title="Remove Like">
                                        <i class="fas fa-thumbs-up"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-success reaction-btn" title="Like">
                                        <i class="fas fa-thumbs-up"></i>
                                    </button>
                                @endif
                            </form>
                            
                            {{-- Dislike Toggle Button --}}
                            <form method="POST" action="{{ route('publications.dislike', $publication) }}" 
                                  class="d-inline dislike-form reaction-form"
                                  data-publication-id="{{ $publication->id }}">
                                @csrf
                                @if($publication->isDislikedByAuthUser())
                                    <button type="submit" class="btn btn-sm btn-danger reaction-btn active-dislike" title="Remove Dislike">
                                        <i class="fas fa-thumbs-down"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-outline-danger reaction-btn" title="Dislike">
                                        <i class="fas fa-thumbs-down"></i>
                                    </button>
                                @endif
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
            <div class="text-center mt-4 wow fadeInUp" data-wow-delay="0.1s">
                {{ $otherPublications->links() }}
            </div>
        </div>
        @else
        <p class="text-center">No publications from other users at the moment.</p>
        @endif
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    .action-icons {
        justify-content: center;
    }
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #f0f0f0;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        font-size: 24px;
    }
    .action-icon:hover {
        background: #e0e0e0;
        transform: scale(1.1);
        color: #000;
    }
    .delete-btn {
        cursor: pointer;
        border: none;
        padding: 0;
    }
    .delete-btn:hover {
        background: #e0e0e0;
        transform: scale(1.1);
        color: #d32f2f;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.3);
    }
    .form-label {
        color: #333;
        font-size: 0.9rem;
    }
    .btn-primary {
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #0056b3;
    }

    /* Reaction Styles */
    .reaction-counts {
        font-size: 0.9em;
        opacity: 0.8;
    }
    .reaction-btn {
        width: 35px;
        height: 35px;
        padding: 0;
        border-radius: 50%;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .reaction-btn:hover {
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    .reaction-btn i {
        font-size: 14px;
    }
    .btn-outline-success:hover {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }
    .btn-outline-danger:hover {
        background-color: #dc3545 !important;
        border-color: #dc3545 !important;
        color: white !important;
    }
    .reaction-buttons {
        gap: 5px;
    }
    
    /* Active state styles */
    .active-like, .active-dislike {
        animation: pulse 0.5s ease-in-out;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .reaction-buttons {
            gap: 3px;
        }
        .reaction-btn {
            width: 30px;
            height: 30px;
        }
        .reaction-btn i {
            font-size: 12px;
        }
    }
</style>

<script>
$(document).ready(function() {
    // Ensure CSRF token is available
    if (!$('meta[name="csrf-token"]').length) {
        $('head').append('<meta name="csrf-token" content="{{ csrf_token() }}">');
    }

    // Owl Carousel for My Publications
    var $myCarousel = $('.mes-publications-carousel');
    var myItemCount = $myCarousel.find('.publication-item').length;
    if (myItemCount > 0) {
        $myCarousel.owlCarousel({
            items: 1,
            loop: (myItemCount > 3),
            margin: 10,
            nav: true,
            autoplay: true,
            autoplayTimeout: 5000,
            responsive: {
                0: { items: 1, loop: (myItemCount > 1) },
                600: { items: 2, loop: (myItemCount > 2) },
                1000: { items: 3, loop: (myItemCount > 3) }
            }
        });
    }

    // Owl Carousel for Other Publications
    var $otherCarousel = $('.other-publications-carousel');
    var otherItemCount = $otherCarousel.find('.publication-item').length;
    if (otherItemCount > 0) {
        $otherCarousel.owlCarousel({
            items: 1,
            loop: (otherItemCount > 3),
            margin: 10,
            nav: true,
            autoplay: true,
            autoplayTimeout: 5000,
            responsive: {
                0: { items: 1, loop: (otherItemCount > 1) },
                600: { items: 2, loop: (otherItemCount > 2) },
                1000: { items: 3, loop: (otherItemCount > 3) }
            }
        });
    }

    // SweetAlert2 for deletion
    $('.delete-btn').on('click', function(e) {
        e.preventDefault();
        var id = $(this).data('id');
        var title = $(this).data('title');

        Swal.fire({
            title: 'Are you sure?',
            text: 'You are about to delete the publication "' + title + '"',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                var form = $('<form>', {
                    'method': 'POST',
                    'action': '{{ route("publications.destroy", ":id") }}'.replace(':id', id)
                });
                form.append($('<input>', { 
                    type: 'hidden', 
                    name: '_token', 
                    value: $('meta[name="csrf-token"]').attr('content') 
                }));
                form.append($('<input>', { 
                    type: 'hidden', 
                    name: '_method', 
                    value: 'DELETE' 
                }));
                $('body').append(form);
                form.submit();
            }
        });
    });

    // AJAX Reaction Handling - Simplified Toggle Logic
    $(document).on('submit', '.reaction-form', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const url = $form.attr('action');
        const publicationId = $form.data('publication-id');
        const $publicationItem = $('[data-publication-id="' + publicationId + '"]');
        const $likesCount = $publicationItem.find('.likes-count-' + publicationId);
        const $dislikesCount = $publicationItem.find('.dislikes-count-' + publicationId);
        const $likeButton = $publicationItem.find('.like-form button');
        const $dislikeButton = $publicationItem.find('.dislike-form button');
        const isLikeForm = $form.hasClass('like-form');
        
        // Show loading state
        const $btn = $form.find('button');
        const originalHtml = $btn.html();
        const originalClass = $btn.attr('class');
        const wasActive = $btn.hasClass('active-like') || $btn.hasClass('active-dislike');
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
                    $likesCount.html('<i class="fas fa-thumbs-up me-1"></i>' + data.likes_count + ' likes');
                    $dislikesCount.html('<i class="fas fa-thumbs-down me-1"></i>' + data.dislikes_count + ' dislikes');
                    
                    // Toggle button states
                    if (data.user_reaction === 'like') {
                        // Active like, inactive dislike
                        $likeButton.removeClass('btn-outline-success active-dislike').addClass('btn-success active-like');
                        $likeButton.html('<i class="fas fa-thumbs-up"></i>');
                        $likeButton.prop('title', 'Remove Like');
                        
                        $dislikeButton.removeClass('btn-danger active-dislike').addClass('btn-outline-danger');
                        $dislikeButton.html('<i class="fas fa-thumbs-down"></i>');
                        $dislikeButton.prop('title', 'Dislike');
                    } else if (data.user_reaction === 'dislike') {
                        // Active dislike, inactive like
                        $dislikeButton.removeClass('btn-outline-danger active-like').addClass('btn-danger active-dislike');
                        $dislikeButton.html('<i class="fas fa-thumbs-down"></i>');
                        $dislikeButton.prop('title', 'Remove Dislike');
                        
                        $likeButton.removeClass('btn-success active-like').addClass('btn-outline-success');
                        $likeButton.html('<i class="fas fa-thumbs-up"></i>');
                        $likeButton.prop('title', 'Like');
                    } else {
                        // No reaction - both inactive
                        $likeButton.removeClass('btn-success active-like').addClass('btn-outline-success');
                        $likeButton.html('<i class="fas fa-thumbs-up"></i>');
                        $likeButton.prop('title', 'Like');
                        
                        $dislikeButton.removeClass('btn-danger active-dislike').addClass('btn-outline-danger');
                        $dislikeButton.html('<i class="fas fa-thumbs-down"></i>');
                        $dislikeButton.prop('title', 'Dislike');
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
            },
            complete: function() {
                // Reset button
                $btn.prop('disabled', false).attr('class', originalClass).html(originalHtml);
            }
        });
    });
});
</script>
@endsection