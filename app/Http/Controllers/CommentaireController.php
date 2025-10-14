<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentaireController extends Controller
{
    /**
     * Stocker un nouveau commentaire
     */
    public function store(Request $request, $publicationId)
    {
        $publication = Publication::findOrFail($publicationId);

        $validator = Validator::make($request->all(), [
            'contenu' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:commentaires,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Vérifier si l'utilisateur répond à un commentaire de la bonne publication
        if ($request->parent_id) {
            $parentComment = Commentaire::findOrFail($request->parent_id);
            if ($parentComment->publication_id !== $publication->id) {
                return redirect()->back()->with('error', 'Commentaire parent invalide.');
            }
        }

        Commentaire::create([
            'contenu' => $request->contenu,
            'publication_id' => $publication->id,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('publications.show', $publication->id)
            ->with('success', 'Commentaire ajouté avec succès !');
    }

    /**
     * Afficher le formulaire d'édition d'un commentaire
     */
    public function edit($id)
    {
        $commentaire = Commentaire::with(['publication', 'user'])->findOrFail($id);

        // Vérifier l'autorisation
        if ($commentaire->user_id !== Auth::id()) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier ce commentaire.');
        }

        return view('commentaires.edit', compact('commentaire'));
    }

    /**
     * Mettre à jour un commentaire
     */
    public function update(Request $request, $id)
    {
        $commentaire = Commentaire::findOrFail($id);

        if ($commentaire->user_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'contenu' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $commentaire->update([
            'contenu' => $request->contenu,
        ]);

        return redirect()->route('publications.show', $commentaire->publication_id)
            ->with('success', 'Commentaire modifié avec succès !');
    }

    /**
     * Supprimer un commentaire
     */
public function destroy($id)
{
    $commentaire = Commentaire::findOrFail($id);

    if ($commentaire->user_id !== Auth::id()) {
        abort(403, 'Vous n\'êtes pas autorisé à supprimer ce commentaire.');
    }

    // Log for debugging
    \Log::info('Attempting to delete comment ID: ' . $id . ' with ' . $commentaire->replies()->count() . ' replies');

    // Delete all replies recursively
    $commentaire->replies()->delete();

    // Delete the comment itself
    $commentaire->delete();

    \Log::info('Comment ID: ' . $id . ' and its replies deleted successfully');

    return redirect()->route('publications.show', $commentaire->publication_id)
        ->with('success', 'Commentaire supprimé avec succès !');
}
}