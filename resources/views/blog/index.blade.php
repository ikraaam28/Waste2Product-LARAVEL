@extends('layouts.app')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Blog</p>
            <h1 class="display-6">Latest Tea Articles</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="blog-item">
                    <img class="img-fluid" src="{{ asset('assets/img/article.jpg') }}" alt="">
                    <div class="p-4">
                        <h4 class="mb-3">The history of tea leaf in the world</h4>
                        <p class="mb-4">Tempor erat elitr rebum at clita. Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit</p>
                        <a href="#" class="btn btn-primary rounded-pill py-2 px-4">Read More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
