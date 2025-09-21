@extends('layouts.app')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Our Tutorials</p>
            <h1 class="display-6">Discover Our Recycling Ideas</h1>
        </div>
        @auth
            <div class="text-center mb-5">
                <a href="{{ route('tutos.create') }}" class="btn btn-primary rounded-pill py-3 px-5 wow fadeInUp" data-wow-delay="0.3s">Create New Tutorial</a>
            </div>
        @endauth
        @if ($tutos->isEmpty())
            <p class="text-center">No tutorials available at the moment.</p>
        @else
            <div class="row g-4">
                @foreach ($tutos as $tuto)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="store-item position-relative text-center">
                            @php
                                $firstMedia = $tuto->media && is_array($tuto->media) && count($tuto->media) > 0 ? $tuto->media[0] : null;
                            @endphp
                            @if ($firstMedia && Illuminate\Support\Facades\Storage::disk('public')->exists($firstMedia))
                                <img class="img-fluid" src="{{ Illuminate\Support\Facades\Storage::url($firstMedia) }}" alt="{{ $tuto->title }}">
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