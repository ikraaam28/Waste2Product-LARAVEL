<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

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
        ]);

        // Envoyer l'email de bienvenue
        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            // Log l'erreur mais ne pas bloquer l'inscription
            \Log::error('Erreur envoi email bienvenue: ' . $e->getMessage());
        }

        return redirect()->route('signup')->with('success', 'Compte créé avec succès ! Bienvenue sur Waste2Product !');
    }

    /**
     * Afficher le formulaire de login
     */
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Gérer la soumission du formulaire de login
     */
    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'password.required' => 'Le mot de passe est requis.',
        ]);

        if ($validator->fails()) {
            return redirect()->back(index)
                ->withErrors($validator)
                ->withInput();
        }

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('')
                ->with('success', 'Connexion réussie ! Bienvenue !');
        }

        return redirect()->back()
            ->withErrors(['email' => 'Les identifiants fournis sont incorrects.'])
            ->withInput();
    }

    /**
     * Déconnexion de l'utilisateur
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }
 
}