@extends('layouts.app')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Our Quizzes</p>
            <h1 class="display-6">Test Your Recycling Knowledge</h1>
        </div>

        <!-- Filter Form -->
        <div class="row mb-4">
            <div class="col-md-6">
                <form method="GET" action="{{ route('quizzes.index') }}">
                    <div class="row g-3">
                        <div class="col-md-5">
                            <label for="tuto_id" class="form-label">Filter by Tutorial</label>
                            <select name="tuto_id" id="tuto_id" class="form-select">
                                <option value="">All Tutorials</option>
                                @foreach ($tutos as $tuto)
                                    <option value="{{ $tuto->id }}" {{ request('tuto_id') == $tuto->id ? 'selected' : '' }}>
                                        {{ $tuto->title }}
                                    </option>
                                @endforeach
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

        @if ($quizzes->isEmpty())
            <p class="text-center">No quizzes available at the moment.</p>
        @else
            <div class="row g-4">
                @foreach ($quizzes as $quiz)
                    <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="store-item position-relative text-center">
                            @php
                                $mediaUrl = $quiz->tuto && $quiz->tuto->media && is_array($quiz->tuto->media) && isset($quiz->tuto->media[0])
                                    ? asset('storage/' . $quiz->tuto->media[0]['path'])
                                    : asset('assets/img/placeholder.jpg');
                                $isVideo = $quiz->tuto && $quiz->tuto->media && is_array($quiz->tuto->media) && isset($quiz->tuto->media[0])
                                    && strpos($quiz->tuto->media[0]['mime_type'], 'video') === 0;
                            @endphp
                            @if ($mediaUrl && $isVideo)
                                <video class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;" loop muted autoplay>
                                    <source src="{{ $mediaUrl }}" type="{{ $quiz->tuto->media[0]['mime_type'] }}">
                                    Your browser does not support the video tag.
                                </video>
                            @else
                                <img class="img-fluid" style="width: 100%; height: 200px; object-fit: cover;" src="{{ $mediaUrl }}" alt="{{ $quiz->title }}">
                            @endif
                            <div class="p-4">
                                <h4 class="mb-3"><a href="{{ route('quizzes.show', $quiz) }}">{{ $quiz->title }}</a></h4>
                                <p>Tutorial: {{ $quiz->tuto ? $quiz->tuto->title : 'None' }}</p>
                                <p>Questions: {{ $quiz->questions->count() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection