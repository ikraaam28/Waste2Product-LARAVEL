@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Create Quiz</h1>
    <form method="POST" action="{{ route('admin.quizzes.store') }}">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="tuto_id" class="form-label">Tutorial</label>
            <select name="tuto_id" id="tuto_id" class="form-select" required>
                <option value="">Select Tutorial</option>
                @foreach ($tutos as $tuto)
                    <option value="{{ $tuto->id }}">{{ $tuto->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="is_published" class="form-label">Published</label>
            <select name="is_published" id="is_published" class="form-select" required>
                <option value="1">Yes</option>
                <option value="0">No</option>
            </select>
        </div>
        <div id="questions">
            <h4>Questions</h4>
            <div class="question mb-3">
                <div class="mb-2">
                    <label class="form-label">Question Text</label>
                    <input type="text" name="questions[0][question_text]" class="form-control" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Correct Answer</label>
                    <select name="questions[0][correct_answer]" class="form-select" required>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary mb-3" onclick="addQuestion()">Add Question</button>
        <button type="submit" class="btn btn-primary">Create Quiz</button>
    </form>
</div>

<script>
    let questionIndex = 1;
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