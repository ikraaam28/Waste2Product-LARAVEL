@extends('layouts.admin')
@section('title', 'Grid System')
@section('content')
  <div class="container">
    <div class="page-inner">
      <div class="page-header">
        <h3 class="fw-bold mb-3">Grid System</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Base</a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Grid System</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-6"><div class="card"><div class="card-body">Col 6</div></div></div>
        <div class="col-md-6"><div class="card"><div class="card-body">Col 6</div></div></div>
      </div>
    </div>
  </div>
@endsection



