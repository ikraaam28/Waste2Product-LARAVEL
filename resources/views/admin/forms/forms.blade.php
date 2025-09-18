@extends('layouts.admin')
@section('title', 'Forms')
@section('content')
  <div class="container">
    <div class="page-inner">
      <div class="page-header">
        <h3 class="fw-bold mb-3">Forms</h3>
        <ul class="breadcrumbs mb-3">
          <li class="nav-home">
            <a href="#"><i class="icon-home"></i></a>
          </li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Forms</a></li>
          <li class="separator"><i class="icon-arrow-right"></i></li>
          <li class="nav-item"><a href="#">Basic Form</a></li>
        </ul>
      </div>

      <div class="row">
        <div class="col-md-12">
          <div class="card">
            <div class="card-header">
              <div class="card-title">Form Elements</div>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <label for="email2">Email Address</label>
                    <input type="email" class="form-control" id="email2" placeholder="Enter Email" />
                    <small id="emailHelp2" class="form-text text-muted">We'll never share your email with anyone else.</small>
                  </div>
                  <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" placeholder="Password" />
                  </div>
                  <div class="form-group">
                    <label for="comment">Comment</label>
                    <textarea class="form-control" id="comment" rows="5"></textarea>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault" />
                    <label class="form-check-label" for="flexCheckDefault">Agree with terms and conditions</label>
                  </div>
                </div>

                <div class="col-md-6 col-lg-4">
                  <div class="form-group">
                    <div class="input-group mb-3">
                      <span class="input-group-text" id="basic-addon1">@</span>
                      <input type="text" class="form-control" placeholder="Username" aria-label="Username" aria-describedby="basic-addon1" />
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="basic-url">Your vanity URL</label>
                    <div class="input-group mb-3">
                      <span class="input-group-text" id="basic-addon3">https://example.com/users/</span>
                      <input type="text" class="form-control" id="basic-url" aria-describedby="basic-addon3" />
                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-lg-4">
                  <label class="mb-3"><b>Form Group Default</b></label>
                  <div class="form-group form-group-default">
                    <label>Input</label>
                    <input id="Name" type="text" class="form-control" placeholder="Fill Name" />
                  </div>
                  <div class="form-group">
                    <label for="largeInput">Large Input</label>
                    <input type="text" class="form-control form-control-lg" id="largeInput" placeholder="Large Input" />
                  </div>
                </div>
              </div>
            </div>
            <div class="card-action">
              <button class="btn btn-success" type="button">Submit</button>
              <button class="btn btn-danger" type="button">Cancel</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection



