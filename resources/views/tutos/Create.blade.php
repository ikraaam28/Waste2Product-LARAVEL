@extends('layouts.app')

@section('content')
<div class="container-fluid py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Créer un Tutoriel</p>
            <h1 class="display-6">Partagez votre idée de recyclage</h1>
        </div>
        <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.3s">
            <div class="col-lg-8">
                <form action="{{ route('tutos.store') }}" method="POST" class="p-4 bg-white shadow-sm rounded" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label for="title" class="form-label">Titre</label>
                        <input type="text" name="title" id="title" required class="form-control" placeholder="Entrez le titre du tuto">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" required class="form-control" rows="5" placeholder="Décrivez votre tuto"></textarea>
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="category" class="form-label">Catégorie</label>
                        <select name="category" id="category" required class="form-select">
                            <option value="" disabled selected>Choisissez une catégorie</option>
                            <option value="plastique">Plastique</option>
                            <option value="bois">Bois</option>
                            <option value="papier">Papier</option>
                            <option value="metal">Métal</option>
                            <option value="verre">Verre</option>
                            <option value="autre">Autre</option>
                        </select>
                        @error('category')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Étapes</label>
                        <div id="steps-container">
                            <div class="input-group mb-2 step-group">
                                <input type="text" name="steps[]" class="form-control" placeholder="Étape 1" required>
                                <button type="button" class="btn btn-danger remove-step">Supprimer</button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-primary rounded-pill mt-2" id="add-step">Ajouter une étape</button>
                        @error('steps.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label for="media" class="form-label">Médias (images ou vidéos)</label>
                        <input type="file" name="media[]" id="media" class="form-control" multiple accept="image/*,video/mp4">
                        <small class="form-text text-muted">Sélectionnez des images ou vidéos (MP4). Taille maximale : 10 Mo par fichier.</small>
                        @error('media.*')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary rounded-pill py-3 px-5">Créer le tuto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('add-step').addEventListener('click', function() {
    const container = document.getElementById('steps-container');
    const stepCount = container.children.length + 1;
    const newStep = document.createElement('div');
    newStep.className = 'input-group mb-2 step-group';
    newStep.innerHTML = `
        <input type="text" name="steps[]" class="form-control" placeholder="Étape ${stepCount}" required>
        <button type="button" class="btn btn-danger remove-step">Supprimer</button>
    `;
    container.appendChild(newStep);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-step')) {
        const container = document.getElementById('steps-container');
        if (container.children.length > 1) {
            e.target.parentElement.remove();
        }
    }
});
</script>
@endsection