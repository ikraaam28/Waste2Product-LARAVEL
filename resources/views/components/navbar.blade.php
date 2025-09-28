<!-- Navbar Start -->
<div class="container-fluid bg-white sticky-top">
    <div class="container">
        <nav class="navbar navbar-expand-lg bg-white navbar-light py-2 py-lg-0">
            <a href="{{ route('home') }}" class="navbar-brand">
                <img class="img-fluid" src="{{ asset('assets/img/recycleverse1.png') }}" alt="Logo">
            </a>
            <button type="button" class="navbar-toggler ms-auto me-0" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <div class="navbar-nav ms-auto">
                    <a href="{{ route('home') }}" class="nav-item nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-item nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About</a>
                    <a href="{{ route('products') }}" class="nav-item nav-link {{ request()->routeIs('products') ? 'active' : '' }}">Products</a>
                    <a href="{{ route('events') }}" class="nav-item nav-link {{ request()->routeIs('events') ? 'active' : '' }}">Events</a>
                    <a href="{{ route('tutos.index') }}" class="nav-item nav-link {{ request()->routeIs('tutos.*') ? 'active' : '' }}">Tutorials</a>
                    <a href="{{ route('publications.my') }}" class="nav-item nav-link {{ request()->routeIs('publications.my') ? 'active' : '' }}">Publications</a>
                    <a href="{{ route('partners.front') }}" class="nav-item nav-link {{ request()->routeIs('partners.front') ? 'active' : '' }}">Partners</a>
                    {{-- <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
                        <div class="dropdown-menu bg-light rounded-0 m-0">
                            <a href="{{ route('feature') }}" class="dropdown-item">Features</a>
                            <a href="{{ route('blog') }}" class="dropdown-item">Blog Article</a>
                            <a href="{{ route('testimonial') }}" class="dropdown-item">Testimonial</a>
                            <a href="#" class="dropdown-item">404 Page</a>
                        </div>
                    </div> --}}
                    <a href="{{ route('contact') }}" class="nav-item nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                </div>
                <div class="border-start ps-4 d-none d-lg-block">
                    <button type="button" class="btn btn-sm p-0 me-3"><i class="fa fa-search"></i></button>
                    @guest
                        <a href="{{ route('signup') }}" class="btn btn-primary btn-sm rounded-pill px-3">Sign Up</a>
                    @endguest
                    @auth
                        <!-- Profile Picture Button -->
                        <a href="{{ route('profile') }}" class="btn btn-sm p-0 me-3" style="border-radius: 50%; width: 35px; height: 35px; overflow: hidden; border: 2px solid #e9ecef;">
                            @if(auth()->user()->profile_picture)
                                <img src="{{ asset('storage/' . auth()->user()->profile_picture) }}" 
                                     alt="Profile" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->first_name . ' ' . auth()->user()->last_name) }}&background=2E7D32&color=fff&size=35" 
                                     alt="Avatar" 
                                     class="img-fluid rounded-circle" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            @endif
                        </a>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3">
                                <i class="fa fa-power-off me-1"></i> Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>
    </div>
</div>
<!-- Navbar End -->