@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>{{ $quiz->title }}</h1>
    <p><strong>Tutorial:</strong> {{ $quiz->tuto ? $quiz->tuto->title : 'None' }}</p>
    <p><strong>Published:</strong> {{ $quiz->is_published ? 'Yes' : 'No' }}</p>
    <h4>Questions</h4>
    @if ($quiz->questions->isEmpty())
        <p>No questions available.</p>
    @else
        <ul>
            @foreach ($quiz->questions as $index => $question)
                <li>
                    <strong>{{ $index + 1 }}. {{ $question->question_text }}</strong>
                    <ul>
                        @foreach ($question->options as $option)
                            <li>{{ $option }} {{ $option == $question->correct_answer ? '(Correct)' : '' }}</li>
                        @endforeach
                    </ul>
                </li>
            @endforeach
        </ul>
    @endif
    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-warning">Edit</a>
    <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection