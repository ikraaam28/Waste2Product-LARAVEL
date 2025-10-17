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
use Dompdf\Dompdf;
use Dompdf\Options;


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
    // Récupérer la progression et l'admissibilité
    $userAttempts = QuizAttempt::where('user_id', Auth::id())
        ->whereIn('quiz_id', $tuto->quizzes->pluck('id'))
        ->get();
    $completedQuizzes = $userAttempts->count();
    $totalQuizzes = $tuto->quizzes->count();
    $averagePercentage = $userAttempts->isEmpty() ? 0 : $userAttempts->avg('percentage');

    // Vérification des conditions d'éligibilité
    if ($completedQuizzes !== $totalQuizzes || $averagePercentage < 70) {
        return redirect()->route('tutos.show', $tuto)
            ->with('error', 'Vous n\'êtes pas éligible au certificat.');
    }

    // Préparer le HTML du certificat (personnalise ce template selon tes besoins)
    $userName = Auth::user()->full_name;
    $logoPath = public_path('assets/img/recycleverse1.png');
    $logoData = file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : null;
    $logoHtml = $logoData ? "<img class='logo' src='data:image/png;base64,{$logoData}' alt='Logo'>" : "";

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
            {$logoHtml}
            <h1>Certificate of Participation</h1>
            <h2>Congratulations!</h2>
            <p>This certifies that</p>
            <p><strong>{$userName}</strong></p>
            <p>has successfully participated in the tutorial</p>
            <p><strong>{$tuto->title}</strong></p>
            <p>with an average score of <strong>" . number_format($averagePercentage, 2) . "%</strong>.</p>
            <p>Issued on: " . date('F d, Y') . "</p>
            <p class='app-name'>Proudly presented by <strong>RecycleVerse</strong></p>
        </div>
    </body>
    </html>
    ";

    try {
        $client = new \GuzzleHttp\Client();
        $apiKey = '9fa09c30-61a1-4c7a-a3ab-f9ea2dcd0469'; // Tu peux te servir du .env si tu préfères
        $response = $client->post('https://api.pdflayer.com/api/convert', [
            'form_params' => [
                'access_key' => $apiKey,
                'document_html' => $html,
                'document_name' => 'certificate_' . Auth::id() . '_' . time() . '.pdf',
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
            ],
            'stream' => true,
            'timeout' => 60
        ]);

        // Sauvegarde du PDF dans storage/app/certificates
        $fileName = 'certificates/certificate_' . Auth::id() . '_' . time() . '.pdf';
        Storage::put($fileName, $response->getBody());

        // Téléchargement automatique du PDF
        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);

    } catch (\Exception $e) {
        \Log::error('PDFLayer PDF generation failed', ['error' => $e->getMessage()]);
        return redirect()->route('tutos.show', $tuto)
            ->with('error', "Generation échouée : " . $e->getMessage());
    }
}



/**
 * Afficher le certificat HTML
 */
public function showCertificate(Tuto $tuto)
{
    // Vérifier l'éligibilité
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

    // Récupérer le nom de l'utilisateur connecté
    $userName = Auth::user()->full_name;

    return view('certificates.show', compact('tuto', 'completedQuizzes', 'totalQuizzes', 'averagePercentage', 'userName'));
}


/**
 * Télécharger le certificat en PDF
 */
public function downloadCertificate(Request $request, Tuto $tuto)
{
    // Vérifier à nouveau l'éligibilité
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

    try {
        $dompdf = new Dompdf();
        
        $userName = Auth::user()->full_name;
        $currentDate = now()->format('F d, Y');
        
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
            <style>
                @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');
                
                body { 
                    font-family: 'Montserrat', 'DejaVu Sans', sans-serif; 
                    text-align: center; 
                    color: #2c3e50;
                    margin: 0;
                    padding: 0;
                    background: #f8f9fa;
                }
                
                .certificate { 
                    background: white;
                    border: 25px solid #f1c40f;
                    padding: 60px 40px;
                    margin: 20px;
                    position: relative;
                    min-height: 700px;
                }
                
                .certificate-badge {
                    position: absolute;
                    top: -20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background: #e74c3c;
                    color: white;
                    padding: 12px 40px;
                    border-radius: 30px;
                    font-weight: bold;
                    font-size: 16px;
                }
                
                .certificate-title {
                    font-size: 42px;
                    color: #2c3e50;
                    margin: 40px 0 20px 0;
                    font-weight: bold;
                    text-transform: uppercase;
                }
                
                .certificate-subtitle {
                    font-size: 20px;
                    color: #7f8c8d;
                    margin-bottom: 40px;
                }
                
                .user-name {
                    font-size: 36px;
                    color: #e74c3c;
                    font-weight: bold;
                    margin: 30px 0;
                    text-transform: uppercase;
                }
                
                .tutorial-title {
                    font-size: 24px;
                    color: #2980b9;
                    font-weight: bold;
                    margin: 25px 0;
                    font-style: italic;
                }
                
                .score-badge {
                    display: inline-block;
                    background: linear-gradient(135deg, #27ae60, #2ecc71);
                    color: white;
                    padding: 15px 35px;
                    border-radius: 50px;
                    font-size: 22px;
                    font-weight: bold;
                    margin: 25px 0;
                }
                
                .certificate-text {
                    font-size: 18px;
                    color: #34495e;
                    margin: 15px 0;
                    line-height: 1.6;
                }
                
                .signature-section {
                    display: flex;
                    justify-content: space-around;
                    margin: 50px 0 30px 0;
                    border-top: 2px solid #bdc3c7;
                    padding-top: 30px;
                }
                
                .signature {
                    text-align: center;
                }
                
                .signature-line {
                    width: 180px;
                    height: 2px;
                    background: #34495e;
                    margin: 15px auto;
                }
                
                .certificate-date {
                    font-size: 14px;
                    color: #7f8c8d;
                    margin-top: 20px;
                }
                
                .decoration {
                    position: absolute;
                    font-size: 80px;
                    opacity: 0.1;
                    color: #3498db;
                }
                
                .decoration-1 { top: 40px; left: 40px; }
                .decoration-2 { top: 40px; right: 40px; }
                .decoration-3 { bottom: 40px; left: 40px; }
                .decoration-4 { bottom: 40px; right: 40px; }
            </style>
        </head>
        <body>
            <div class=\"certificate\">
                <!-- Badge -->
                <div class=\"certificate-badge\">
                    CERTIFICATE OF ACHIEVEMENT
                </div>
                
                <!-- Decorations -->
                <div class=\"decoration decoration-1\">★</div>
                <div class=\"decoration decoration-2\">★</div>
                <div class=\"decoration decoration-3\">★</div>
                <div class=\"decoration decoration-4\">★</div>
                
                <!-- Header -->
                <div>
                    <h1 class=\"certificate-title\">Certificate of Excellence</h1>
                    <p class=\"certificate-subtitle\">Awarded with Honors</p>
                </div>
                
                <!-- Body -->
                <div>
                    <p class=\"certificate-text\">This certificate is proudly awarded to</p>
                    
                    <div class=\"user-name\">
                        {$userName}
                    </div>
                    
                    <p class=\"certificate-text\">for successfully completing the tutorial</p>
                    
                    <div class=\"tutorial-title\">
                        \"{$tuto->title}\"
                    </div>
                    
                    <p class=\"certificate-text\">with an outstanding average score of</p>
                    
                    <div class=\"score-badge\">
                        ".number_format($averagePercentage, 2)."%
                    </div>
                    
                    <p class=\"certificate-text\">
                        Quizzes completed: <strong>{$completedQuizzes}/{$totalQuizzes}</strong>
                    </p>
                </div>
                
                <!-- Footer -->
                <div>
                    <div class=\"signature-section\">
                        <div class=\"signature\">
                            <div class=\"signature-line\"></div>
                            <p>Educational Director</p>
                            <p><strong>RecycleVerse Academy</strong></p>
                        </div>
                        
                        <div class=\"signature\">
                            <div class=\"signature-line\"></div>
                            <p>Date Issued</p>
                            <p><strong>{$currentDate}</strong></p>
                        </div>
                    </div>
                    
                    <p class=\"certificate-date\">
                        Certificate ID: RV".Auth::id()."-{$tuto->id}-".time()."
                    </p>
                </div>
            </div>
        </body>
        </html>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->set_option('isHtml5ParserEnabled', true);
        $dompdf->set_option('isRemoteEnabled', true);
        $dompdf->render();

        $fileName = 'RecycleVerse_Certificate_' . Auth::user()->name . '_' . time() . '.pdf';

        return response()->streamDownload( // streamDownload est plus efficace en mémoire que download pour les fichiers générés à la volée
            function () use ($dompdf) {
                echo $dompdf->output();
            },
            $fileName,
            [
                'Content-Type' => 'application/pdf',
            ]
        );

    } catch (\Exception $e) {
        Log::error('Certificate PDF generation failed', [
            'message' => $e->getMessage(),
            'user_id' => Auth::id(),
            'tuto_id' => $tuto->id
        ]);
        
        return redirect()->route('certificates.show', $tuto)
            ->with('error', 'Error generating PDF: ' . $e->getMessage());
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