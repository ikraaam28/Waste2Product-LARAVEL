<?php

namespace App\Http\Controllers;

use App\Models\Tuto;
use App\Models\Question;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TutoController extends Controller
{
    // Front-end: List all published tutorials
    public function index()
    {
        $tutos = Tuto::with('user')->where('is_published', true)->latest()->get();
        return view('tutos.index', compact('tutos'));
    }

    // Front-end: Show a single tutorial (user)
    public function show(Tuto $tuto)
    {
        if (!$tuto->is_published && !Auth::check()) {
            abort(403, 'This tutorial is not published.');
        }

        $tuto->increment('views'); 
        $tuto->load(['user', 'questions.replies.user']);

        // Nombre total de réactions
        $likes = $tuto->reactions()->where('type', 'like')->count();
        $dislikes = $tuto->reactions()->where('type', 'dislike')->count();

        // Réaction de l'utilisateur connecté
        $userReaction = Auth::check()
            ? $tuto->reactions()->where('user_id', Auth::id())->first()
            : null;

        // Admin voit toutes les réactions
        $allReactions = (Auth::check() && Auth::user()->role === 'admin')
            ? $tuto->reactions()->with('user')->get()
            : collect();

        return view('tutos.show', compact('tuto', 'userReaction', 'likes', 'dislikes', 'allReactions'));
    }

    // Admin: List all tutorials
    public function adminIndex()
    {
        $tutos = Tuto::with('user')->latest()->get();
        return view('admin.tuto.index', compact('tutos'));
    }

    // Admin: Show a single tutorial
    public function adminShow(Tuto $tuto)
    {
        $tuto->increment('views');
        $tuto->load(['user', 'questions.replies.user']);

        // Admin → liste des réactions
        $allReactions = $tuto->reactions()->with('user')->get();

        return view('admin.tuto.show', compact('tuto', 'allReactions'));
    }

    public function create()
    {
        return view('admin.tuto.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plastic,wood,paper,metal,glass,other',
            'steps' => 'required|array|min:1',
            'steps.*' => 'required|string|max:255',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
            'is_published' => 'required|boolean',
            'admin_notes' => 'nullable|string',
        ]);

        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('tutos_media', 'public');
                    $mediaPaths[] = [
                        'path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'thumbnail' => null,
                    ];
                    Log::info('Stored media file: ' . $path);
                } else {
                    Log::warning('Invalid file upload: ' . $file->getClientOriginalName());
                }
            }
        }

        Tuto::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'steps' => array_filter(array_map('trim', $validated['steps'])),
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
            'user_id' => Auth::id(),
            'is_published' => $validated['is_published'],
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.tutos.index')->with('success', 'Tutorial created successfully!');
    }

    public function edit(Tuto $tuto)
    {
        return view('admin.tuto.edit', compact('tuto'));
    }

    public function update(Request $request, Tuto $tuto)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plastic,wood,paper,metal,glass,other',
            'steps' => 'required|array|min:1',
            'steps.*' => 'required|string|max:255',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
            'is_published' => 'required|boolean',
            'admin_notes' => 'nullable|string',
        ]);

        $mediaPaths = $tuto->media ?? [];
        if ($request->hasFile('media')) {
            foreach ($tuto->media ?? [] as $media) {
                Storage::disk('public')->delete($media['path']);
            }
            $mediaPaths = [];
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('tutos_media', 'public');
                    $mediaPaths[] = [
                        'path' => $path,
                        'mime_type' => $file->getClientMimeType(),
                        'thumbnail' => null,
                    ];
                    Log::info('Stored media file: ' . $path);
                } else {
                    Log::warning('Invalid file upload: ' . $file->getClientOriginalName());
                }
            }
        }

        $tuto->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'steps' => array_filter(array_map('trim', $validated['steps'])),
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
            'is_published' => $validated['is_published'],
            'admin_notes' => $validated['admin_notes'],
        ]);

        return redirect()->route('admin.tutos.index')->with('success', 'Tutorial updated successfully!');
    }

    public function destroy(Tuto $tuto)
    {
        if ($tuto->media) {
            foreach ($tuto->media as $media) {
                Storage::disk('public')->delete($media['path']);
            }
        }
        $tuto->questions()->delete(); 
        $tuto->reactions()->delete(); 
        $tuto->delete();
        return redirect()->route('admin.tutos.index')->with('success', 'Tutorial deleted successfully!');
    }

    // Gestion des réactions (like/dislike)
    public function react(Request $request, Tuto $tuto)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        if (!Auth::check()) {
            return back()->with('error', 'You must be logged in to react.');
        }

        $existingReaction = Reaction::where('user_id', Auth::id())
            ->where('tuto_id', $tuto->id)
            ->first();

        if ($existingReaction) {
            if ($existingReaction->type === $request->type) {
                $existingReaction->delete(); 
            } else {
                $existingReaction->update(['type' => $request->type]); 
            }
        } else {
            Reaction::create([
                'user_id' => Auth::id(),
                'tuto_id' => $tuto->id,
                'type' => $request->type,
            ]);
        }

        return back()->with('success', 'Reaction updated!');
    }

    // Ajouter une question
    public function askQuestion(Request $request, Tuto $tuto)
    {
        $request->validate([
            'question_text' => 'required|string',
            'parent_id' => 'nullable|exists:questions,id',
        ]);

        Question::create([
            'user_id' => Auth::id(),
            'tuto_id' => $tuto->id,
            'question_text' => $request->question_text,
            'parent_id' => $request->parent_id,
        ]);

        return back()->with('success', $request->parent_id ? 'Reply added!' : 'Question asked!');
    }
}
