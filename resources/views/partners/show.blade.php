@extends('layouts.app')

@section('content')
<div class="container-xxl py-5" style="margin-top: 80px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body">
                        <h2 class="text-primary mb-3">{{ $partner->name }}</h2>
                        <p><strong>Type:</strong> {{ $partner->type ?? '-' }}</p>
                        <p><strong>Address:</strong> {{ $partner->address ?? '-' }}</p>
                        <p><strong>Email:</strong> {{ $partner->email ?? '-' }}</p>
                        <p><strong>Phone:</strong> {{ $partner->phone ?? '-' }}</p>

                        <div class="mt-4">
                            <a href="{{ route('partners.front') }}" class="btn btn-outline-primary">
                                <i class="fa fa-arrow-left me-2"></i>Back to Partners
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
