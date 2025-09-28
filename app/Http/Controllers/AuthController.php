<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;  
use Illuminate\Support\Str;       
use App\Mail\ResetPasswordEmail;

use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire d'inscription
     */
    public function signup()
    {
        return view('auth.signup');
    }

    /**
     * Gérer l'enregistrement d'un nouvel utilisateur
     */
    public function store(Request $request)
    {
        // Debug: Afficher les données reçues
        \Log::info('reCAPTCHA data received:', [
            'g-recaptcha-response' => $request->input('g-recaptcha-response'),
            'all_inputs' => $request->all()
        ]);
        
        // For now, let's skip reCAPTCHA verification to test if the form works
        // We'll add it back once we confirm the basic functionality works
        \Log::info('Skipping reCAPTCHA verification for testing');

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'city' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'terms_accepted' => 'required|accepted',
        ], [
            'first_name.required' => 'Le prénom est requis.',
            'last_name.required' => 'Le nom est requis.',
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.unique' => 'Cette adresse email est déjà enregistrée. Veuillez utiliser une autre adresse ou essayer de vous connecter.',
            'phone.required' => 'Le numéro de téléphone est requis.',
            'city.required' => 'Veuillez sélectionner votre ville.',
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'terms_accepted.required' => 'Vous devez accepter les conditions générales.',
            'terms_accepted.accepted' => 'Vous devez accepter les conditions générales.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'city' => $request->city,
            'password' => Hash::make($request->password),
            'newsletter_subscription' => $request->has('newsletter_subscription'),
            'terms_accepted' => true,
            'role' => 'user',
        ]);

        // Envoyer l'email de bienvenue
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas bloquer l'inscription
            \Log::error('Erreur envoi email bienvenue: ' . $e->getMessage());
        }

        return redirect()->route('login')->with('success', 'Compte créé avec succès ! Veuillez vous connecter pour continuer.');
    }

  
 public function login()
{
    return view('auth.login');
}


public function authenticate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required',
    ], [
        'email.required' => 'The email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'password.required' => 'The password is required.',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        \Log::info('Utilisateur connecté : ', [
            'email' => Auth::user()->email,
            'role' => Auth::user()->role,
            'isAdmin' => Auth::user()->isAdmin(),
        ]);

        if (Auth::user()->isAdmin()) {
            \Log::info('Redirection vers admin.dashboard');
            return redirect()->route('admin.dashboard')
                ->with('success', 'Welcome back, Admin!');
        }

        \Log::info('Redirection vers home');
        return redirect()->route('home')->with('success', 'Login successful! Welcome!');
    }

    return redirect()->back()
        ->withErrors(['email' => 'Les identifiants fournis sont incorrects.'])
        ->withInput();
}


public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('home')
        ->with('success', 'You have been logged out successfully.');
}
 

// Affiche formulaire "mot de passe oublié"
public function showForgotForm()
{
    return view('auth.forgot-password');
}

// Send reset password email
public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);
    
    $user = User::where('email', $request->email)->first();
    
    if (!$user) {
        return back()->withErrors(['email' => 'No user found with this email.']);
    }

    $token = \Str::random(64);

    // Save the token in password_resets table
    \DB::table('password_resets')->updateOrInsert(
        ['email' => $user->email],
        [
            'token' => $token,
            'created_at' => now()
        ]
    );

    // Send custom reset password email
    Mail::to($user->email)->send(new ResetPasswordEmail($user, $token));

    return back()->with('success', 'Reset password email sent!');
}

// Show reset password form
public function showResetForm($token)
{
    return view('auth.reset-password', ['token' => $token]);
}

// Update password
public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $record = \DB::table('password_resets')->where('email', $request->email)
                ->where('token', $request->token)
                ->first();

    if (!$record) {
        return back()->withErrors(['email' => 'Invalid token or email.']);
    }

    $user = User::where('email', $request->email)->first();
    $user->password = \Hash::make($request->password);
    $user->save();

    // Delete used token
    \DB::table('password_resets')->where('email', $request->email)->delete();

    return redirect()->route('login')->with('success', 'Password updated successfully!');
}

public function profile()
{
    $user = Auth::user(); // get the logged-in user
    
    // Get user's participated events
    $participatedEvents = $user->participatedEvents()
        ->whereNotNull('events.id') // Ensure event exists
        ->orderBy('events.created_at', 'desc')
        ->limit(6) // Limit to 6 recent events
        ->get();
    
    return view('auth.profile', compact('user', 'participatedEvents'));
}

public function updateProfilePicture(Request $request)
{
    $request->validate([
        'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    $user = Auth::user();

    if ($request->hasFile('profile_picture')) {
        // Store image in public storage
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $user->profile_picture = $path;
        $user->save();
    }

    return redirect()->route('profile')->with('success', 'Photo de profil mise à jour !');
}

/**
 * Update user profile
 */
public function updateProfile(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
        'city' => 'nullable|string|max:255',
        'newsletter_subscription' => 'sometimes|boolean',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
                         ->withErrors($validator)
                         ->withInput();
    }

    $user->update([
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'city' => $request->city,
        'newsletter_subscription' => $request->has('newsletter_subscription'),
    ]);

    return redirect()->route('profile')->with('success', 'Profil mis à jour avec succès !');
}

/**
 * Redirect to Google OAuth
 */
public function redirectToGoogle()
{
    return Socialite::driver('google')->redirect();
}

/**
 * Handle Google OAuth callback
 */
public function handleGoogleCallback()
{
    try {
        $googleUser = Socialite::driver('google')->user();

        // Check if user already exists with this email
        $existingUser = User::where('email', $googleUser->getEmail())->first();

        if ($existingUser) {
            // Update Google ID if not set
            if (!$existingUser->google_id) {
                $existingUser->update([
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            Auth::login($existingUser);
            return redirect()->intended('/')->with('success', 'Connexion réussie avec Google !');
        }

        // Create new user
        $user = User::create([
            'first_name' => $googleUser->user['given_name'] ?? explode(' ', $googleUser->getName())[0],
            'last_name' => $googleUser->user['family_name'] ?? (explode(' ', $googleUser->getName())[1] ?? ''),
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'email_verified_at' => now(),
            'terms_accepted' => true,
            'password' => Hash::make(Str::random(24)), // Random password for Google users
        ]);

        // Send welcome email
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email bienvenue Google: ' . $e->getMessage());
        }

        Auth::login($user);
        return redirect()->intended('/')->with('success', 'Compte créé avec succès via Google ! Bienvenue sur Waste2Product !');

    } catch (\Exception $e) {
        \Log::error('Google OAuth Error: ' . $e->getMessage());
        return redirect()->route('login')->with('error', 'Erreur lors de la connexion avec Google. Veuillez réessayer.');
    }
}

public function redirectToResetPassword()
{
    $user = Auth::user();

    // Générer un token aléatoire
    $token = \Str::random(64);

    // Sauvegarder ou mettre à jour le token dans la table password_resets
    \DB::table('password_resets')->updateOrInsert(
        ['email' => $user->email],
        [
            'token' => $token,
            'created_at' => now(),
        ]
    );

    // Rediriger vers la page reset avec le token
    return redirect()->route('password.reset', ['token' => $token]);
}
}