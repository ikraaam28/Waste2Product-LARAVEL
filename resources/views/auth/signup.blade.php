@extends('layouts.app')

@section('content')
<!-- reCAPTCHA v2 Script -->
<!-- <script src="https://www.google.com/recaptcha/api.js" async defer></script> -->
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

/* Error Popup Styles */
.error-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    animation: slideInRight 0.5s ease-out;
}

.error-popup-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
    border: 1px solid rgba(220, 53, 69, 0.2);
    overflow: hidden;
    position: relative;
}

.error-popup::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #dc3545, #c82333);
}

.error-popup-header {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1), rgba(200, 35, 51, 0.1));
    border-bottom: 1px solid rgba(220, 53, 69, 0.1);
}

.error-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #dc3545, #c82333);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    animation: pulse 2s infinite;
}

.error-icon i {
    color: white;
    font-size: 18px;
}

.error-title {
    flex: 1;
    margin: 0;
    color: #dc3545;
    font-weight: 600;
    font-size: 16px;
}

.error-close {
    background: none;
    border: none;
    color: #dc3545;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.error-close:hover {
    background: rgba(220, 53, 69, 0.1);
    transform: scale(1.1);
}

.error-popup-body {
    padding: 20px;
}

.error-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 10px;
    padding: 8px 12px;
    background: rgba(220, 53, 69, 0.05);
    border-radius: 8px;
    border-left: 3px solid #dc3545;
    transition: all 0.3s ease;
}

.error-item:last-child {
    margin-bottom: 0;
}

.error-item:hover {
    background: rgba(220, 53, 69, 0.1);
    transform: translateX(5px);
}

.error-item i {
    color: #dc3545;
    font-size: 14px;
    margin-top: 2px;
    flex-shrink: 0;
}

.error-item span {
    color: #dc3545;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 500;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
    }
}

/* Success Popup Styles */
.success-popup {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
    animation: slideInRight 0.5s ease-out;
}

.success-popup-content {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
    border: 1px solid rgba(40, 167, 69, 0.2);
    overflow: hidden;
    position: relative;
}

.success-popup::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #28a745, #20c997);
}

.success-popup-header {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
    border-bottom: 1px solid rgba(40, 167, 69, 0.1);
}

.success-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 12px;
    animation: successPulse 2s infinite;
}

.success-icon i {
    color: white;
    font-size: 18px;
}

.success-title {
    flex: 1;
    margin: 0;
    color: #28a745;
    font-weight: 600;
    font-size: 16px;
}

.success-close {
    background: none;
    border: none;
    color: #28a745;
    font-size: 18px;
    cursor: pointer;
    padding: 5px;
    border-radius: 50%;
    transition: all 0.3s ease;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.success-close:hover {
    background: rgba(40, 167, 69, 0.1);
    transform: scale(1.1);
}

.success-popup-body {
    padding: 20px;
}

.success-item {
    display: flex;
    align-items: flex-start;
    padding: 8px 12px;
    background: rgba(40, 167, 69, 0.05);
    border-radius: 8px;
    border-left: 3px solid #28a745;
    transition: all 0.3s ease;
}

.success-item:hover {
    background: rgba(40, 167, 69, 0.1);
    transform: translateX(5px);
}

.success-item i {
    color: #28a745;
    font-size: 14px;
    margin-top: 2px;
    flex-shrink: 0;
}

.success-item span {
    color: #28a745;
    font-size: 14px;
    line-height: 1.4;
    font-weight: 500;
}

@keyframes successPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .error-popup,
    .success-popup {
        top: 10px;
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>
<!-- Signup Start -->
<div class="container-xxl py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s" style="max-width: 600px;">
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
                        </div>
                    </div>
                    
                    <div class="col-lg-6 wow fadeIn" data-wow-delay="0.5s">
                        <div class="bg-white rounded-4 shadow-lg p-5 position-relative overflow-hidden">
                            <!-- Decorative elements -->
                            <div class="position-absolute top-0 end-0" style="width: 100px; height: 100px; background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1)); border-radius: 0 0 0 100px;"></div>
                            <div class="position-absolute bottom-0 start-0" style="width: 80px; height: 80px; background: linear-gradient(135deg, rgba(40, 167, 69, 0.05), rgba(32, 201, 151, 0.05)); border-radius: 0 80px 0 0;"></div>
                            
                             <div class="text-center mb-4">
                                 <h4 class="text-primary mb-2">Create Your Account</h4>
                                 <p class="text-muted">Join the waste transformation revolution</p>
                                
                                @if(session('success'))
                                    <div class="success-popup" role="alert">
                                        <div class="success-popup-content">
                                            <div class="success-popup-header">
                                                <div class="success-icon">
                                                    <i class="fa fa-check-circle"></i>
                                                </div>
                                                <h6 class="success-title">Registration Successful!</h6>
                                                <button type="button" class="success-close" data-bs-dismiss="alert">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="success-popup-body">
                                                <div class="success-item">
                                                    <i class="fa fa-check-circle me-2"></i>
                                                    <span>{{ session('success') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($errors->any())
                                    <div class="error-popup" role="alert">
                                        <div class="error-popup-content">
                                            <div class="error-popup-header">
                                                <div class="error-icon">
                                                    <i class="fa fa-exclamation-circle"></i>
                                                </div>
                                                <h6 class="error-title">Registration Error</h6>
                                                <button type="button" class="error-close" data-bs-dismiss="alert">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                            <div class="error-popup-body">
                                                @foreach($errors->all() as $error)
                                                    <div class="error-item">
                                                        <i class="fa fa-times-circle me-2"></i>
                                                        <span>{{ $error }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <form method="POST" action="{{ route('signup.store') }}" id="signupForm">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-pill border-2 @error('first_name') is-invalid @enderror" id="firstName" name="first_name" placeholder="First Name" value="{{ old('first_name') }}" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                            <label for="firstName" class="text-muted">
                                                <i class="fa fa-user me-2"></i>First Name
                                            </label>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control rounded-pill border-2 @error('last_name') is-invalid @enderror" id="lastName" name="last_name" placeholder="Last Name" value="{{ old('last_name') }}" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                            <label for="lastName" class="text-muted">
                                                <i class="fa fa-user me-2"></i>Last Name
                                            </label>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="email" class="form-control rounded-pill border-2 @error('email') is-invalid @enderror" id="email" name="email" placeholder="Email Address" value="{{ old('email') }}" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                            <label for="email" class="text-muted">
                                                <i class="fa fa-envelope me-2"></i>Email Address
                                            </label>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="tel" class="form-control rounded-pill border-2 @error('phone') is-invalid @enderror" id="phone" name="phone" placeholder="Phone Number" value="{{ old('phone') }}" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                            <label for="phone" class="text-muted">
                                                <i class="fa fa-phone me-2"></i>Phone Number
                                            </label>
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <select class="form-select rounded-pill border-2 @error('city') is-invalid @enderror" id="city" name="city" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                                <option value="">Select your city</option>
                                                <option value="Tunis" {{ old('city') == 'Tunis' ? 'selected' : '' }}>Tunis</option>
                                                <option value="Sfax" {{ old('city') == 'Sfax' ? 'selected' : '' }}>Sfax</option>
                                                <option value="Sousse" {{ old('city') == 'Sousse' ? 'selected' : '' }}>Sousse</option>
                                                <option value="Kairouan" {{ old('city') == 'Kairouan' ? 'selected' : '' }}>Kairouan</option>
                                                <option value="Bizerte" {{ old('city') == 'Bizerte' ? 'selected' : '' }}>Bizerte</option>
                                                <option value="Gabès" {{ old('city') == 'Gabès' ? 'selected' : '' }}>Gabès</option>
                                                <option value="Ariana" {{ old('city') == 'Ariana' ? 'selected' : '' }}>Ariana</option>
                                                <option value="Gafsa" {{ old('city') == 'Gafsa' ? 'selected' : '' }}>Gafsa</option>
                                                <option value="Monastir" {{ old('city') == 'Monastir' ? 'selected' : '' }}>Monastir</option>
                                                <option value="Ben Arous" {{ old('city') == 'Ben Arous' ? 'selected' : '' }}>Ben Arous</option>
                                                <option value="Kasserine" {{ old('city') == 'Kasserine' ? 'selected' : '' }}>Kasserine</option>
                                                <option value="Medenine" {{ old('city') == 'Medenine' ? 'selected' : '' }}>Medenine</option>
                                                <option value="Nabeul" {{ old('city') == 'Nabeul' ? 'selected' : '' }}>Nabeul</option>
                                                <option value="Tataouine" {{ old('city') == 'Tataouine' ? 'selected' : '' }}>Tataouine</option>
                                                <option value="Béja" {{ old('city') == 'Béja' ? 'selected' : '' }}>Béja</option>
                                                <option value="Jendouba" {{ old('city') == 'Jendouba' ? 'selected' : '' }}>Jendouba</option>
                                                <option value="Kébili" {{ old('city') == 'Kébili' ? 'selected' : '' }}>Kébili</option>
                                                <option value="Le Kef" {{ old('city') == 'Le Kef' ? 'selected' : '' }}>Le Kef</option>
                                                <option value="Mahdia" {{ old('city') == 'Mahdia' ? 'selected' : '' }}>Mahdia</option>
                                                <option value="Manouba" {{ old('city') == 'Manouba' ? 'selected' : '' }}>Manouba</option>
                                                <option value="Médenine" {{ old('city') == 'Médenine' ? 'selected' : '' }}>Médenine</option>
                                                <option value="Siliana" {{ old('city') == 'Siliana' ? 'selected' : '' }}>Siliana</option>
                                                <option value="Zaghouan" {{ old('city') == 'Zaghouan' ? 'selected' : '' }}>Zaghouan</option>
                                            </select>
                                            <label for="city" class="text-muted">
                                                <i class="fa fa-map-marker-alt me-2"></i>City
                                            </label>
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control rounded-pill border-2 @error('password') is-invalid @enderror" id="password" name="password" placeholder="Password" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                            <label for="password" class="text-muted">
                                                <i class="fa fa-lock me-2"></i>Password
                                            </label>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <input type="password" class="form-control rounded-pill border-2 @error('password_confirmation') is-invalid @enderror" id="confirmPassword" name="password_confirmation" placeholder="Confirm Password" style="border-color: #e9ecef; transition: all 0.3s ease;" required>
                                            <label for="confirmPassword" class="text-muted">
                                                <i class="fa fa-lock me-2"></i>Confirm Password
                                            </label>
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-check p-3 bg-light rounded-3">
                                            <input class="form-check-input @error('terms_accepted') is-invalid @enderror" type="checkbox" id="terms" name="terms_accepted" value="1" required style="transform: scale(1.2);">
                                            <label class="form-check-label ms-2" for="terms">
                                                I agree to the <a href="#" class="text-primary fw-bold">Terms & Conditions</a> and <a href="#" class="text-primary fw-bold">Privacy Policy</a>
                                            </label>
                                            @error('terms_accepted')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- reCAPTCHA v2 invisible -->
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="text-muted small">
                                                <i class="fa fa-shield-alt me-1"></i>
                                                Protected by reCAPTCHA
                                            </div>
                                            <div class="text-muted small">
                                                <a href="https://policies.google.com/privacy" target="_blank" class="text-decoration-none">Privacy</a> • 
                                                <a href="https://policies.google.com/terms" target="_blank" class="text-decoration-none">Terms</a>
                                            </div>
                                        </div>
                                        <div class="g-recaptcha" data-sitekey="{{ config('app.recaptcha_site_key') }}" data-callback="onRecaptchaSuccess" data-size="invisible"></div>
                                        <!-- Hidden input for reCAPTCHA token -->
                                        <input type="hidden" name="g-recaptcha-response" id="recaptcha-token" value="">
                                        @error('recaptcha')
                                            <div class="text-danger small mt-1">
                                                <i class="fa fa-exclamation-circle me-1"></i>{{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                 
                                    <div class="col-12">
                                        <button class="btn btn-primary rounded-pill py-3 px-5 w-100 position-relative overflow-hidden" type="submit" id="submitBtn" disabled style="background: linear-gradient(135deg, #6c757d, #495057); border: none; font-weight: 600; letter-spacing: 0.5px;">
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
                                        <p class="mb-0">Already have an account? <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">Sign In <i class="fa fa-arrow-right ms-1"></i></a></p>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const inputs = form.querySelectorAll('input, select');
    
    // Validation state for each field
    const fieldValidationState = {};
    
    // Fonction pour valider un champ
    function validateField(field, showError = false) {
        const value = field.value.trim();
        const fieldName = field.name;
        let isValid = true;
        let errorMessage = '';
        
        // Supprimer les anciens messages d'erreur
        const existingError = field.parentNode.querySelector('.field-error');
        if (existingError) {
            existingError.remove();
        }
        
        // Validation selon le type de champ
        switch(fieldName) {
            case 'first_name':
            case 'last_name':
                if (value.length === 0) {
                    isValid = false;
                    errorMessage = 'This field is required';
                } else if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Must be at least 2 characters long';
                }
                break;
            case 'email':
                if (value.length === 0) {
                    isValid = false;
                    errorMessage = 'Email is required';
                } else {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Please enter a valid email address';
                    }
                }
                break;
            case 'phone':
                if (value.length === 0) {
                    isValid = false;
                    errorMessage = 'Phone number is required';
                } else {
                    const phoneRegex = /^[0-9]{8}$/;
                    if (!phoneRegex.test(value)) {
                        isValid = false;
                        errorMessage = 'Please enter exactly 8 digits';
                    }
                }
                break;
            case 'city':
                if (!value) {
                    isValid = false;
                    errorMessage = 'Please select your city';
                }
                break;
            case 'password':
                if (value.length === 0) {
                    isValid = false;
                    errorMessage = 'Password is required';
                } else if (value.length < 8) {
                    isValid = false;
                    errorMessage = 'Password must be at least 8 characters long';
                }
                break;
            case 'password_confirmation':
                if (value.length === 0) {
                    isValid = false;
                    errorMessage = 'Password confirmation is required';
                } else {
                    const password = form.querySelector('input[name="password"]').value;
                    if (value !== password) {
                        isValid = false;
                        errorMessage = 'Password confirmation does not match';
                    }
                }
                break;
        }
        
        // Store validation state
        fieldValidationState[fieldName] = isValid;
        
        // Afficher l'erreur seulement si showError est true et qu'il y a une erreur
        if (!isValid && showError) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error text-danger small mt-1';
            errorDiv.innerHTML = `<i class="fa fa-exclamation-circle me-1"></i>${errorMessage}`;
            field.parentNode.appendChild(errorDiv);
            field.classList.add('is-invalid');
        } else if (isValid && value.length > 0) {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        } else if (!isValid) {
            field.classList.remove('is-valid');
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid', 'is-valid');
        }
        
        return isValid;
    }
    
    // Function to check if all fields are valid
    function checkFormValidity() {
        let allValid = true;
        
        // Check all required fields
        const requiredFields = ['first_name', 'last_name', 'email', 'phone', 'city', 'password', 'password_confirmation'];
        requiredFields.forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const isValid = validateField(field, false); // Ne pas afficher d'erreur
                if (!isValid || field.value.trim() === '') {
                    allValid = false;
                }
            }
        });
        
        // Check terms and conditions
        const termsCheckbox = form.querySelector('input[name="terms_accepted"]');
        if (termsCheckbox && !termsCheckbox.checked) {
            allValid = false;
        }
        
        // Enable/disable button
        if (allValid) {
            submitBtn.disabled = false;
            submitBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            submitBtn.style.cursor = 'pointer';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.background = 'linear-gradient(135deg, #6c757d, #495057)';
            submitBtn.style.cursor = 'not-allowed';
        }
    }
    
    // Add validation events
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this, true); // Afficher les erreurs au blur
            checkFormValidity();
        });
        
        input.addEventListener('input', function() {
            // Limit phone to exactly 8 characters
            if (this.name === 'phone') {
                // Remove all non-numeric characters
                this.value = this.value.replace(/[^0-9]/g, '');
                // Limit to 8 characters maximum
                if (this.value.length > 8) {
                    this.value = this.value.substring(0, 8);
                }
            }
            
            // Immediate validation for button check
            checkFormValidity();
            
            // Immediate validation for error display
            validateField(this, true);
            
            // Re-valider la confirmation si on tape dans le mot de passe
            if (this.name === 'password') {
                const confirmField = form.querySelector('input[name="password_confirmation"]');
                if (confirmField.value) {
                    validateField(confirmField, true);
                }
            }
        });
    });
    
    // Event for terms checkbox
    const termsCheckbox = form.querySelector('input[name="terms_accepted"]');
    if (termsCheckbox) {
        termsCheckbox.addEventListener('change', function() {
            checkFormValidity();
        });
    }
    
    // Validation initiale sans afficher d'erreurs
    checkFormValidity();
    
    // Check that reCAPTCHA is loaded
    function checkRecaptchaLoaded() {
        if (typeof grecaptcha !== 'undefined') {
            console.log('reCAPTCHA loaded successfully');
        } else {
            console.log('Waiting for reCAPTCHA...');
            setTimeout(checkRecaptchaLoaded, 100);
        }
    }
    
    // Start verification
    // checkRecaptchaLoaded();
    
    // // Gestion du submit du formulaire
    // form.addEventListener('submit', function(e) {
    //     e.preventDefault();
        
    //     // Check form validity
    //     if (form.checkValidity()) {
    //         // Trigger invisible reCAPTCHA v2
    //         if (typeof grecaptcha !== 'undefined') {
    //             console.log('Executing reCAPTCHA...');
    //             grecaptcha.execute();
    //         } else {
    //             console.error('reCAPTCHA not loaded');
    //             alert('reCAPTCHA not loaded. Please refresh the page.');
    //         }
    //     } else {
    //         // Afficher les erreurs
    //         form.classList.add('was-validated');
    //     }
    // });
    
    // // reCAPTCHA v2 invisible - Callback function
    // window.onRecaptchaSuccess = function(token) {
    //     console.log('reCAPTCHA v2 success:', token);
        
    //     // Ajouter le token reCAPTCHA au formulaire
    //     const recaptchaInput = form.querySelector('input[name="g-recaptcha-response"]');
    //     if (recaptchaInput) {
    //         recaptchaInput.value = token;
    //     } else {
    //         // Create hidden input for reCAPTCHA token
    //         const hiddenInput = document.createElement('input');
    //         hiddenInput.type = 'hidden';
    //         hiddenInput.name = 'g-recaptcha-response';
    //         hiddenInput.value = token;
    //         form.appendChild(hiddenInput);
    //     }
        
    //     // Disable button to prevent double submissions
    //     const submitButton = form.querySelector('button[type="submit"]');
    //     if (submitButton) {
    //         submitButton.disabled = true;
    //         submitButton.innerHTML = '<i class="fa fa-spinner fa-spin me-2"></i>Creating Account...';
    //     }
        
    //     // Submit form after reCAPTCHA validation
    //     form.submit();
    // };
    
    
    // // reCAPTCHA v2 invisible - Callback en cas d'erreur
    // window.onRecaptchaError = function(error) {
    //     console.error('reCAPTCHA v2 error:', error);
    //     alert('reCAPTCHA verification failed. Please try again.');
        
    //     // Re-enable button
    //     const submitButton = form.querySelector('button[type="submit"]');
    //     if (submitButton) {
    //         submitButton.disabled = false;
    //         submitButton.innerHTML = '<i class="fa fa-recycle me-2"></i>Join Waste2Product';
    //     }
    // };
    
    // // reCAPTCHA v2 invisible - Callback d'expiration
    // window.onRecaptchaExpired = function() {
    //     console.log('reCAPTCHA v2 expired');
    //     if (typeof grecaptcha !== 'undefined') {
    //         grecaptcha.reset();
    //     }
    };
    
    // Gestion du popup d'erreur
    const errorPopup = document.querySelector('.error-popup');
    if (errorPopup) {
        // Auto-close after 8 seconds
        setTimeout(() => {
            if (errorPopup) {
                errorPopup.style.animation = 'slideOutRight 0.5s ease-in forwards';
                setTimeout(() => {
                    errorPopup.remove();
                }, 500);
            }
        }, 8000);
        
        // Fermeture manuelle
        const closeBtn = errorPopup.querySelector('.error-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                errorPopup.style.animation = 'slideOutRight 0.5s ease-in forwards';
                setTimeout(() => {
                    errorPopup.remove();
                }, 500);
            });
        }
    }
    
    // Gestion du popup de succès
    const successPopup = document.querySelector('.success-popup');
    if (successPopup) {
        // Auto-close after 5 seconds (faster than error)
        setTimeout(() => {
            if (successPopup) {
                successPopup.style.animation = 'slideOutRight 0.5s ease-in forwards';
                setTimeout(() => {
                    successPopup.remove();
                }, 500);
            }
        }, 5000);
        
        // Fermeture manuelle
        const closeBtn = successPopup.querySelector('.success-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                successPopup.style.animation = 'slideOutRight 0.5s ease-in forwards';
                setTimeout(() => {
                    successPopup.remove();
                }, 500);
            });
        }
    }
});
</script>

@endsection