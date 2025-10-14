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
        @if(auth()->check() && !auth()->user()->isBanned())
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
        @elseif(auth()->check() && auth()->user()->isBanned())
            <div class="alert alert-danger text-center">
                Vous êtes banni et ne pouvez pas créer de publication.
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
                    <div class="d-flex gap-3 mt-3 action-icons">
                        <a href="{{ route('publications.show', $publication->id) }}" class="action-icon">
                            <span class="material-icons">visibility</span>
                        </a>
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
</style>

<script>
    $(document).ready(function() {
        // Owl Carousel
        var $myCarousel = $('.mes-publications-carousel');
        var myItemCount = $myCarousel.find('.publication-item').length;
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

        var $otherCarousel = $('.other-publications-carousel');
        var otherItemCount = $otherCarousel.find('.publication-item').length;
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
                    form.append('@csrf');
                    form.append('@method("DELETE")');
                    $('body').append(form);
                    form.submit();
                }
            });
        });
    });
</script>
@endsection