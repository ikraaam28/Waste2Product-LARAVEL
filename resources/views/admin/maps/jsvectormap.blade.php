@extends('layouts.admin')
@section('title', 'Jsvectormap')
@section('content')
  <div class="container">
    <div class="page-inner">
      <div class="page-header">
        <h3 class="fw-bold mb-3">Jsvectormap</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Maps</a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Jsvectormap</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header"><div class="card-title">Map</div></div>
            <div class="card-body">
              <div id="jsvectormap" style="width: 100%; height: 400px;"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection



