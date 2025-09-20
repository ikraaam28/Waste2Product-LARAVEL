<!-- Navbar Start -->
<div class="container-fluid bg-white sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-white navbar-light py-2 py-lg-0">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img class="img-fluid" src="{{ asset('assets/img/logo.png') }}" alt="Logo">
            </a>
            <button type="button" class="navbar-toggler ms-auto me-0" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto">
                    <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                    <a href="{{ route('products') }}" class="nav-item nav-link {{ request()->routeIs('products') ? 'active' : '' }}">Products</a>
                    <a href="{{ route('store') }}" class="nav-item nav-link {{ request()->routeIs('store') ? 'active' : '' }}">Store</a>
                    <a href="{{ route('events') }}" class="nav-item nav-link {{ request()->routeIs('events') ? 'active' : '' }}">Events</a>
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu bg-light rounded-0 m-0">
                            <a href="{{ route('feature') }}" class="dropdown-item">Features</a>
                            <a href="{{ route('blog') }}" class="dropdown-item">Blog Article</a>
                            <a href="{{ route('testimonial') }}" class="dropdown-item">Testimonial</a>
                            <a href="#" class="dropdown-item">404 Page</a>
                        </div>
                    </div>
                    <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                </div>
                <div class="border-start ps-4 d-none d-lg-block">
                    <button type="button" class="btn btn-sm p-0 me-3"><i class="fa fa-search"></i></button>
                    @guest
                        <a href="{{ route('signup') }}" class="btn btn-primary btn-sm rounded-pill px-3">Sign Up</a>
                    @endguest
                    @auth
                        <!-- User Dropdown -->
                        <div class="nav-item dropdown">
                            <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" data-bs-toggle="dropdown">
                                @if(auth()->user()->profile_picture)
                                    <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                         alt="Profile" 
                                         class="rounded-circle me-2" 
                                         style="width: 30px; height: 30px; object-fit: cover;">
                                @else
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->full_name) }}&background=2E7D32&color=fff&size=30" 
                                         alt="Avatar" 
                                         class="rounded-circle me-2" 
                                         style="width: 30px; height: 30px; object-fit: cover;">
                                @endif
                                {{ auth()->user()->first_name }}
                            </a>
                            <div class="dropdown-menu bg-light rounded-0 m-0">
                                <a href="{{ route('profile') }}" class="dropdown-item">
                                    <i class="fa fa-user me-2"></i>Profile
                                </a>
                                <a href="{{ route('my-events') }}" class="dropdown-item">
                                    <i class="fa fa-calendar me-2"></i>My Events
                                </a>
                                <div class="dropdown-divider"></div>
                                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fa fa-power-off me-2"></i>Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar End -->