<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Commentaire;
use App\Models\PublicationReaction; // ADD THIS IMPORT
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PublicationController extends Controller
{
    /**
     * Afficher la liste paginée des publications
     */
    public function index()
    {
        $publications = Publication::with(['user', 'publicationReactions'])->latest()->paginate(10);
        return view('publications.index', compact('publications'));
    }

    public function myPublications()
    {
        $userId = Auth::id();
        
        if (!$userId) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté');
        }

        $myPublications = Publication::with(['user', 'publicationReactions'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $otherPublications = Publication::with(['user', 'publicationReactions'])
            ->where('user_id', '!=', $userId)
            ->latest()
            ->paginate(10);

        return view('publications.my-publications', compact('myPublications', 'otherPublications'));
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
            'categorie' => 'required|in:reemployment,repair,transformation',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
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

        return redirect()->route('publications.my')->with('success', 'Publication créée !');
    }

    /**
     * Afficher une publication spécifique avec ses commentaires
     */
    public function show($id)
    {
        try {
            $publication = Publication::with([
                'user', 
                'commentaires.user', 
                'publicationReactions' => function($query) {
                    return $query->with('user');
                }
            ])->findOrFail($id);
            
            return view('publications.show', compact('publication'));
        } catch (\Exception $e) {
            \Log::error('Show publication error: ' . $e->getMessage());
            return redirect()->route('publications.index')->with('error', 'Publication non trouvée');
        }
    }

    /**
     * Afficher le formulaire pour modifier une publication
     */
    public function edit($id)
    {
        try {
            $publication = Publication::findOrFail($id);

            if ($publication->user_id !== Auth::id()) {
                return redirect()->route('publications.index')->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
            }

            return view('publications.edit', compact('publication'));
        } catch (\Exception $e) {
            \Log::error('Edit publication error: ' . $e->getMessage());
            return redirect()->route('publications.index')->with('error', 'Publication non trouvée');
        }
    }

    /**
     * Mettre à jour une publication existante
     */
    public function update(Request $request, $id)
    {
        try {
            $publication = Publication::findOrFail($id);

            if ($publication->user_id !== Auth::id()) {
                return redirect()->route('publications.my')->with('error', 'Vous n\'êtes pas autorisé à modifier cette publication.');
            }

            $validator = Validator::make($request->all(), [
                'titre' => 'required|string|max:255',
                'contenu' => 'required|string',
                'categorie' => 'required|in:reemployment,repair,transformation',
                'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $imagePath = $publication->image;
            if ($request->hasFile('image')) {
                if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                    Storage::disk('public')->delete($imagePath);
                }
                $imagePath = $request->file('image')->store('publications', 'public');
            }

            $publication->update([
                'titre' => $request->titre,
                'contenu' => $request->contenu,
                'categorie' => $request->categorie,
                'image' => $imagePath,
            ]);

            return redirect()->route('publications.my')->with('success', 'Publication mise à jour !');
        } catch (\Exception $e) {
            \Log::error('Update publication error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour');
        }
    }

    public function destroy($id)
    {
        try {
            $publication = Publication::findOrFail($id);

            if ($publication->user_id !== Auth::id()) {
                return redirect()->route('publications.my')->with('error', 'Vous n\'êtes pas autorisé à supprimer cette publication.');
            }

            if ($publication->image && Storage::disk('public')->exists($publication->image)) {
                Storage::disk('public')->delete($publication->image);
            }

            $publication->delete();

            return redirect()->route('publications.my')->with('success', 'Publication supprimée !');
        } catch (\Exception $e) {
            \Log::error('Destroy publication error: ' . $e->getMessage());
            return redirect()->route('publications.my')->with('error', 'Erreur lors de la suppression');
        }
    }

    /**
     * Afficher la liste de toutes les publications pour l'admin
     */
    public function adminIndex(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $query = Publication::with('user')->whereHas('user');

        if ($request->filled('category')) {
            $query->where('categorie', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
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

        $publications = $query->latest()->paginate(10)->appends($request->query());

        $stats = [
            'total_publications' => Publication::count(),
            'category_distribution' => Publication::groupBy('categorie')
                ->select('categorie', DB::raw('count(*) as count'))
                ->pluck('count', 'categorie')
                ->toArray(),
            'recent_publications' => Publication::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        return view('admin.publications.index', compact('publications', 'stats'));
    }

    /**
     * Export publications to CSV
     */
    public function exportCsv()
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $publications = Publication::with('user')->get();

        $filename = 'publications-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($publications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Title', 'Author', 'Category', 'Content', 'Status', 'Created At']);

            foreach ($publications as $publication) {
                fputcsv($file, [
                    $publication->id,
                    $publication->titre,
                    $publication->user ? $publication->user->first_name . ' ' . $publication->user->last_name : 'Deleted User',
                    $publication->categorie,
                    substr($publication->contenu, 0, 100) . '...',
                    $publication->status ?? 'published',
                    $publication->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete a single publication (Admin)
     */
    public function adminDestroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        try {
            \Log::info("Attempting to delete publication with ID: {$id}");
            $publication = Publication::findOrFail($id);
            
            if ($publication->image && Storage::disk('public')->exists($publication->image)) {
                Storage::disk('public')->delete($publication->image);
            }
            
            $publication->delete();
            
            return redirect()->route('admin.publications.index')->with('success', 'Publication supprimée !');
        } catch (\Exception $e) {
            \Log::error('Admin destroy publication error: ' . $e->getMessage());
            return redirect()->route('admin.publications.index')->with('error', 'Erreur lors de la suppression');
        }
    }
}