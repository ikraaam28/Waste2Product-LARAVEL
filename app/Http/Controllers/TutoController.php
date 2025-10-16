<?php

namespace App\Http\Controllers;

use App\Models\Tuto;
use App\Models\Category;
use App\Models\Question;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\QuizAttempt;
class TutoController extends Controller
{
    public function index(Request $request)
    {
        $query = Tuto::with(['user', 'category'])
            ->withCount(['likes', 'dislikes'])
            ->where('is_published', true);

        // Filter by category_id
        if ($request->has('category_id') && $request->category_id !== '') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by media type
        if ($request->has('media_type') && in_array($request->media_type, ['photo', 'video'])) {
            $mediaType = $request->media_type;
            $query->where(function ($q) use ($mediaType) {
                if ($mediaType === 'photo') {
                    $q->whereJsonContains('media', ['mime_type' => 'image/jpeg'])
                      ->orWhereJsonContains('media', ['mime_type' => 'image/png']);
                } elseif ($mediaType === 'video') {
                    $q->whereJsonContains('media', ['mime_type' => 'video/mp4']);
                }
            });
        }

        $tutos = $query->latest()->get();
        $categories = Category::active()->ordered()->get(); // Fetch categories for filter dropdown

        return view('tutos.index', compact('tutos', 'categories'));
    }

public function show(Tuto $tuto)
    {
        if (!$tuto->is_published && !Auth::check()) {
            abort(403, 'This tutorial is not published.');
        }

        $tuto->increment('views');
        $tuto->load(['user', 'category', 'questions.replies.user'])->loadCount(['likes', 'dislikes']);

        $likes = $tuto->reactions()->where('type', 'like')->count();
        $dislikes = $tuto->reactions()->where('type', 'dislike')->count();

        $userReaction = Auth::check()
            ? $tuto->reactions()->where('user_id', Auth::id())->first()
            : null;

        $allReactions = (Auth::check() && Auth::user()->role === 'admin')
            ? $tuto->reactions()->with('user')->get()
            : collect();

        // Load quizzes related to this tutorial with attempts for the authenticated user
        $quizzes = $tuto->quizzes()->with(['attempts' => function ($query) {
            if (Auth::check()) {
                $query->where('user_id', Auth::id());
            }
        }])->get();

        // Calculate progress and average percentage for the user
        $userAttempts = QuizAttempt::where('user_id', Auth::id())
            ->whereIn('quiz_id', $tuto->quizzes->pluck('id'))
            ->get();
        $completedQuizzes = $userAttempts->count();
        $totalQuizzes = $tuto->quizzes->count();
        $averagePercentage = $userAttempts->isEmpty() ? 0 : $userAttempts->avg('percentage');

        return view('tutos.show', compact('tuto', 'userReaction', 'likes', 'dislikes', 'allReactions', 'quizzes', 'averagePercentage', 'completedQuizzes', 'totalQuizzes'));
    }

    
    public function adminIndex()
    {
        $tutos = Tuto::with(['user', 'category'])->latest()->get();
        return view('admin.tuto.index', compact('tutos'));
    }

    public function adminShow(Tuto $tuto)
    {
        $tuto->increment('views');
        $tuto->load(['user', 'category', 'questions.replies.user']);

        $allReactions = $tuto->reactions()->with('user')->get();

        return view('admin.tuto.show', compact('tuto', 'allReactions'));
    }

    public function create()
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.tuto.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
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
            'category_id' => $validated['category_id'],
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
        $categories = Category::active()->ordered()->get();
        return view('admin.tuto.edit', compact('tuto', 'categories'));
    }

    public function update(Request $request, Tuto $tuto)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
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
            'category_id' => $validated['category_id'],
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