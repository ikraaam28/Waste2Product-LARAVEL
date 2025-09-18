@extends('layouts.admin')
@section('title', 'Sparkline')
@section('content')
  <div class="container">
    <div class="page-inner">
      <div class="page-header">
        <h3 class="fw-bold mb-3">Sparkline</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home"><a href="#"><i class="icon-home"></i></a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Charts</a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Sparkline</a></li>
        </ul>
      </div>
      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header"><div class="card-title">Sparkline examples</div></div>
            <div class="card-body">
              <div id="sparkline-example"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection



