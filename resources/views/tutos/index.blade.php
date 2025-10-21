@extends('layouts.app')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Our Tutorials</p>
            <h1 class="display-6">Discover Our Recycling Ideas</h1>
        </div>

        <!-- Filter Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="{{ route('tutos.index') }}">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="category_id" class="form-label">Filter by Category</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label for="media_type" class="form-label">Filter by Media Type</label>
                            <select name="media_type" id="media_type" class="form-select">
                                <option value="">All Media Types</option>
                                <option value="photo" {{ request('media_type') == 'photo' ? 'selected' : '' }}>Photo</option>
                                <option value="video" {{ request('media_type') == 'video' ? 'selected' : '' }}>Video</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @auth
            <!-- Add any auth-specific content here -->
        @endauth

        @if ($tutos->isEmpty())
            <p class="text-center">No tutorials available at the moment.</p>
        @else
            <div class="row g-4">
                @foreach ($tutos as $tuto)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="card position-relative text-center" style="border-radius: 12px; overflow: hidden; background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(8px); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); min-height: 350px;">
                            @php
                                $mediaUrl = null;
                                $isVideo = false;
                                $mimeType = '';
                                if ($tuto->media && is_array($tuto->media) && isset($tuto->media[0])) {
                                    $firstMedia = $tuto->media[0];
                                    $mediaUrl = asset('storage/' . $firstMedia['path']);
                                    $mimeType = $firstMedia['mime_type'];
                                    if (strpos($firstMedia['mime_type'], 'video') === 0) {
                                        $isVideo = true;
                                    }
                                }
                            @endphp
                            @if ($mediaUrl)
                                @if ($isVideo)
                                    <video class="card-img-top" style="width: 100%; height: auto; max-height: 200px; object-fit: contain;" loop muted autoplay>
                                        <source src="{{ $mediaUrl }}" type="{{ $mimeType }}">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img class="card-img-top" style="width: 100%; height: auto; max-height: 200px; object-fit: contain;" src="{{ $mediaUrl }}" alt="{{ $tuto->title }}">
                                @endif
                            @else
                                <img class="card-img-top" src="{{ asset('assets/img/placeholder.jpg') }}" alt="Placeholder" style="width: 100%; height: auto; max-height: 200px; object-fit: contain;">
                            @endif
                            <div class="card-body p-3">
                                <h4 class="card-title mb-2" style="font-size: 1.25rem;"><a href="{{ route('tutos.show', $tuto) }}" class="text-decoration-none text-dark">{{ $tuto->title }}</a></h4>
                                <p class="text-muted mb-2" style="font-size: 0.9rem;">{{ $tuto->category ? ucfirst($tuto->category->name) : 'Uncategorized' }}</p>
                                <p style="font-size: 0.85rem;">{{ \Illuminate\Support\Str::limit($tuto->description, 80) }}</p>
                                <p class="text-muted" style="font-size: 0.8rem;">Views: {{ $tuto->views }} | 👍 {{ $tuto->likes_count }} | 👎 {{ $tuto->dislikes_count }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection