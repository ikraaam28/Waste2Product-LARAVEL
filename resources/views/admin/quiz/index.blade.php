@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Manage Quizzes</h1>
    <a href="{{ route('admin.quizzes.create') }}" class="btn btn-primary mb-3">Create New Quiz</a>
    <table class="table">
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
                    <td>{{ $quiz->is_published ? 'Yes' : 'No' }}</td>
                    <td>
                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-info btn-sm">View</a>
                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection