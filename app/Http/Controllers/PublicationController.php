<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Commentaire;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PublicationController extends Controller
{
    /**
     * Afficher la liste paginée des publications
     */
    public function index()
    {
        $publications = Publication::with('user')->latest()->paginate(10);  // Liste paginée
        return view('publications.index', compact('publications'));
    }

    /**
     * Afficher le formulaire pour créer une nouvelle publication
     */
    public function create()
    {
        return view('publications.create');
    }

    /**
     * Enregistrer une nouvelle publication
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'categorie' => 'required|in:reemploi,reparation,transformation',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',  // Ou vidéo si vous ajoutez
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('publications', 'public');
        }

        Publication::create([
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'categorie' => $request->categorie,
            'image' => $imagePath,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('publications.index')->with('success', 'Publication créée !');
    }

    /**
     * Afficher une publication spécifique avec ses commentaires
     */
    public function show($id)
    {
        $publication = Publication::with(['user', 'commentaires.user'])->findOrFail($id);
        return view('publications.show', compact('publication'));
    }

    /**
     * Afficher le formulaire pour modifier une publication
     */
    public function edit($id)
    {
        $publication = Publication::findOrFail($id);

        // Vérifier que l'utilisateur est le propriétaire de la publication
        if ($publication->user_id !== Auth::id()) {
            return redirect()->route('publications.index')->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
        }

        return view('publications.edit', compact('publication'));
    }

    /**
     * Mettre à jour une publication existante
     */
    public function update(Request $request, $id)
    {
        $publication = Publication::findOrFail($id);

        // Vérifier que l'utilisateur est le propriétaire de la publication
        if ($publication->user_id !== Auth::id()) {
            return redirect()->route('publications.index')->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
        }

        $validator = Validator::make($request->all(), [
            'titre' => 'required|string|max:255',
            'contenu' => 'required|string',
            'categorie' => 'required|in:reemploi,reparation,transformation',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $imagePath = $publication->image; // Garder l'image existante par défaut
        if ($request->hasFile('image')) {
            // Supprimer l'ancienne image si elle existe
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            // Stocker la nouvelle image
            $imagePath = $request->file('image')->store('publications', 'public');
        }

        $publication->update([
            'titre' => $request->titre,
            'contenu' => $request->contenu,
            'categorie' => $request->categorie,
            'image' => $imagePath,
        ]);

        return redirect()->route('publications.show', $publication->id)->with('success', 'Publication mise à jour !');
    }

    /**
     * Supprimer une publication
     */
    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        // Vérifier que l'utilisateur est le propriétaire de la publication
        if ($publication->user_id !== Auth::id()) {
            return redirect()->route('publications.index')->with('error', 'Vous n\'êtes pas autorisé à supprimer cette publication.');
        }

        // Supprimer l'image associée si elle existe
        if ($publication->image && Storage::disk('public')->exists($publication->image)) {
            Storage::disk('public')->delete($publication->image);
        }

        // Supprimer la publication (les commentaires sont supprimés automatiquement grâce à onDelete('cascade'))
        $publication->delete();

        return redirect()->route('publications.index')->with('success', 'Publication supprimée !');
    }
}