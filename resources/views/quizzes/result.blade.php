@extends('layouts.app')

@section('content')
<div class="container-fluid product py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Quiz Result</p>
            <h1 class="display-6">{{ $quiz->title }}</h1>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Your Score: {{ $score }} / {{ $totalQuestions }}</h4>
                        <p class="card-text">Percentage: {{ number_format($percentage, 2) }}%</p>
                        <p class="card-text {{ $status == 'Validated' ? 'text-success' : 'text-danger' }}">
                            Status: {{ $status }}
                        </p>
                        <hr>
                        @foreach ($quiz->questions as $index => $question)
                            <div class="mb-4">
                                <h5>{{ $index + 1 }}. {{ $question->question_text }}</h5>
                                <p>Your Answer: {{ $correctAnswers[$question->id]['user_answer'] ?? 'Not answered' }}</p>
                                <p>Correct Answer: {{ $correctAnswers[$question->id]['correct_answer'] }}</p>
                                <p class="{{ $correctAnswers[$question->id]['is_correct'] ? 'text-success' : 'text-danger' }}">
                                    {{ $correctAnswers[$question->id]['is_correct'] ? 'Correct' : 'Incorrect' }}
                                </p>
                                <p><strong>Feedback:</strong> {{ $correctAnswers[$question->id]['feedback'] }}</p>
                            </div>
                        @endforeach
                        <a href="{{ route('quizzes.index') }}" class="btn btn-primary">Back to Quizzes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection