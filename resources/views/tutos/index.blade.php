@extends('layouts.app')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Our Tutorials</p>
            <h1 class="display-6">Discover Our Recycling Ideas</h1>
        </div>
        @auth
         
        @endauth
        @if ($tutos->isEmpty())
            <p class="text-center">No tutorials available at the moment.</p>
        @else
            <div class="row g-4">
                @foreach ($tutos as $tuto)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="store-item position-relative text-center">
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
                                    <video class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;" loop muted autoplay><source src="{{ $mediaUrl }}" type="{{ $mimeType }}">Your browser does not support the video tag.</video>
                                @else
                                    <img class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;" src="{{ $mediaUrl }}" alt="{{ $tuto->title }}">
                                @endif
                            @else
                                <img class="img-fluid" src="{{ asset('assets/img/placeholder.jpg') }}" alt="Placeholder">
                            @endif
                            <div class="p-4">
                                <h4 class="mb-3"><a href="{{ route('tutos.show', $tuto) }}">{{ $tuto->title }}</a></h4>
                                <p>By {{ $tuto->user->full_name }} | {{ ucfirst($tuto->category) }}</p>
                                <p>{{ \Illuminate\Support\Str::limit($tuto->description, 100) }}</p>
                                <p>Views: {{ $tuto->views }} | 👍 {{ $tuto->likes_count }} | 👎 {{ $tuto->dislikes_count }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection