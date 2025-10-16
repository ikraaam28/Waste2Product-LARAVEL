@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center">
                    <h2>Generate Your Certificate</h2>
                    <p>Congratulations! You are eligible to receive a certificate for completing the tutorial "{{ $tuto->title }}".</p>
                    <form action="{{ route('certificates.generate', $tuto) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <button type="submit" class="btn btn-primary mt-3">Generate Certificate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection