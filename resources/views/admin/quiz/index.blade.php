@extends('layouts.admin')

@section('title', 'Manage Quizzes')

@section('content')
<style>
    .quiz-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 16px rgba(31, 38, 135, 0.15);
        transition: all 0.3s ease;
        margin-bottom: 1rem; /* Réduit l'espace entre cartes */
        overflow: hidden;
    }

    .quiz-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(31, 38, 135, 0.25);
    }

    .tuto-header {
        background: linear-gradient(135deg, #1572E8, #0d47a1);
        color: white;
        padding: 1rem; /* Réduit le padding */
        border-radius: 12px 12px 0 0;
    }

    .tuto-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .quiz-item {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s ease;
        margin: 0.5rem 1rem; /* Réduit marges internes */
    }

    .quiz-item:hover {
        background: rgba(255, 255, 255, 1);
        transform: translateX(3px);
    }

    .quiz-item-header {
        padding: 0.75rem 1rem; /* Réduit le padding */
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 8px 8px 0 0;
    }

    .quiz-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 1rem; /* Taille réduite pour compacité */
    }

    .quiz-meta {
        display: flex;
        gap: 0.5rem; /* Réduit l'espace entre badges */
        margin-top: 0.25rem;
        flex-wrap: wrap;
    }

    .meta-badge {
        padding: 0.2rem 0.6rem; /* Badges plus petits */
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .questions-badge {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .published-badge {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .unpublished-badge {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: #212529;
    }

    .quiz-actions {
        padding: 0.5rem 1rem; /* Réduit le padding */
        background: rgba(248, 249, 250, 0.9);
        border-radius: 0 0 8px 8px;
        display: flex;
        gap: 0.3rem; /* Réduit l'espace entre boutons */
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn-action {
        padding: 0.4rem 0.8rem; /* Boutons plus compacts */
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-view {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .btn-edit {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: #212529;
    }

    .btn-delete {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .no-quizzes {
        text-align: center;
        padding: 2rem; /* Réduit l'espace */
        color: #6c757d;
    }

    .no-quizzes i {
        font-size: 3rem; /* Icône plus petite */
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .create-btn {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        padding: 0.5rem 1.2rem; /* Bouton plus compact */
        border-radius: 20px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    @media (max-width: 768px) {
        .quiz-meta {
            flex-direction: column;
            gap: 0.2rem;
        }

        .quiz-actions {
            flex-direction: column;
            gap: 0.2rem;
            align-items: flex-end;
        }

        .quiz-card {
            margin-bottom: 0.75rem;
        }

        .quiz-item {
            margin: 0.3rem 0.5rem;
        }
    }
</style>

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
            <h3 class="fw-bold mb-2">Manage Quizzes</h3>
            <ul class="breadcrumbs mb-2">
                <li class="nav-home">
                    <a href="{{ url('admin') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Quizzes</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="quiz-card">
                    <div class="card-header d-flex justify-content-between align-items-center p-3">
                        <h4 class="card-title mb-0">Quizzes by Tutorial</h4>
                        <a href="{{ route('admin.quizzes.create') }}" class="create-btn">
                            <i class="fas fa-plus"></i> Create New Quiz
                        </a>
                    </div>

                    <div class="card-body p-3">
                        @if ($tutos->isEmpty() && $quizzesWithoutTuto->isEmpty())
                            <div class="no-quizzes">
                                <i class="fas fa-clipboard-list"></i>
                                <h5>No quizzes available</h5>
                                <p>Create your first quiz to get started!</p>
                            </div>
                        @else
                            <!-- Tutorials with Quizzes -->
                            @foreach ($tutos as $tuto)
                                @if ($tuto->quizzes->isNotEmpty())
                                    <div class="quiz-card">
                                        <div class="tuto-header">
                                            <h5>
                                                <i class="fas fa-book"></i>
                                                {{ $tuto->title }}
                                                <span class="badge bg-light text-dark ms-2">{{ $tuto->quizzes->count() }} Quiz{{ $tuto->quizzes->count() > 1 ? 'zes' : 'z' }}</span>
                                            </h5>
                                        </div>
                                        <div class="p-2">
                                            @foreach ($tuto->quizzes as $quiz)
                                                <div class="quiz-item">
                                                    <div class="quiz-item-header">
                                                        <h6 class="quiz-title">{{ $quiz->title }}</h6>
                                                        <div class="quiz-meta">
                                                            <span class="meta-badge questions-badge">
                                                                <i class="fas fa-question-circle"></i> {{ $quiz->questions_count }} Questions
                                                            </span>
                                                            <span class="meta-badge {{ $quiz->is_published ? 'published-badge' : 'unpublished-badge' }}">
                                                                <i class="fas fa-eye"></i> {{ $quiz->is_published ? 'Published' : 'Draft' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="quiz-actions">
                                                        <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-action btn-view">
                                                            <i class="fas fa-eye"></i> View
                                                        </a>
                                                        <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-action btn-edit">
                                                            <i class="fas fa-edit"></i> Edit
                                                        </a>
                                                        <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this quiz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-action btn-delete">
                                                                <i class="fas fa-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach

                            <!-- Quizzes without Tutorial -->
                            @if ($quizzesWithoutTuto->isNotEmpty())
                                <div class="quiz-card mt-3">
                                    <div class="tuto-header">
                                        <h5>
                                            <i class="fas fa-book-open"></i>
                                            Quizzes without Tutorial
                                            <span class="badge bg-light text-dark ms-2">{{ $quizzesWithoutTuto->count() }} Quiz{{ $quizzesWithoutTuto->count() > 1 ? 'zes' : 'z' }}</span>
                                        </h5>
                                    </div>
                                    <div class="p-2">
                                        @foreach ($quizzesWithoutTuto as $quiz)
                                            <div class="quiz-item">
                                                <div class="quiz-item-header">
                                                    <h6 class="quiz-title">{{ $quiz->title }}</h6>
                                                    <div class="quiz-meta">
                                                        <span class="meta-badge questions-badge">
                                                            <i class="fas fa-question-circle"></i> {{ $quiz->questions_count }} Questions
                                                        </span>
                                                        <span class="meta-badge {{ $quiz->is_published ? 'published-badge' : 'unpublished-badge' }}">
                                                            <i class="fas fa-eye"></i> {{ $quiz->is_published ? 'Published' : 'Draft' }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="quiz-actions">
                                                    <a href="{{ route('admin.quizzes.show', $quiz) }}" class="btn btn-action btn-view">
                                                        <i class="fas fa-eye"></i> View
                                                    </a>
                                                    <a href="{{ route('admin.quizzes.edit', $quiz) }}" class="btn btn-action btn-edit">
                                                        <i class="fas fa-edit"></i> Edit
                                                    </a>
                                                    <form action="{{ route('admin.quizzes.destroy', $quiz) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this quiz?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-action btn-delete">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
