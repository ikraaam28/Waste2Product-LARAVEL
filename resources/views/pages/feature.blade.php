@extends('layouts.app')

@section('content')
<div class="container-xxl py-5">
    <div class="container">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-primary">Features</p>
            <h1 class="display-6">Why Choose Our Tea</h1>
        </div>
        <div class="row g-4">
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.1s">
                <div class="feature-item text-center p-4">
                    <div class="btn-square mx-auto mb-3">
                        <i class="fa fa-leaf fa-2x text-white"></i>
                    </div>
                    <h4 class="mb-3">100% Organic</h4>
                    <p class="mb-0">Our tea is made from 100% organic ingredients sourced from the finest tea gardens.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.3s">
                <div class="feature-item text-center p-4">
                    <div class="btn-square mx-auto mb-3">
                        <i class="fa fa-heart fa-2x text-white"></i>
                    </div>
                    <h4 class="mb-3">Health Benefits</h4>
                    <p class="mb-0">Rich in antioxidants and nutrients that promote good health and well-being.</p>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 wow fadeInUp" data-wow-delay="0.5s">
                <div class="feature-item text-center p-4">
                    <div class="btn-square mx-auto mb-3">
                        <i class="fa fa-award fa-2x text-white"></i>
                    </div>
                    <h4 class="mb-3">Premium Quality</h4>
                    <p class="mb-0">We ensure the highest quality standards in every cup of tea we serve.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
