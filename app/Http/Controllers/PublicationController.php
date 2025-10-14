<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Publication;
use App\Models\Commentaire;
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
        $publications = Publication::with('user')->latest()->paginate(10);
        return view('publications.index', compact('publications'));
    }

    public function myPublications()
    {
        $myPublications = Publication::with('user')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        $otherPublications = Publication::with('user')
            ->where('user_id', '!=', Auth::id())
            ->latest()
            ->paginate(10);

        return view('publications.my-publications', compact('myPublications', 'otherPublications'));
    }

    /**
     * Afficher le formulaire pour créer une nouvelle publication
     */
    public function create()
    {
        if (Auth::check() && Auth::user()->isBanned()) {
            return redirect()->route('publications.my')->with('error', 'Vous êtes banni et ne pouvez pas créer de publication.');
        }
        return view('publications.create');
    }

    /**
     * Enregistrer une nouvelle publication
     */
    public function store(Request $request)
    {
        if (Auth::user()->isBanned()) {
            return redirect()->route('publications.my')->with('error', 'Vous êtes banni et ne pouvez pas créer de publication.');
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
        $publication = Publication::with(['user', 'commentaires.user'])->findOrFail($id);
        return view('publications.show', compact('publication'));
    }

    /**
     * Afficher le formulaire pour modifier une publication
     */
    public function edit($id)
    {
        $publication = Publication::findOrFail($id);

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
    }

    public function destroy($id)
    {
        $publication = Publication::findOrFail($id);

        if ($publication->user_id !== Auth::id()) {
            return redirect()->route('publications.my')->with('error', 'Vous n\'êtes pas autorisé à supprimer cette publication.');
        }

        if ($publication->image && Storage::disk('public')->exists($publication->image)) {
            Storage::disk('public')->delete($publication->image);
        }

        $publication->delete();

        return redirect()->route('publications.my')->with('success', 'Publication supprimée !');
    }

    /**
     * Afficher la liste de toutes les publications pour l'admin avec filtres et statistiques
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
            'monthly_trends' => Publication::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('count', 'month')
                ->toArray(),
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
                    $publication->contenu,
                    $publication->status ?? 'published',
                    $publication->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

  /**
     * Ban a user and delete their publications
     */
    public function adminBanUser($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        $publication = Publication::with('user')->findOrFail($id);
        $user = $publication->user;

        if ($user) {
            // Ban the user by setting banned_at timestamp
            $user->update(['banned_at' => now()]);

            // Delete all publications by the banned user
            $user->publications()->each(function ($pub) {
                if ($pub->image && Storage::disk('public')->exists($pub->image)) {
                    Storage::disk('public')->delete($pub->image);
                }
                $pub->delete();
            });

            // Log out the banned user if they are currently authenticated
            if (Auth::id() === $user->id) {
                Auth::logout();
                return redirect()->route('login')->with('success', 'You have been banned and logged out.');
            }

            \Log::info("User ID {$user->id} banned and publications deleted.");
        }

        return redirect()->route('admin.publications.index')->with('success', 'User banned and their publications deleted!');
    }

    /**
     * Delete a single publication
     */
    public function adminDestroy($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized. Admin access required.');
        }

        \Log::info("Attempting to delete publication with ID: {$id}");
        $publication = Publication::findOrFail($id);
        \Log::info("Publication found: " . json_encode($publication));

        if ($publication->image && Storage::disk('public')->exists($publication->image)) {
            Storage::disk('public')->delete($publication->image);
        }
        $publication->delete();

        return redirect()->route('admin.publications.index')->with('success', 'Publication deleted!');
    }
}