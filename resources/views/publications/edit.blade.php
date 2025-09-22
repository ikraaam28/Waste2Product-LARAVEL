@extends('layouts.app')

@section('content')
<div class="container-fluid publication py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <h1 class="display-6">Edit Publication</h1>
        </div>

        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5>Edit: {{ $publication->titre }}</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('publications.update', $publication->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="titre" class="form-control" value="{{ old('titre', $publication->titre) }}" required>
                    </div>
                    <div class="form-group">
                        <label>Content</label>
                        <textarea name="contenu" class="form-control" required>{{ old('contenu', $publication->contenu) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label>Category</label>
<select name="categorie" class="form-control" required>
    <option value="reemployment" {{ old('categorie', $publication->categorie) == 'reemployment' ? 'selected' : '' }}>Reemployment</option>
    <option value="repair" {{ old('categorie', $publication->categorie) == 'repair' ? 'selected' : '' }}>Repair</option>
    <option value="transformation" {{ old('categorie', $publication->categorie) == 'transformation' ? 'selected' : '' }}>Transformation</option>
</select>

                    </div>
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control">
                        @if($publication->image)
                            <img src="{{ asset('storage/' . $publication->image) }}" alt="{{ $publication->titre }}" class="img-fluid mt-2" style="max-height: 150px;">
                            <p class="text-muted">Leave empty to keep the current image.</p>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-primary mt-2">Update</button>
                    <a href="{{ route('publications.my') }}" class="btn btn-secondary mt-2">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
