@extends('layouts.app')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Tutorial</p>
            <h1 class="display-6">{{ $tuto->title }}</h1>
        </div>
        <div class="row g-5">
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.1s">
                @php
                    $mediaArray = $tuto->media ?: [];
                @endphp
                @if (!empty($mediaArray))
                    <div class="row g-3">
                        @foreach ($mediaArray as $media)
                            @php
                                $mediaExists = is_string($media) && Illuminate\Support\Facades\Storage::disk('public')->exists($media);
                            @endphp
                            @if ($mediaExists)
                                <div class="col-6">
                                    @if (str_ends_with($media, '.mp4'))
                                        <video src="{{ Illuminate\Support\Facades\Storage::url($media) }}" controls autoplay loop muted class="img-fluid w-100"></video>
                                    @else
                                        <img src="{{ Illuminate\Support\Facades\Storage::url($media) }}" alt="Media" class="img-fluid w-100">
                                    @endif
                                </div>
                            @else
                                <div class="col-6">
                                    <p class="text-danger">Media not found or invalid: {{ $media }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <img class="img-fluid" src="{{ asset('assets/img/placeholder.jpg') }}" alt="Placeholder">
                @endif
            </div>
            <div class="col-lg-6 wow fadeInUp" data-wow-delay="0.5s">
                <p>By {{ $tuto->user->full_name }} | {{ ucfirst($tuto->category) }} | {{ $tuto->views }} views</p>
                <p>{{ $tuto->description }}</p>
                <h2>Steps</h2>
                <ul>
                    @foreach ($tuto->steps as $step)
                        <li>{{ $step }}</li>
                    @endforeach
                </ul>
                <h2>Reactions</h2>
                @auth
                    <form action="{{ route('tutos.react', $tuto) }}" method="POST" class="mb-4">
                        @csrf
                        <button name="type" value="like" class="btn btn-primary rounded-pill py-2 px-4">👍 {{ $tuto->likes_count }}</button>
                        <button name="type" value="dislike" class="btn btn-primary rounded-pill py-2 px-4">👎 {{ $tuto->dislikes_count }}</button>
                    </form>
                @endauth
            </div>
        </div>
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Questions & Answers</p>
            <h1 class="display-6">Ask Questions About This Tutorial</h1>
        </div>
        @auth
            <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.3s">
                <div class="col-lg-8">
                    <form action="{{ route('tutos.question', $tuto) }}" method="POST" class="mb-5">
                        @csrf
                        <textarea name="question_text" required placeholder="Ask your question..." class="w-full p-2 border rounded"></textarea>
                        <button type="submit" class="btn btn-primary rounded-pill py-2 px-4">Ask a Question</button>
                    </form>
                </div>
            </div>
        @endauth
        @if ($tuto->questions->whereNull('parent_id')->isEmpty())
            <p class="text-center">No questions for this tutorial.</p>
        @else
            <div class="row g-4">
                @foreach ($tuto->questions->whereNull('parent_id') as $question)
                    <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="border p-4 rounded">
                            <p><strong>{{ $question->user->full_name }}</strong> ({{ $question->created_at->diffForHumans() }}): {{ $question->question_text }}</p>
                            @foreach ($question->replies as $reply)
                                <p style="margin-left: 20px;">↳ <strong>{{ $reply->user->full_name }}</strong> ({{ $reply->created_at->diffForHumans() }}): {{ $reply->question_text }}</p>
                            @endforeach
                            @auth
                                <form action="{{ route('tutos.question', $tuto) }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $question->id }}">
                                    <textarea name="question_text" required placeholder="Reply to this question..." class="w-full p-2 border rounded"></textarea>
                                    <button type="submit" class="btn btn-primary rounded-pill py-2 px-4">Reply</button>
                                </form>
                            @endauth
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection