@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Edit Quiz</h1>
    <form method="POST" action="{{ route('admin.quizzes.update', $quiz) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ $quiz->title }}" required>
        </div>
        <div class="mb-3">
            <label for="tuto_id" class="form-label">Tutorial</label>
            <select name="tuto_id" id="tuto_id" class="form-select" required>
                <option value="">Select Tutorial</option>
                @foreach ($tutos as $tuto)
                    <option value="{{ $tuto->id }}" {{ $quiz->tuto_id == $tuto->id ? 'selected' : '' }}>{{ $tuto->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="is_published" class="form-label">Published</label>
            <select name="is_published" id="is_published" class="form-select" required>
                <option value="1" {{ $quiz->is_published ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ !$quiz->is_published ? 'selected' : '' }}>No</option>
            </select>
        </div>
        <div id="questions">
            <h4>Questions</h4>
            @foreach ($quiz->questions as $index => $question)
                <div class="question mb-3">
                    <div class="mb-2">
                        <label class="form-label">Question Text</label>
                        <input type="text" name="questions[{{ $index }}][question_text]" class="form-control" value="{{ $question->question_text }}" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Correct Answer</label>
                        <select name="questions[{{ $index }}][correct_answer]" class="form-select" required>
                            <option value="Yes" {{ $question->correct_answer == 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ $question->correct_answer == 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">Add Question</button>
        <button type="submit" class="btn btn-primary">Update Quiz</button>
    </form>
</div>

<script>
    let questionIndex = {{ count($quiz->questions) }};
    function addQuestion() {
        const questionsDiv = document.getElementById('questions');
        const newQuestion = document.createElement('div');
        newQuestion.classList.add('question', 'mb-3');
        newQuestion.innerHTML = `
            <div class="mb-2">
                <label class="form-label">Question Text</label>
                <input type="text" name="questions[${questionIndex}][question_text]" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label">Correct Answer</label>
                <select name="questions[${questionIndex}][correct_answer]" class="form-select" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        `;
        questionsDiv.appendChild(newQuestion);
        questionIndex++;
    }
</script>
@endsection