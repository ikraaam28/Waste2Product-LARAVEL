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
                                
                                @if(session('success'))
                                    <div class="alert alert-success alert-dismissible fade show rounded-pill" role="alert" style="background: linear-gradient(135deg, #28a745, #20c997); border: none; color: white;">
                                        <i class="fa fa-check-circle me-2"></i>
                                        <strong>{{ session('success') }}</strong>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                                
                                @if($errors->any())
                                    <div class="alert alert-danger alert-dismissible fade show rounded-pill" role="alert" style="background: linear-gradient(135deg, #dc3545, #c82333); border: none; color: white;">
                                        <i class="fa fa-exclamation-triangle me-2"></i>
                                        <strong>Please correct the following errors:</strong>
                                        <ul class="mb-0 mt-2">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                                    </div>
                                @endif
                            </div>
                            
                            <form method="POST" action="{{ route('signup.store') }}">
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
                                    <div class="col-12">
                                        <div class="form-check p-3 bg-light rounded-3">
                                            <input class="form-check-input" type="checkbox" id="newsletter" name="newsletter_subscription" value="1" style="transform: scale(1.2);">
                                            <label class="form-check-label ms-2" for="newsletter">
                                                <i class="fa fa-bell me-2 text-primary"></i>Subscribe to our newsletter for recycling tips and eco-news
                                            </label>
                                        </div>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const inputs = form.querySelectorAll('input, select');
    
    // Fonction pour valider un champ
    function validateField(field) {
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
                if (value.length < 2) {
                    isValid = false;
                    errorMessage = 'Must be at least 2 characters long';
                }
                break;
            case 'email':
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    isValid = false;
                    errorMessage = 'Please enter a valid email address';
                }
                break;
            case 'phone':
                if (value.length < 8) {
                    isValid = false;
                    errorMessage = 'Phone number must be at least 8 digits';
                }
                break;
            case 'city':
                if (!value) {
                    isValid = false;
                    errorMessage = 'Please select your city';
                }
                break;
            case 'password':
                if (value.length < 8) {
                    isValid = false;
                    errorMessage = 'Password must be at least 8 characters long';
                }
                break;
            case 'password_confirmation':
                const password = form.querySelector('input[name="password"]').value;
                if (value !== password) {
                    isValid = false;
                    errorMessage = 'Password confirmation does not match';
                }
                break;
        }
        
        // Afficher l'erreur si nécessaire
        if (!isValid) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'field-error text-danger small mt-1';
            errorDiv.innerHTML = `<i class="fa fa-exclamation-circle me-1"></i>${errorMessage}`;
            field.parentNode.appendChild(errorDiv);
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
        
        return isValid;
    }
    
    // Fonction pour vérifier si tous les champs sont valides
    function checkFormValidity() {
        let allValid = true;
        
        // Vérifier tous les champs requis
        const requiredFields = ['first_name', 'last_name', 'email', 'phone', 'city', 'password', 'password_confirmation'];
        requiredFields.forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field && !validateField(field)) {
                allValid = false;
            }
        });
        
        // Vérifier les termes et conditions
        const termsCheckbox = form.querySelector('input[name="terms_accepted"]');
        if (termsCheckbox && !termsCheckbox.checked) {
            allValid = false;
        }
        
        // Activer/désactiver le bouton
        if (allValid) {
            submitBtn.disabled = false;
            submitBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.background = 'linear-gradient(135deg, #6c757d, #495057)';
        }
    }
    
    // Ajouter les événements de validation
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
            checkFormValidity();
        });
        
        input.addEventListener('input', function() {
            // Validation en temps réel pour certains champs
            if (['password', 'password_confirmation'].includes(this.name)) {
                validateField(this);
                // Re-valider la confirmation si on tape dans le mot de passe
                if (this.name === 'password') {
                    const confirmField = form.querySelector('input[name="password_confirmation"]');
                    if (confirmField.value) {
                        validateField(confirmField);
                    }
                }
            }
            checkFormValidity();
        });
    });
    
    // Validation initiale
    checkFormValidity();
});
</script>

@endsection
