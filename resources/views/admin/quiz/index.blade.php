@extends('layouts.admin')

@section('title', 'Manage Quizzes')

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
            <h3 class="fw-bold mb-3">Manage Quizzes</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="{{ url('admin') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Quizzes</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Quizzes</h4>
                        <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary btn-sm float-end">Create New Quiz</a>
                    </div>
                    <div class="card-body">
                        @if ($quizzes->isEmpty())
                            <p class="text-muted">No quizzes available.</p>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Tutorial</th>
                                        <th>Questions</th>
                                        <th>Published</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($quizzes as $quiz)
                                        <tr>
                                            <td>{{ $quiz->title }}</td>
                                            <td>{{ $quiz->tuto ? $quiz->tuto->title : 'None' }}</td>
                                            <td>{{ $quiz->questions->count() }}</td>
                                            <td>
                                                <span class="badge {{ $quiz->is_published ? 'bg-success' : 'bg-warning' }}">
                                                    {{ $quiz->is_published ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-info btn-sm">View</a>
                                                <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-warning btn-sm">Edit</a>
                                                <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this quiz?')">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection