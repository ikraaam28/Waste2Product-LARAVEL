<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentaireController extends Controller
{
    public function store(Request $request, $publicationId)
    {
        $validator = Validator::make($request->all(), [
            'contenu' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        Commentaire::create([
            'contenu' => $request->contenu,
            'user_id' => Auth::id(),
            'publication_id' => $publicationId,
        ]);

        return redirect()->back()->with('success', 'Commentaire ajouté !');
    }

    // Ajoutez destroy si besoin (pour delete un commentaire)
}