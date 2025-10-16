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
use GuzzleHttp\Client;


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

    public function uploadCertificate(Tuto $tuto)
    {
        // Check eligibility (all quizzes completed and average >= 70%)
        $userAttempts = QuizAttempt::where('user_id', Auth::id())
            ->whereIn('quiz_id', $tuto->quizzes->pluck('id'))
            ->get();
        $completedQuizzes = $userAttempts->count();
        $totalQuizzes = $tuto->quizzes->count();
        $averagePercentage = $userAttempts->isEmpty() ? 0 : $userAttempts->avg('percentage');

        if ($completedQuizzes !== $totalQuizzes || $averagePercentage < 70) {
            return redirect()->route('tutos.show', $tuto)->with('error', 'You are not eligible for a certificate.');
        }

        return view('certificates.upload', compact('tuto'));
    }

public function generateCertificate(Request $request, Tuto $tuto)
{
    // 🔹 Vérifier l'éligibilité
    $userAttempts = QuizAttempt::where('user_id', Auth::id())
        ->whereIn('quiz_id', $tuto->quizzes->pluck('id'))
        ->get();
    $completedQuizzes = $userAttempts->count();
    $totalQuizzes = $tuto->quizzes->count();
    $averagePercentage = $userAttempts->isEmpty() ? 0 : $userAttempts->avg('percentage');

    if ($completedQuizzes !== $totalQuizzes || $averagePercentage < 70) {
        return redirect()->route('tutos.show', $tuto)
            ->with('error', 'You are not eligible for a certificate.');
    }

    // 🔹 Données utilisateur
    $userName = Auth::user()->name;

    // 🔹 Convertir le logo en Base64
    $logoPath = public_path('assets/img/recycleverse1.png');
    if (!file_exists($logoPath)) {
        return redirect()->route('tutos.show', $tuto)
            ->with('error', 'Logo file not found.');
    }
    $logoData = base64_encode(file_get_contents($logoPath));
    $logoBase64 = "data:image/png;base64,{$logoData}";

    // 🔹 Préparer le HTML du certificat
    $html = "
    <html>
    <head>
        <style>
            body { font-family: 'DejaVu Sans', sans-serif; text-align: center; color: #333; background: #fff; }
            .certificate { width: 80%; margin: 50px auto; border: 10px solid #004c99; padding: 20px; }
            .logo { max-width: 150px; margin-bottom: 20px; }
            h1 { font-size: 2.5em; color: #004c99; }
            h2 { font-size: 1.5em; color: #004c99; }
            p { font-size: 1.2em; margin: 5px 0; }
            .app-name { font-weight: bold; font-size: 1.1em; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='certificate'>
            <img class='logo' src='{$logoBase64}' alt='RecycleVerse Logo'>
            <h1>Certificate of Participation</h1>
            <h2>Congratulations!</h2>
            <p>This certifies that</p>
            <p><strong>{$userName}</strong></p>
            <p>has successfully participated in the tutorial</p>
            <p><strong>{$tuto->title}</strong></p>
            <p>with an average score of <strong>".number_format($averagePercentage, 2)."%</strong>.</p>
            <p>Issued on: ".date('F d, Y')."</p>
            <p class='app-name'>Proudly presented by <strong>RecycleVerse</strong></p>
        </div>
    </body>
    </html>
    ";

    try {
        // 🔹 Appel API PDF.co
        $client = new Client(['verify' => false]);
        $apiKey = env('PDFCO_API_KEY');

        $response = $client->post('https://api.pdf.co/v1/pdf/convert/from/html', [
            'headers' => [
                'x-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'html' => $html,
                'name' => 'certificate_' . Auth::id() . '_' . time() . '.pdf',
                'inline' => true,
                'margins' => ['top'=>10,'bottom'=>10,'left'=>10,'right'=>10],
            ],
        ]);

        $result = json_decode($response->getBody(), true);

        if (!isset($result['url']) || $result['error']) {
            Log::error('PDF.co error', ['result' => $result]);
            return redirect()->route('tutos.show', $tuto)
                ->with('error', 'Failed to generate certificate.');
        }

        // 🔹 Télécharger le PDF depuis le lien temporaire
        $pdfResponse = $client->get($result['url'], ['verify' => false]);
        $pdfContent = $pdfResponse->getBody()->getContents();

        // 🔹 Sauvegarder localement
        $storagePath = 'certificates/certificate_' . Auth::id() . '_' . time() . '.pdf';
        Storage::put($storagePath, $pdfContent);

        // 🔹 Télécharger automatiquement le PDF
        return response()->download(storage_path('app/' . $storagePath))->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        Log::error('PDF generation failed', ['message' => $e->getMessage()]);
        return redirect()->route('tutos.show', $tuto)
            ->with('error', 'Failed to generate certificate: ' . $e->getMessage());
    }
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