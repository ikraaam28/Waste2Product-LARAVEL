<?php

namespace App\Http\Controllers;

use App\Models\Tuto;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class TutoController extends Controller
{
    public function index()
    {
        $tutos = Tuto::with('user')->latest()->get();
        return view('tutos.index', compact('tutos'));
    }

    public function show(Tuto $tuto)
    {
        $tuto->increment('views');
        $tuto->load(['user', 'questions.replies.user']);
        return view('tutos.show', compact('tuto'));
    }

    public function create()
    {
        return view('tutos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:plastique,bois,papier,metal,verre,autre',
            'steps' => 'required|array|min:1',
            'steps.*' => 'required|string|max:255',
            'media.*' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240', // 10MB max per file
        ]);

        $mediaPaths = [];
        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('media', 'public');
                    $mediaPaths[] = $path; // Store relative path (e.g., "media/filename.mp4")
                    $fullPath = storage_path('app/public/' . $path);
                    Log::info('Stored media: ' . $fullPath);
                    if (!file_exists($fullPath)) {
                        Log::warning('File not found after storage: ' . $fullPath);
                    }
                } else {
                    Log::warning('Invalid file upload: ' . $file->getClientOriginalName());
                }
            }
        } else {
            Log::info('No media files uploaded');
        }

        $tuto = Tuto::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'steps' => array_filter(array_map('trim', $validated['steps'])),
            'media' => !empty($mediaPaths) ? $mediaPaths : null,
            'user_id' => Auth::id(),
        ]);

        Log::info('Created tuto ID: ' . $tuto->id . ', Media: ' . json_encode($mediaPaths));

        return redirect()->route('tutos.show', $tuto)->with('success', 'Tutorial created successfully!');
    }

    public function react(Request $request, Tuto $tuto)
    {
        $request->validate([
            'type' => 'required|in:like,dislike',
        ]);

        $type = $request->type;
        $tuto->increment($type === 'like' ? 'likes_count' : 'dislikes_count');

        return back()->with('success', 'Reaction recorded!');
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