<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Admin
{
    public function handle(Request $request, Closure $next)
    {
        \Log::info('Middleware Admin : ', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email ?? 'non connecté',
            'role' => Auth::user()->role ?? 'non défini',
            'isAdmin' => Auth::user()->isAdmin() ?? false,
        ]);

        if (Auth::check() && Auth::user()->isAdmin()) {
            return $next($request);
        }

        return redirect()->route('home')->with('error', 'Accès non autorisé. Vous devez être administrateur.');
    }
}