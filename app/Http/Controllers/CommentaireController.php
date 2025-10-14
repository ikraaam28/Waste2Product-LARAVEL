<?php

namespace App\Http\Controllers;

use App\Models\Commentaire;
use App\Models\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB; 

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
/**
     * Afficher la liste de tous les commentaires pour l'admin avec filtres et statistiques
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        // Initialize query
        $query = Commentaire::with(['user', 'publication'])->whereHas('user');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('contenu', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Paginate results
        $commentaires = $query->latest()->paginate(10)->appends($request->query());

        // Statistics
        $stats = [
            'total_commentaires' => Commentaire::count(),
            'publication_distribution' => Commentaire::groupBy('publication_id', 'publications.titre') // Fixed GROUP BY
                ->select('publication_id', DB::raw('count(*) as count'), DB::raw('COALESCE(publications.titre, "Unknown Publication") as titre'))
                ->leftJoin('publications', 'commentaires.publication_id', '=', 'publications.id')
                ->pluck('count', 'titre')
                ->toArray(),
            'recent_commentaires' => Commentaire::where('created_at', '>=', now()->subDays(30))->count(),
            'monthly_trends' => Commentaire::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
        ];

        return view('admin.commentaires.index', compact('commentaires', 'stats'));
    }

    /**
     * Delete a single commentaire
     */
    public function adminDestroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        \Log::info("Attempting to delete commentaire with ID: {$id}");
        $commentaire = Commentaire::findOrFail($id);
        \Log::info("Commentaire found: " . json_encode($commentaire));

        // No image to delete for comments, but ensure replies are handled
        $commentaire->replies()->delete(); // Delete all replies recursively
        $commentaire->delete();

        return redirect()->route('admin.commentaires.index')->with('success', 'Commentaire deleted!');
    }



    /**
     * Export commentaires to CSV
     */
    public function exportCsv()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $commentaires = Commentaire::with(['user', 'publication'])->get();

        $filename = 'commentaires-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($commentaires) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Author', 'Content', 'Publication', 'Created At']);

            foreach ($commentaires as $commentaire) {
                fputcsv($file, [
                    $commentaire->id,
                    $commentaire->user ? $commentaire->user->full_name : 'Deleted User',
                    $commentaire->contenu,
                    $commentaire->publication ? $commentaire->publication->titre : 'Deleted Publication',
                    $commentaire->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

}