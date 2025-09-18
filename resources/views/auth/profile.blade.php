@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Mon Profil</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body d-flex flex-column align-items-center text-center">
            
            <!-- Profile Picture -->
            <div class="mb-3">
                @if($user->profile_picture)
                <img src="{{ asset('storage/' . $user->profile_picture) }}" 
     alt="Photo de profil" 
     class="rounded-circle border shadow"
     width="150" height="150" 
     style="object-fit: cover;">

                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->full_name) }}&background=0D8ABC&color=fff" 
                         alt="Default Avatar" 
                         class="rounded-circle border shadow"
                         width="150" height="150">
                @endif
            </div>

            <!-- Upload form -->
            <form action="{{ route('profile.picture.update') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                @csrf
                <div class="input-group">
                    <input type="file" name="profile_picture" class="form-control" accept="image/*" required>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
                @error('profile_picture')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </form>

            <!-- User Info -->
            <h4 class="mb-3">{{ $user->full_name }}</h4>
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Téléphone:</strong> {{ $user->phone }}</p>
            <p><strong>Ville:</strong> {{ $user->city }}</p>
            <p><strong>Abonné à la newsletter:</strong> {{ $user->newsletter_subscription ? 'Oui' : 'Non' }}</p>
            <p><strong>Conditions acceptées:</strong> {{ $user->terms_accepted ? 'Oui' : 'Non' }}</p>
            <p><strong>Membre depuis:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Logout -->
    <a href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
       class="btn btn-danger mt-4">
       Déconnexion
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
</div>
@endsection
