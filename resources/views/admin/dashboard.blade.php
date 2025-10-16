@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/kaiadmin/css/demo.css') }}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@endpush

@section('content')
@include('admin.partials.dashboard-content')
@endsection

@push('scripts')
<script src="{{ asset('vendor/kaiadmin/js/setting-demo.js') }}"></script>
<script src="{{ asset('vendor/kaiadmin/js/demo.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

@endpush


