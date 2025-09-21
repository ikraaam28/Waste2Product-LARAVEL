@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-6">{{ $publication->titre }}</h1>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <p>{{ $publication->contenu }}</p>
                @if($publication->image)
                    <img src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->titre }}" class="img-fluid mb-3" style="max-height: 300px;">
                @endif
                <p><strong>Par :</strong> {{ $publication->user->full_name }} | <strong>Catégorie :</strong> {{ ucfirst($publication->categorie) }}</p>
            </div>
        </div>

        <h2>Commentaires</h2>
        @if($publication->commentaires->isNotEmpty())
            @foreach($publication->commentaires as $commentaire)
                <div class="card mb-2">
                    <div class="card-body">
                        <p>{{ $commentaire->contenu }}</p>
                        <small>Par : {{ $commentaire->user->full_name }} | {{ $commentaire->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            @endforeach
        @else
            <p class="text-center">Aucun commentaire pour le moment.</p>
        @endif

        @if(auth()->check())
            <div class="card mt-4">
                <div class="card-header bg-primary text-white">
                    <h5>Ajouter un commentaire</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commentaires.store', $publication->id) }}">
                        @csrf
                        <div class="form-group">
                            <textarea name="contenu" class="form-control" required placeholder="Ajoutez un commentaire..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">Commenter</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection