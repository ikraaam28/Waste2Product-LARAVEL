@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body">
            <h1 class="text-center mb-4 text-primary fw-bold">
                <i class="bi bi-journal-plus me-2"></i>Create Quiz
            </h1>
            <form method="POST" action="{{ route('admin.quizzes.store') }}">
                @csrf

                <!-- Title -->
                <div class="mb-4">
                    <label for="title" class="form-label fw-semibold">Quiz Title</label>
                    <input type="text" name="title" id="title" class="form-control form-control-lg rounded-3 shadow-sm" placeholder="Enter quiz title" required>
                </div>

                <!-- Tutorial -->
                <div class="mb-4">
                    <label for="tuto_id" class="form-label fw-semibold">Associated Tutorial</label>
                    <select name="tuto_id" id="tuto_id" class="form-select form-select-lg rounded-3 shadow-sm" required>
                        <option value="">Select a tutorial</option>
                        @foreach ($tutos as $tuto)
                            <option value="{{ $tuto->id }}">{{ $tuto->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Publication -->
                <div class="mb-4">
                    <label for="is_published" class="form-label fw-semibold">Publish Status</label>
                    <select name="is_published" id="is_published" class="form-select form-select-lg rounded-3 shadow-sm" required>
                        <option value="1">Published</option>
                        <option value="0">Draft</option>
                    </select>
                </div>

                <!-- Questions Section -->
                <div id="questions" class="mb-4">
                    <h4 class="fw-bold text-secondary mb-3">
                        <i class="bi bi-question-circle me-2"></i>Questions
                    </h4>

                    <div class="question card mb-3 border-0 shadow-sm rounded-4 p-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Question Text</label>
                            <input type="text" name="questions[0][question_text]" class="form-control rounded-3" placeholder="Enter question text" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-semibold">Correct Answer</label>
                            <select name="questions[0][correct_answer]" class="form-select rounded-3" required>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Add Question -->
                <div class="text-center mb-4">
                    <button type="button" class="btn btn-outline-primary rounded-3 px-4 shadow-sm" onclick="addQuestion()">
                        <i class="bi bi-plus-circle me-2"></i>Add Question
                    </button>
                </div>

                <!-- Submit -->
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-4 shadow">
                        <i class="bi bi-check-circle me-2"></i>Create Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap Icons (if not already loaded) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<script>
    let questionIndex = 1;
    function addQuestion() {
        const questionsDiv = document.getElementById('questions');
        const newQuestion = document.createElement('div');
        newQuestion.classList.add('question', 'card', 'mb-3', 'border-0', 'shadow-sm', 'rounded-4', 'p-3');
        newQuestion.innerHTML = `
            <div class="mb-3">
                <label class="form-label fw-semibold">Question Text</label>
                <input type="text" name="questions[${questionIndex}][question_text]" class="form-control rounded-3" placeholder="Enter question text" required>
            </div>
            <div class="mb-2">
                <label class="form-label fw-semibold">Correct Answer</label>
                <select name="questions[${questionIndex}][correct_answer]" class="form-select rounded-3" required>
                    <option value="Yes">Yes</option>
                    <option value="No">No</option>
                </select>
            </div>
        `;
        newQuestion.style.animation = 'fadeIn 0.5s ease-in-out';
        questionsDiv.appendChild(newQuestion);
        questionIndex++;
    }

    // Petite animation CSS
    const style = document.createElement('style');
    style.innerHTML = `
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    `;
    document.head.appendChild(style);
</script>
@endsection
