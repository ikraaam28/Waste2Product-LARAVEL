@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-success">My Publications</p>
            <h1 class="display-6">Manage and explore your recycling ideas</h1>
        </div>

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Create Form --}}
        @if(auth()->check())
        <div class="card mb-4 shadow-sm border-0" style="background: #f8fff9; border: 1px solid #e8f5e8; border-radius: 12px;">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 fw-semibold text-success text-center">📝 Create New Publication</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('publications.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3">
<div class="col-md-6">
    <div class="form-floating">
        <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror" id="titre" value="{{ old('titre') }}" required>
        <label for="titre">Title</label>
        @error('titre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <button type="button" id="generate-title-btn" class="btn btn-outline-success mt-2">Generate Title with AI</button>
</div>
                        <div class="col-md-6">
                            <select name="categorie" class="form-select @error('categorie') is-invalid @enderror" required>
                                <option value="">Select category</option>
                                <option value="reemployment" {{ old('categorie') == 'reemployment' ? 'selected' : '' }}>♻️ Reemployment</option>
                                <option value="repair" {{ old('categorie') == 'repair' ? 'selected' : '' }}>🔧 Repair</option>
                                <option value="transformation" {{ old('categorie') == 'transformation' ? 'selected' : '' }}>⚙️ Transformation</option>
                            </select>
                            @error('categorie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea name="contenu" class="form-control @error('contenu') is-invalid @enderror" id="contenu" rows="4" required>{{ old('contenu') }}</textarea>
                                <label for="contenu">Content</label>
                                @error('contenu')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Upload Image (Optional)</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="file-preview d-none mt-2">
                                <img id="preview-img" class="img-thumbnail" style="max-height: 80px;">
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success px-4 py-2">Publish</button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        {{-- Filter Tabs --}}
        <div class="card mb-4 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <ul class="nav nav-tabs" id="publicationFilter" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link @if(!request('filter') || request('filter') == 'mine') active @endif filter-tab" 
                                        data-filter="mine" type="button">
                                    <i class="fas fa-user-circle text-success me-1"></i>
                                    My Publications
                                    @if(isset($myPublications) && method_exists($myPublications, 'total') && $myPublications->total() > 0)
                                        <span class="badge bg-success ms-1">{{ $myPublications->total() }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link @if(request('filter') == 'others') active @endif filter-tab" 
                                        data-filter="others" type="button">
                                    <i class="fas fa-users text-success me-1"></i>
                                    From Others
                                    @if(isset($otherPublications) && method_exists($otherPublications, 'total') && $otherPublications->total() > 0)
                                        <span class="badge bg-success ms-1">{{ $otherPublications->total() }}</span>
                                    @endif
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <form method="GET" class="d-flex justify-content-end">
                            <input type="hidden" name="filter" value="{{ request('filter', 'mine') }}">
                            <select name="date_filter" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                                <option value="recent" {{ request('date_filter', 'recent') == 'recent' ? 'selected' : '' }}>Recent</option>
                                <option value="oldest" {{ request('date_filter') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            
            <div class="card-body p-0">
                <div class="tab-content" id="publicationTabContent">
                    <!-- My Publications Tab -->
                    <div class="tab-pane fade @if(request('filter') != 'others') show active @endif" id="mine" role="tabpanel">
                        @if($myPublications->isNotEmpty())
                            <div class="row g-4 pt-3" id="my-publications-grid">
                                @foreach($myPublications as $publication)
                                <div class="col-12 col-md-4">
                                    <div class="publication-item h-100 p-4 bg-white rounded-2 shadow-sm border position-relative">
                                        <h4 class="fw-semibold text-dark mb-2">{{ $publication->titre }}</h4>
                                        <p class="mb-3 text-muted small">{{ \Illuminate\Support\Str::limit($publication->contenu, 100) }}</p>
                                        @if($publication->image)
                                            <img class="img-fluid rounded mb-3 w-100" src="{{ asset('storage/' . $publication->image) }}" 
                                                 alt="{{ $publication->titre }}" style="height: 150px; object-fit: cover;">
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="text-start">
                                                <h6 class="fw-semibold mb-1">{{ $publication->user->full_name }}</h6>
                                                <span class="badge bg-success-subtle text-success rounded-pill">{{ ucfirst($publication->categorie) }}</span>
                                            </div>
                                            <small class="text-muted">{{ $publication->created_at->diffForHumans() }}</small>
                                        </div>
                                        
                                        <div class="reaction-counts mt-2 mb-3 d-flex justify-content-center gap-2">
                                            <small class="text-success">
                                                <i class="fas fa-thumbs-up me-1"></i>{{ $publication->likes_count }}
                                            </small>
                                            <small class="text-danger">
                                                <i class="fas fa-thumbs-down me-1"></i>{{ $publication->dislikes_count }}
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2 mt-3 action-icons justify-content-center">
                                            <a href="{{ route('publications.show', $publication->id) }}" class="action-icon minimal-action bg-light" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('publications.edit', $publication->id) }}" class="action-icon minimal-action bg-light" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="action-icon minimal-action bg-danger text-white delete-btn" 
                                                    data-id="{{ $publication->id }}" data-title="{{ $publication->titre }}" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
@if(method_exists($myPublications, 'appends'))
    <div class="text-center mt-4">
        {{ $myPublications->appends(request()->query())->links() }}
    </div>
@endif


                     @if(method_exists($myPublications, 'currentPage'))
    <div class="text-center mt-2">
        <small class="text-muted">
            Page {{ $myPublications->currentPage() }} of {{ $myPublications->lastPage() }}
            @if(request('date_filter') == 'recent')
                (most recent first)
            @elseif(request('date_filter') == 'oldest')
                (oldest first)
            @endif
        </small>
    </div>
@endif

                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">You have no publications. Create your first one above!</p>
                            </div>
                        @endif
                    </div>

                    <!-- Others Publications Tab -->
                    <div class="tab-pane fade @if(request('filter') == 'others') show active @endif" id="others" role="tabpanel">
                        @if($otherPublications->isNotEmpty())
                            <div class="row g-4 pt-3" id="others-publications-grid">
                                @foreach($otherPublications as $publication)
                                <div class="col-12 col-md-4">
                                    <div class="publication-item h-100 p-4 bg-white rounded-2 shadow-sm border position-relative" data-publication-id="{{ $publication->id }}">
                                        <h4 class="fw-semibold text-dark mb-2">{{ $publication->titre }}</h4>
                                        <p class="mb-3 text-muted small">{{ \Illuminate\Support\Str::limit($publication->contenu, 100) }}</p>
                                        @if($publication->image)
                                            <img class="img-fluid rounded mb-3 w-100" src="{{ asset('storage/' . $publication->image) }}" 
                                                 alt="{{ $publication->titre }}" style="height: 150px; object-fit: cover;">
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="text-start">
                                                <h6 class="fw-semibold mb-1">{{ $publication->user->full_name }}</h6>
                                                <span class="badge bg-success-subtle text-success rounded-pill">{{ ucfirst($publication->categorie) }}</span>
                                            </div>
                                            <small class="text-muted">{{ $publication->created_at->diffForHumans() }}</small>
                                        </div>
                                        
                                        <div class="reaction-counts mt-2 mb-3 d-flex justify-content-center gap-2">
                                            <small class="text-success likes-count-{{ $publication->id }}">
                                                <i class="fas fa-thumbs-up me-1"></i>{{ $publication->likes_count }}
                                            </small>
                                            <small class="text-danger dislikes-count-{{ $publication->id }}">
                                                <i class="fas fa-thumbs-down me-1"></i>{{ $publication->dislikes_count }}
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2 mt-3 action-icons justify-content-center">
                                            <a href="{{ route('publications.show', $publication->id) }}" class="action-icon minimal-action bg-light" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(auth()->check() && auth()->id() !== $publication->user_id)
                                            <div class="reaction-buttons d-flex gap-1">
                                                <form method="POST" action="{{ route('publications.like', $publication) }}" 
                                                      class="d-inline like-form reaction-form" data-publication-id="{{ $publication->id }}">
                                                    @csrf
                                                    @if($publication->isLikedByAuthUser())
                                                        <button type="submit" class="btn minimal-reaction-btn btn-success" title="Remove Like">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn minimal-reaction-btn btn-outline-success" title="Like">
                                                            <i class="fas fa-thumbs-up"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                                <form method="POST" action="{{ route('publications.dislike', $publication) }}" 
                                                      class="d-inline dislike-form reaction-form" data-publication-id="{{ $publication->id }}">
                                                    @csrf
                                                    @if($publication->isDislikedByAuthUser())
                                                        <button type="submit" class="btn minimal-reaction-btn btn-danger" title="Remove Dislike">
                                                            <i class="fas fa-thumbs-down"></i>
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn minimal-reaction-btn btn-outline-danger" title="Dislike">
                                                            <i class="fas fa-thumbs-down"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="text-center mt-4">
                                {{ $otherPublications->appends(request()->query())->links() }}
                            </div>
                            <div class="text-center mt-2">
                                <small class="text-muted">
                                    Page {{ $otherPublications->currentPage() }} of {{ $otherPublications->lastPage() }}
                                    @if(request('date_filter') == 'recent')
                                        (most recent first)
                                    @elseif(request('date_filter') == 'oldest')
                                        (oldest first)
                                    @endif
                                </small>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-0">No publications from other users at the moment.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
/* Filter Tabs Styles */
.filter-tabs .nav-link {
    border: none;
    color: #6c757d;
    font-weight: 500;
    padding: 12px 24px;
    border-radius: 10px 10px 0 0;
    transition: all 0.3s ease;
    position: relative;
}
.filter-tabs .nav-link.active {
    color: #28a745;
    background: #f8fff9;
    border-bottom: 2px solid #28a745;
}
.filter-tabs .nav-link:hover {
    color: #28a745;
    background: #f8f9fa;
}

/* Date Filter Styles */
.date-filter-select {
    border: 1px solid #e8f5e8;
    border-radius: 8px;
    background: white;
    transition: all 0.2s ease;
}
.date-filter-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
}

/* Publication Grid */
.publication-item {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    overflow: hidden;
}
.publication-item:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transform: translateY(-5px);
    border-color: #28a745;
}

.minimal-form-card {
    transition: box-shadow 0.3s ease;
}
.minimal-form-card:hover {
    box-shadow: 0 5px 20px rgba(40, 167, 69, 0.1);
}

.minimal-input, .minimal-textarea, .minimal-file {
    border: 1px solid #e8f5e8;
    border-radius: 8px;
    padding: 10px 12px;
    transition: all 0.2s ease;
    background: #fff;
}
.minimal-input:focus, .minimal-textarea:focus, .minimal-file:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.15);
    outline: none;
}

.minimal-btn {
    border-radius: 8px;
    background: #28a745;
    border: none;
    transition: all 0.2s ease;
}
.minimal-btn:hover {
    background: #218838;
    transform: translateY(-1px);
    box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
}

.minimal-action {
    width: 40px !important;
    height: 40px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease;
    border: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 14px !important;
}
.minimal-action:hover {
    transform: scale(1.05);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.minimal-reaction-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    padding: 0;
    font-size: 12px;
    transition: all 0.2s ease;
}
.minimal-reaction-btn:hover {
    transform: scale(1.05);
}

.action-icons {
    gap: 8px !important;
}

.bg-success-subtle {
    background-color: rgba(40, 167, 69, 0.1) !important;
}

/* Grid Responsiveness */
@media (max-width: 768px) {
    .filter-tabs .nav-link {
        padding: 10px 16px;
        font-size: 0.9em;
    }
    .action-icons {
        gap: 6px !important;
    }
    .minimal-reaction-btn {
        width: 28px;
        height: 28px;
    }
}
</style>

<script>
$(document).ready(function() {
    // Filter Tab URL Update

    // Generate Title Button
// Generate Title Button - FIXED VERSION
$('#generate-title-btn').on('click', function() {
    const content = $('#contenu').val().trim();
    console.log('Content being sent:', content); // DEBUG - check browser console
    
    if (!content || content.length < 10) {
        Swal.fire('Error', 'Please enter at least 10 characters of content!', 'error');
        return;
    }

    const $btn = $(this);
    $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> AI Thinking...');

    $.ajax({
        url: '{{ route("publications.generateTitle") }}',
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            content: content  // This was the problem!
        },
        success: function(data) {
            console.log('Success:', data); // DEBUG
            if (data.title) {
                $('#titre').val(data.title);
                Swal.fire({
                    icon: 'success',
                    title: '🎉 Title Generated!',
                    text: data.title,
                    timer: 2000
                });
            } else {
                Swal.fire('Error', 'No title returned. Try again.', 'error');
            }
        },
        error: function(xhr) {
            console.log('Error details:', xhr.responseJSON); // DEBUG
            let error = xhr.responseJSON?.error || 'Unknown error';
            Swal.fire('❌ Error', error, 'error');
        },
        complete: function() {
            $btn.prop('disabled', false).html('✨ Generate Title with AI');
        }
    });
});
// Filter tabs
$('.filter-tab').click(function(e) {
    e.preventDefault();
    const filter = $(this).data('filter');
    const url = new URL(window.location.href);
    const dateFilter = $('select[name="date_filter"]').val();
    url.searchParams.set('filter', filter);
    url.searchParams.set('date_filter', dateFilter);
    window.location.href = url.toString();
});


    // Image preview
    $(document).on('change', 'input[type="file"]', function() {
        const file = this.files[0];
        const $preview = $('.file-preview');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $preview.removeClass('d-none').find('#preview-img').attr('src', e.target.result);
            };
            reader.readAsDataURL(file);
        } else {
            $preview.addClass('d-none');
        }
    });

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

    // AJAX Reaction Handling
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