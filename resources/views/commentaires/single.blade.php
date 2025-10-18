<div class="comment card shadow-sm mb-3 @if($commentaire->isReply()) reply-comment @endif">
    <div class="card-body p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <strong>{{ $commentaire->user->full_name }}</strong>
                <small class="text-muted ms-2">{{ $commentaire->created_at->diffForHumans() }}</small>
                @if($commentaire->isReply())
                    <span class="badge bg-secondary ms-2">Réponse</span>
                @endif
            </div>
            @if(auth()->check() && auth()->id() === $commentaire->user_id)
                <div class="comment-actions">
                    <a href="{{ route('commentaires.edit', $commentaire->id) }}" 
                       class="btn btn-sm btn-outline-primary me-1 edit-comment-btn" 
                       title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form method="POST" action="{{ route('commentaires.destroy', $commentaire->id) }}" 
                          class="d-inline delete-form" data-comment-id="{{ $commentaire->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-outline-danger delete-comment-btn" 
                                data-id="{{ $commentaire->id }}" data-title="ce commentaire" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <div class="comment-content" data-comment-content="{{ $commentaire->id }}">
            <p class="mb-0">{!! nl2br(e($commentaire->contenu)) !!}</p>
        </div>

        <!-- Formulaire d'édition caché -->
        <form method="POST" action="{{ route('commentaires.update', $commentaire->id) }}" 
              class="mt-2" style="display: none;" data-comment-textarea="{{ $commentaire->id }}">
            @csrf
            @method('PUT')
            <textarea name="contenu" class="form-control" rows="3" required>{{ $commentaire->contenu }}</textarea>
            <div class="mt-2">
                <button type="submit" class="btn btn-sm btn-primary" data-save-btn="{{ $commentaire->id }}">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <button type="button" class="btn btn-sm btn-secondary" data-cancel-btn="{{ $commentaire->id }}"
                        onclick="cancelEdit({{ $commentaire->id }})">
                    Annuler
                </button>
            </div>
        </form>

        <!-- Formulaire de réponse -->
        @if(auth()->check())
        <div class="reply-form mt-3 p-3 bg-light rounded" id="reply-{{ $commentaire->id }}" style="display: none;">
            <form method="POST" action="{{ route('commentaires.store', $commentaire->publication_id) }}">
                @csrf
                <input type="hidden" name="parent_id" value="{{ $commentaire->id }}">
                <div class="mb-2">
                    <textarea name="contenu" class="form-control" rows="2" 
                              placeholder="Répondre à {{ $commentaire->user->full_name }}..." required></textarea>
                </div>
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary me-2">Répondre</button>
                    <button type="button" class="btn btn-sm btn-secondary reply-toggle" 
                            data-target="#reply-{{ $commentaire->id }}">Annuler</button>
                </div>
            </form>
        </div>
        @endif

        <!-- Bouton répondre -->
        @if(auth()->check())
            <button class="btn btn-sm btn-link text-primary reply-toggle p-0 mt-2" 
                    data-target="#reply-{{ $commentaire->id }}">
                <i class="fas fa-reply"></i> Répondre
            </button>
        @endif

        <!-- Réponses récursives -->
        @if($commentaire->replies->count() > 0)
            <div class="replies mt-3">
                @foreach($commentaire->replies as $reply)
                    @include('commentaires.single', ['commentaire' => $reply])
                @endforeach
            </div>
        @endif
    </div>
</div>

<!-- Include SweetAlert2 before custom script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle delete confirmation
    document.querySelectorAll('.delete-comment-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.dataset.id;
            const title = this.dataset.title;

            if (typeof Swal === 'undefined') {
                console.error('SweetAlert2 is not loaded');
                alert('Une erreur est survenue. Veuillez réessayer.');
                return;
            }

            Swal.fire({
                title: 'Confirmer la suppression',
                text: `Voulez-vous vraiment supprimer le commentaire "${title}" ?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    console.log('Submitting delete for ID: ' + id); // Debug log
                    const form = document.querySelector(`form.delete-form[data-comment-id="${id}"]`);
                    if (form) {
                        form.submit();
                    } else {
                        console.error('Form not found for ID: ' + id);
                    }
                }
            });
        });
    });

    // Cancel edit functionality
    function cancelEdit(commentId) {
        const content = document.querySelector(`[data-comment-content="${commentId}"]`);
        const textarea = document.querySelector(`[data-comment-textarea="${commentId}"]`);
        const editBtn = document.querySelector(`.edit-comment-btn[href="${route('commentaires.edit', commentId)}"]`);

        content.style.display = 'block';
        textarea.style.display = 'none';
        if (editBtn) editBtn.style.display = 'inline-block';

        document.querySelector(`[data-save-btn="${commentId}"]`).style.display = 'none';
        document.querySelector(`[data-cancel-btn="${commentId}"]`).style.display = 'none';
    }

    // Toggle reply form
    document.querySelectorAll('.reply-toggle').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const form = document.querySelector(targetId);
            if (form) {
                form.style.display = form.style.display === 'none' ? 'block' : 'none';
            }
        });
    });
});
</script>

<style>
.comment {
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