@extends('layouts.admin')

@section('title', 'View Quiz')

@section('content')
<div class="container">
    <div class="page-inner">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="page-header">
            <h3 class="fw-bold mb-3">View Quiz</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('admin') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">View</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-title">{{ $quiz->title }}</h4>
                    </div>

                    <!-- Body -->
                    <div class="card-body">
                        <p><strong>Tutorial:</strong> {{ $quiz->tuto ? $quiz->tuto->title : 'None' }}</p>
                        <p><strong>Published:</strong> 
                            <span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning' }}">
                                {{ $quiz->is_published ? 'Yes' : 'No' }}
                            </span>
                        </p>

                        <!-- Questions -->
                        <h4 class="mt-4">Questions</h4>
                        @if ($quiz->questions->isEmpty())
                            <p class="text-muted">No questions available.</p>
                        @else
                            <ul class="list-group mb-4">
                                @foreach ($quiz->questions as $index => $question)
                                    <li class="list-group-item">
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

                        <!-- Participants -->
                        <h5 class="mt-4">Participants</h5>
                        @if ($quiz->attempts->isEmpty())
                            <p class="text-muted">No participants have taken this quiz yet.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Score</th>
                                        <th>Percentage</th>
                                        <th>Status</th>
                                        <th>Attempted At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quiz->attempts as $attempt)
                                        <tr>
                                            <td>{{ $attempt->user ? $attempt->user->full_name : 'Unknown User' }}</td>
                                            <td>{{ $attempt->score }} / {{ $quiz->questions->count() }}</td>
                                            <td>{{ number_format($attempt->percentage, 2) }}%</td>
                                            <td>
                                                <span class="badge {{ $attempt->status === 'Validated' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $attempt->status }}
                                                </span>
                                            </td>
                                            <td>{{ $attempt->created_at->format('Y-m-d H:i:s') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="card-footer">
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-warning">Edit</a>
                        <a href="{{ route('admin.quizzes.index') }}" class="btn btn-secondary">Back</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection