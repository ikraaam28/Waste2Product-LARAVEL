<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function signup()
    {
        return view('auth.signup');
    }

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
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email address is already registered. Please use a different email or try logging in.',
            'phone.required' => 'Phone number is required.',
            'city.required' => 'Please select your city.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 8 characters long.',
            'password.confirmed' => 'Password confirmation does not match.',
            'terms_accepted.required' => 'You must accept the terms and conditions.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
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

        return redirect()->route('signup')->with('success', 'Account created successfully! Welcome to Waste2Product!');
    }

}
