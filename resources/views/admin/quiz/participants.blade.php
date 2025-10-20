@extends('layouts.admin')

@section('title', 'Quiz Participants')

@section('content')
<style>
    .tuto-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 4px 16px rgba(31, 38, 135, 0.15);
        transition: all 0.3s ease;
        margin-bottom: 1rem;
        overflow: hidden;
    }

    .tuto-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(31, 38, 135, 0.25);
    }

    .tuto-header {
        background: linear-gradient(135deg, #1572E8, #0d47a1);
        color: white;
        padding: 1rem;
        border-radius: 12px 12px 0 0;
        cursor: pointer;
    }

    .tuto-header h5 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .participant-item {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s ease;
        margin: 0.5rem 1rem;
    }

    .participant-item:hover {
        background: rgba(255, 255, 255, 1);
        transform: translateX(3px);
    }

    .participant-item-header {
        padding: 0.75rem 1rem;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 8px 8px 0 0;
    }

    .participant-title {
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
        font-size: 1rem;
    }

    .participant-meta {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.25rem;
        flex-wrap: wrap;
    }

    .meta-badge {
        padding: 0.2rem 0.6rem;
        border-radius: 15px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .score-badge {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .status-validated {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .status-failed {
        background: linear-gradient(135deg, #dc3545, #c82333);
        color: white;
    }

    .certificate-yes {
        background: linear-gradient(135deg, #17a2b8, #138496);
        color: white;
    }

    .certificate-no {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: #212529;
    }

    .no-participants {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
    }

    .no-participants i {
        font-size: 3rem;
        margin-bottom: 0.5rem;
        opacity: 0.5;
    }

    .collapse-toggle {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        text-align: left;
        background: none;
        border: none;
        color: white;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .collapse-toggle:hover {
        color: #ecf0f1;
    }

    @media (max-width: 768px) {
        .participant-meta {
            flex-direction: column;
            gap: 0.2rem;
        }

        .tuto-card {
            margin-bottom: 0.75rem;
        }

        .participant-item {
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
            <h3 class="fw-bold mb-2">Quiz Participants</h3>
            <ul class="breadcrumbs mb-2">
                <li class="nav-home">
                    <a href="{{ url('admin') }}"><i class="icon-home"></i></a>
                </li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item"><a href="{{ route('admin.quizzes.index') }}">Quizzes</a></li>
                <li class="separator"><i class="icon-arrow-right"></i></li>
                <li class="nav-item">Participants</li>
            </ul>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="tuto-card">
                    <div class="card-header p-3">
                        <h4 class="card-title mb-0">Participants by Tutorial</h4>
                    </div>

                    <div class="card-body p-3">
                        @if ($tutos->isEmpty())
                            <div class="no-participants">
                                <i class="fas fa-users"></i>
                                <h5>No participants found</h5>
                                <p>No users have taken quizzes for any tutorials yet.</p>
                            </div>
                        @else
                            @foreach ($tutos as $index => $tutoData)
                                <div class="tuto-card">
                                    <div class="tuto-header">
                                        <button class="collapse-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#participants-{{ $index }}" aria-expanded="false" aria-controls="participants-{{ $index }}">
                                            <span>
                                                <i class="fas fa-book"></i>
                                                {{ $tutoData['tuto']->title }}
                                                <span class="badge bg-light text-dark ms-2">{{ $tutoData['participants']->count() }} Participant{{ $tutoData['participants']->count() > 1 ? 's' : '' }}</span>
                                            </span>
                                            <i class="fas fa-chevron-down"></i>
                                        </button>
                                    </div>
                                    <div class="collapse" id="participants-{{ $index }}">
                                        <div class="p-2">
                                            @foreach ($tutoData['participants'] as $participant)
                                                <div class="participant-item">
                                                    <div class="participant-item-header">
                                                        <h6 class="participant-title">{{ $participant['user'] ? $participant['user']->full_name : 'Unknown User' }}</h6>
                                                        <div class="participant-meta">
                                                            <span class="meta-badge score-badge">
                                                                <i class="fas fa-star"></i> Total Score: {{ $participant['total_score'] }}/{{ $participant['total_questions'] }}
                                                            </span>
                                                            <span class="meta-badge score-badge">
                                                                <i class="fas fa-percentage"></i> {{ number_format($participant['average_percentage'], 2) }}%
                                                            </span>
                                                            <span class="meta-badge {{ $participant['status'] === 'Validated' ? 'status-validated' : 'status-failed' }}">
                                                                <i class="fas fa-check-circle"></i> {{ $participant['status'] }}
                                                            </span>
                                                            <span class="meta-badge {{ $participant['has_certificate'] ? 'certificate-yes' : 'certificate-no' }}">
                                                                <i class="fas fa-certificate"></i> Certificate: {{ $participant['has_certificate'] ? 'Yes' : 'No' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection