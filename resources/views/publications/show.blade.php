@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s">
            <h1 class="display-6">{{ $publication->titre }}</h1>
        </div>

        <!-- Messages flash -->
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

        <!-- Contenu de la publication -->
        <div class="card mb-5 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h2 class="card-title text-primary">{{ $publication->titre }}</h2>
                        <p class="text-muted">Par {{ $publication->user->full_name }} 
                            | {{ $publication->created_at->diffForHumans() }}</p>
                        <span class="badge bg-success">{{ ucfirst($publication->categorie) }}</span>
                        @if($publication->user->isBanned())
                            <span class="badge bg-danger ms-2">Banni</span>
                        @endif
                    </div>
                    @if(auth()->check() && auth()->id() === $publication->user_id && !auth()->user()->isBanned())
                        <div>
                            <a href="{{ route('publications.edit', $publication->id) }}" 
                               class="btn btn-sm btn-outline-primary me-2">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="mb-4">
                    {!! nl2br(e($publication->contenu)) !!}
                </div>

                @if($publication->image)
                    <img src="{{ asset('storage/' . $publication->image) }}" 
                         alt="{{ $publication->titre }}" 
                         class="img-fluid rounded shadow-sm" 
                         style="max-height: 400px; object-fit: cover;">
                @endif
            </div>
        </div>

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
                Vous êtes banni et ne pouvez pas ajouter de commentaires.
            </div>
        @endif

        <!-- Liste des commentaires -->
        <div class="comments-section">
            <h3 class="mb-4">
                <i class="fas fa-comments"></i> 
                {{ $publication->commentaires()->count() }} commentaire{{ $publication->commentaires()->count() > 1 ? 's' : '' }}
            </h3>

            @if($publication->commentaires->isNotEmpty())
                @foreach($publication->commentaires()->with('replies.user')->whereNull('parent_id')->latest()->get() as $commentaire)
                    @include('commentaires.single', ['commentaire' => $commentaire])
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun commentaire pour le moment. Soyez le premier à commenter !</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Scripts -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Confirmation de suppression
    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            
            const form = this.closest('form');
            const title = this.dataset.title;
            
            Swal.fire({
                title: 'Confirmer la suppression',
                text: `Voulez-vous vraiment supprimer ce commentaire ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Edition inline (optionnel)
    document.querySelectorAll('.edit-comment-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const commentId = this.dataset.commentId;
            const content = document.querySelector(`[data-comment-content="${commentId}"]`);
            const textarea = document.querySelector(`[data-comment-textarea="${commentId}"]`);
            
            content.style.display = 'none';
            textarea.style.display = 'block';
            this.style.display = 'none';
            
            const saveBtn = document.querySelector(`[data-save-btn="${commentId}"]`);
            const cancelBtn = document.querySelector(`[data-cancel-btn="${commentId}"]`);
            saveBtn.style.display = 'inline-block';
            cancelBtn.style.display = 'inline-block';
        });
    });
});
</script>
@endpush

<style>
.comments-section .comment {
    border-left: 4px solid #007bff;
    margin-bottom: 1rem;
    padding-left: 1rem;
}
.reply-comment {
    border-left: 4px solid #6c757d;
    margin-left: 2rem;
    padding-left: 1rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}
.comment-actions {
    opacity: 0;
    transition: opacity 0.3s ease;
}
.comment:hover .comment-actions {
    opacity: 1;
}
</style>
@endsection