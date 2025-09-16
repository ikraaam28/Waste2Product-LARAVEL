@extends('layouts.app')

@section('content')
<style>
/* Custom styles for signup form */
.form-control:focus {
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
    transform: translateY(-2px);
}

.form-control {
    transition: all 0.3s ease;
}

.form-control:hover {
    border-color: #28a745;
    transform: translateY(-1px);
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
}

.form-check-input:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

/* Floating label animation */
.form-floating > .form-control:focus ~ label,
.form-floating > .form-control:not(:placeholder-shown) ~ label {
    color: #28a745;
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
}

/* Decorative elements animation */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.position-absolute {
    animation: float 6s ease-in-out infinite;
}

.position-absolute:nth-child(2) {
    animation-delay: -3s;
}
</style>
<!-- Signup Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
                    <p class="fs-5 fw-medium fst-italic text-primary">Join Our Eco-Community</p>
                    <h1 class="display-6">Create Your Account</h1>
                    <p class="lead text-muted">Join thousands of eco-conscious individuals and transform waste into valuable products</p>
                </div>
                
                <div class="row g-5">
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.1s">
                        <div class="bg-light rounded p-5 h-100">
                            <h3 class="text-primary mb-4">Why Join Waste2Product?</h3>
                            <div class="row g-3 mb-4">
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                            <i class="fa fa-recycle"></i>
                                        </div>
                                        <span>Access to innovative recycling solutions</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                            <i class="fa fa-lightbulb"></i>
                                        </div>
                                        <span>Creative upcycling ideas and tutorials</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                            <i class="fa fa-globe"></i>
                                        </div>
                                        <span>Contribute to environmental sustainability</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                            <i class="fa fa-heart"></i>
                                        </div>
                                        <span>Personalized waste reduction tips</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0 btn-lg-square bg-primary text-white rounded-circle me-3">
                                            <i class="fa fa-users"></i>
                                        </div>
                                        <span>Join our eco-conscious community</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="bg-primary rounded p-4 text-white">
                                    <i class="fa fa-recycle fa-3x mb-3"></i>
                                    <h5>Transform Waste Into Value</h5>
                                    <p class="mb-0">Every member contributes to a sustainable future</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                        <div class="bg-white rounded-4 shadow-lg p-5 position-relative overflow-hidden">
                            <!-- Decorative elements -->
                            <div class="position-absolute top-0 end-0" style="width: 100px; height: 100px; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1)); border-radius: 0 0 0 100px;"></div>
                            <div class="position-absolute bottom-0 start-0" style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(32, 201, 151, 0.05)); border-radius: 0 80px 0 0;"></div>
                            
                            <div class="text-center mb-4">
                                <div class="btn-square mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(135deg, #28a745, #20c997);">
                                    <i class="fa fa-recycle fa-2x text-white"></i>
                                </div>
                                <h4 class="text-primary mb-2">Create Your Account</h4>
                                <p class="text-muted">Join the waste transformation revolution</p>
                            </div>
                            
                            <form>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-pill border-2" id="firstName" placeholder="First Name" style="border-color: #e9ecef; transition: all 0.3s ease;">
                                            <label for="firstName" class="text-muted">
                                                <i class="fa fa-user me-2"></i>First Name
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-pill border-2" id="lastName" placeholder="Last Name" style="border-color: #e9ecef; transition: all 0.3s ease;">
                                            <label for="lastName" class="text-muted">
                                                <i class="fa fa-user me-2"></i>Last Name
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control rounded-pill border-2" id="email" placeholder="Email Address" style="border-color: #e9ecef; transition: all 0.3s ease;">
                                            <label for="email" class="text-muted">
                                                <i class="fa fa-envelope me-2"></i>Email Address
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control rounded-pill border-2" id="phone" placeholder="Phone Number" style="border-color: #e9ecef; transition: all 0.3s ease;">
                                            <label for="phone" class="text-muted">
                                                <i class="fa fa-phone me-2"></i>Phone Number
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control rounded-pill border-2" id="password" placeholder="Password" style="border-color: #e9ecef; transition: all 0.3s ease;">
                                            <label for="password" class="text-muted">
                                                <i class="fa fa-lock me-2"></i>Password
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control rounded-pill border-2" id="confirmPassword" placeholder="Confirm Password" style="border-color: #e9ecef; transition: all 0.3s ease;">
                                            <label for="confirmPassword" class="text-muted">
                                                <i class="fa fa-lock me-2"></i>Confirm Password
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check p-3 bg-light rounded-3">
                                            <input class="form-check-input" type="checkbox" id="terms" required style="transform: scale(1.2);">
                                            <label class="form-check-label ms-2" for="terms">
                                                I agree to the <a href="#" class="text-primary fw-bold">Terms & Conditions</a> and <a href="#" class="text-primary fw-bold">Privacy Policy</a>
                                            </label>
                                        </div>
                                    </div>
                                 
                                    <div class="col-12">
                                        <button class="btn btn-primary rounded-pill py-3 px-5 w-100 position-relative overflow-hidden" type="submit" style="background: linear-gradient(135deg, #28a745, #20c997); border: none; font-weight: 600; letter-spacing: 0.5px;">
                                            <span class="position-relative z-1">
                                                <i class="fa fa-recycle me-2"></i>Join Waste2Product
                                            </span>
                                            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1)); transform: translateX(-100%); transition: transform 0.6s ease;"></div>
                                        </button>
                                    </div>
                                    <div class="col-12 text-center">
                                        <div class="position-relative">
                                            <hr class="my-4">
                                            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted">or</span>
                                        </div>
                                        <p class="mb-0">Already have an account? <a href="#" class="text-primary fw-bold text-decoration-none">Sign In <i class="fa fa-arrow-right ms-1"></i></a></p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Signup End -->

@endsection
