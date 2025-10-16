@extends('layouts.app')

@section('title', isset($score) ? 'Quiz Result: ' . $quiz->title : 'Quiz: ' . $quiz->title)

@section('content')
<div class="container-fluid product py-5 my-5 bg-light">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">{{ isset($score) ? 'Quiz Result' : 'Quiz' }}</p>
            <h1 class="display-6 text-white bg-primary p-2 rounded shadow-sm">{{ $quiz->title }}</h1>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                @if (isset($score))
                    <!-- Results Section -->
                    <div class="card bg-white shadow-lg border-0">
                        <div class="card-body p-4">
                            <h4 class="card-title text-primary mb-3">Your Score: <span class="text-dark">{{ $score }} / {{ $totalQuestions }}</span></h4>
                            <p class="card-text fs-5 text-muted mb-3">Percentage: <span class="text-dark">{{ number_format($percentage, 2) }}%</span></p>
                            <p class="card-text fs-4 fw-bold {{ $status == 'Validated' ? 'text-success' : 'text-danger' }} mb-4">
                                Status: {{ $status }} (Final)
                            </p>
                            <hr class="my-4">
                            @foreach ($quiz->questions as $index => $question)
                                <div class="result-block mb-4 p-3 bg-light rounded shadow-sm">
                                    <h5 class="mb-3 text-primary"><span class="badge bg-primary text-white">{{ $index + 1 }}</span> {{ $question->question_text }}</h5>
                                    <p class="mb-2"><strong>Your Answer:</strong> {{ $correctAnswers[$question->id]['user_answer'] ?? 'Not answered' }}</p>
                                    <p class="mb-2"><strong>Correct Answer:</strong> {{ $correctAnswers[$question->id]['correct_answer'] }}</p>
                                    <p class="mb-2 {{ $correctAnswers[$question->id]['is_correct'] ? 'text-success' : 'text-danger' }}">
                                        <strong>{{ $correctAnswers[$question->id]['is_correct'] ? 'Correct' : 'Incorrect' }}</strong>
                                    </p>
                                    <p class="text-muted"><strong>Feedback:</strong> {{ $correctAnswers[$question->id]['feedback'] }}</p>
                                </div>
                            @endforeach
                            <a href="{{ route('quizzes.index') }}" class="btn btn-primary btn-lg w-100 py-3 mt-4 shadow">Back to Quizzes</a>
                        </div>
                    </div>
                @else
                    <!-- Form Section -->
                    <div class="card bg-white shadow-sm border-0">
                        <div class="card-body p-4">
                            <h4 class="card-title text-muted mb-4">Related Tutorial: {{ $quiz->tuto ? $quiz->tuto->title : 'None' }}</h4>
                            <form method="POST" action="{{ route('quizzes.submit', $quiz) }}" class="quiz-form">
                                @csrf
                                @foreach ($quiz->questions as $index => $question)
                                    <div class="question-block mb-5 p-3 bg-white rounded shadow-sm">
                                        <h5 class="mb-3 text-primary"><span class="badge bg-primary text-white">{{ $index + 1 }}</span> {{ $question->question_text }}</h5>
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="Yes" required>
                                            <label class="form-check-label fs-5 text-dark">Yes</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="No">
                                            <label class="form-check-label fs-5 text-dark">No</label>
                                        </div>
                                    </div>
                                @endforeach
                                <button type="submit" class="btn btn-primary btn-lg w-100 py-3 mt-4 shadow">Submit Quiz</button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .question-block, .result-block {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .question-block:hover, .result-block:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .card {
        border-radius: 15px;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #0056b3;
    }
    .text-success {
        color: #28a745 !important;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }
        .btn-lg {
            font-size: 1rem;
        }
    }
</style>
@endsection