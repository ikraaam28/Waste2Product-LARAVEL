@extends('layouts.admin')

@section('title', 'Dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/kaiadmin/css/demo.css') }}" />
@endpush

@section('content')
@include('admin.partials.dashboard-content')
@endsection

@push('scripts')
<script src="{{ asset('vendor/kaiadmin/js/setting-demo.js') }}"></script>
<script src="{{ asset('vendor/kaiadmin/js/demo.js') }}"></script>
@endpush


