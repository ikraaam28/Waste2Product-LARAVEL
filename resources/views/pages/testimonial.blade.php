@extends('layouts.app')

@section('content')
<div class="container-fluid testimonial py-5 my-5">
    <div class="container py-5">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 500px;">
            <p class="fs-5 fw-medium fst-italic text-white">Testimonial</p>
            <h1 class="display-6">What our clients say about our tea</h1>
        </div>
        <div class="owl-carousel testimonial-carousel wow fadeInUp" data-wow-delay="0.5s">
            <div class="testimonial-item p-4 p-lg-5">
                <p class="mb-4">Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo</p>
                <div class="d-flex align-items-center justify-content-center">
                    <img class="img-fluid flex-shrink-0" src="{{ asset('assets/img/testimonial-1.jpg') }}" alt="">
                    <div class="text-start ms-3">
                        <h5>Client Name</h5>
                        <span class="text-primary">Profession</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-item p-4 p-lg-5">
                <p class="mb-4">Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo</p>
                <div class="d-flex align-items-center justify-content-center">
                    <img class="img-fluid flex-shrink-0" src="{{ asset('assets/img/testimonial-2.jpg') }}" alt="">
                    <div class="text-start ms-3">
                        <h5>Client Name</h5>
                        <span class="text-primary">Profession</span>
                    </div>
                </div>
            </div>
            <div class="testimonial-item p-4 p-lg-5">
                <p class="mb-4">Diam dolor diam ipsum sit. Aliqu diam amet diam et eos. Clita erat ipsum et lorem et sit, sed stet lorem sit clita duo justo</p>
                <div class="d-flex align-items-center justify-content-center">
                    <img class="img-fluid flex-shrink-0" src="{{ asset('assets/img/testimonial-3.jpg') }}" alt="">
                    <div class="text-start ms-3">
                        <h5>Client Name</h5>
                        <span class="text-primary">Profession</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
