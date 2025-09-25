<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventFeedback;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventParticipationEmail;
// QR Code generation will be handled client-side

class EventController extends Controller
{
    /**
     * Dashboard des événements
     */
    public function dashboard()
    {
        $totalEvents = Event::count();
        $totalParticipants = \DB::table('event_participants')->count();
        $totalScanned = \DB::table('event_participants')->whereNotNull('scanned_at')->count();
        $totalBadges = \DB::table('user_badges')->count();
        
        $upcomingEvents = Event::active()
            ->upcoming()
            ->with(['participants'])
            ->orderBy('date')
            ->limit(5)
            ->get();
            
        $recentEvents = Event::with(['participants', 'feedbacks'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.events.dashboard', compact(
            'totalEvents',
            'totalParticipants', 
            'totalScanned',
            'totalBadges',
            'upcomingEvents',
            'recentEvents'
        ));
    }

    /**
     * Liste de tous les événements (vue calendrier)
     */
    public function index()
    {
        $events = Event::with(['participants', 'creator'])
            ->orderBy('date')
            ->get();
            
        $categories = $this->getEventCategories();
        
        return view('admin.events.index', compact('events', 'categories'));
    }

    /**
     * Liste de gestion des événements
     */
    public function manage()
    {
        $events = Event::with(['participants', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.events.manage', compact('events'));
    }

    /**
     * Afficher un événement
     */
    public function show(Event $event)
    {
        $event->load(['participants', 'feedbacks', 'creator']);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Formulaire de création d'événement
     */
    public function create()
    {
        $categories = $this->getEventCategories();
        $products = \App\Models\Product::with('category')->get();
        return view('admin.events.create', compact('categories', 'products'));
    }

    /**
     * Créer un événement
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|string|in:Recycling,Education,Awareness,Collection,Workshop',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'location' => 'required|string|max:255',
            'city' => 'required|string',
            'organizer_email' => 'required|email|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_participants' => 'nullable|integer|min:1|max:1000',
        ]);

        $data = $request->all();
        $data['qr_code'] = Str::random(32);
        $data['created_by'] = auth()->id() ?? 1; // Fallback pour les tests

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event = Event::create($data);


        return redirect()->route('admin.events.manage')
            ->with('success', 'Événement créé avec succès!');
    }

    /**
     * Formulaire d'édition d'événement
     */
    public function edit(Event $event)
    {
        $categories = $this->getEventCategories();
        $products = \App\Models\Product::with('category')->get();
        return view('admin.events.edit', compact('event', 'categories', 'products'));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'max_participants' => 'nullable|integer|min:1',
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);


        return redirect()->route('admin.events.manage')
            ->with('success', 'Événement mis à jour avec succès!');
    }

    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        
        $event->delete();
        
        return redirect()->route('admin.events.manage')
            ->with('success', 'Événement supprimé avec succès!');
    }

    /**
     * Toggle le statut d'un événement
     */
    public function toggleStatus(Event $event)
    {
        $event->update(['status' => !$event->status]);
        
        $status = $event->status ? 'activé' : 'désactivé';
        return redirect()->back()
            ->with('success', "Événement {$status} avec succès!");
    }

    /**
     * Scanner QR Code
     */
    public function qrScanner()
    {
        return view('admin.events.qr-scanner');
    }

    /**
     * Traiter le scan QR Code
     */
    public function scanQr(Request $request)
    {
        $request->validate([
            'participant_id' => 'required|string'
        ]);

        // Find participant by participant_id in the pivot table
        $participant = \DB::table('event_participants')
            ->where('participant_id', $request->participant_id)
            ->first();
        
        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid participant ID - QR Code not found',
                'type' => 'error'
            ]);
        }

        // Get the event and user details
        $event = Event::find($participant->event_id);
        $user = \App\Models\User::find($participant->user_id);

        if (!$event || !$user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid participant data - Event or user not found',
                'type' => 'error'
            ]);
        }

        // Always return success for valid participant IDs, regardless of scan status
        // Only mark as scanned if not already scanned
        if (!$participant->scanned_at) {
            \DB::table('event_participants')
                ->where('participant_id', $request->participant_id)
                ->update(['scanned_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'message' => 'QR Code validated successfully - Participant information displayed',
            'type' => 'success',
            'event' => $event,
            'participant' => $user,
            'participant_id' => $request->participant_id,
            'scanned_at' => $participant->scanned_at
        ]);
    }

    /**
     * Store event feedback
     */
    public function storeFeedback(Request $request, Event $event)
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Check if event has ended
        if ($event->date >= now()) {
            return redirect()->back()->with('error', 'Feedback can only be submitted after the event has ended.');
        }

        // Check if user participated in this event
        $participation = $event->participants()
            ->where('user_id', auth()->id())
            ->first();

        if (!$participation) {
            return redirect()->back()->with('error', 'You must participate in this event to submit feedback.');
        }

        // Check if user already submitted feedback
        $existingFeedback = \App\Models\EventFeedback::where('event_id', $event->id)
            ->where('user_id', auth()->id())
            ->first();

        // Validate the request
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'satisfaction_level' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Handle photo uploads
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('feedback-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // Prepare feedback data
        $feedbackData = [
            'rating' => $request->rating,
            'satisfaction_level' => $request->satisfaction_level,
            'comment' => $request->comment,
            'recycled_quantity' => 0,
            'co2_saved' => 0,
        ];

        // Handle photos - merge existing and new photos
        $finalPhotos = [];
        
        // Get existing photos if any
        if ($existingFeedback && $existingFeedback->photo) {
            $existingPhotos = json_decode($existingFeedback->photo, true) ?: [];
            $finalPhotos = $existingPhotos;
        }
        
        // Remove photos that user wants to delete
        if ($request->has('removed_photos')) {
            $removedPhotos = is_array($request->removed_photos) ? $request->removed_photos : [$request->removed_photos];
            $finalPhotos = array_filter($finalPhotos, function($photo) use ($removedPhotos) {
                return !in_array($photo, $removedPhotos);
            });
        }
        
        // Add new photos
        if (!empty($photoPaths)) {
            $finalPhotos = array_merge($finalPhotos, $photoPaths);
        }
        
        // Ensure we don't exceed 5 photos total
        $finalPhotos = array_slice($finalPhotos, 0, 5);
        
        $feedbackData['photo'] = !empty($finalPhotos) ? json_encode($finalPhotos) : null;

        if ($existingFeedback) {
            // Update existing feedback
            $existingFeedback->update($feedbackData);
            $message = 'Your feedback has been updated successfully!';
        } else {
            // Create new feedback
            $feedbackData['event_id'] = $event->id;
            $feedbackData['user_id'] = auth()->id();
            \App\Models\EventFeedback::create($feedbackData);
            $message = 'Thank you for sharing your experience! Your feedback has been submitted successfully.';
        }

        return redirect()->back()->with('feedback_success', $message);
    }

    /**
     * Feedback et impact
     */
    public function feedback()
    {
        try {
            $events = Event::where('date', '<', now())
                ->with(['feedbacks', 'participants'])
                ->get();
                
            $totalCo2Saved = EventFeedback::sum('co2_saved') ?? 0;
            $totalRecycled = EventFeedback::sum('recycled_quantity') ?? 0;
            $averageRating = EventFeedback::avg('rating') ?? 0;
            $totalFeedbacks = EventFeedback::count();
            
            // Get all feedbacks with user and event relationships
            $feedbacks = EventFeedback::with(['user', 'event'])
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('admin.events.feedback', compact(
                'events',
                'totalCo2Saved',
                'totalRecycled',
                'averageRating',
                'totalFeedbacks',
                'feedbacks'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in feedback method: ' . $e->getMessage());
            return view('admin.events.feedback', [
                'events' => collect(),
                'totalCo2Saved' => 0,
                'totalRecycled' => 0,
                'averageRating' => 0,
                'totalFeedbacks' => 0,
                'feedbacks' => collect()
            ]);
        }
    }

    /**
     * Gestion des badges
     */
    public function badges()
    {
        $badges = Badge::with(['users'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.events.badges', compact('badges'));
    }

    /**
     * Créer un badge
     */
    public function createBadge(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
            'color' => 'required|string',
            'criteria_type' => 'required|string',
            'criteria_value' => 'required|integer|min:0',
            'points_required' => 'required|integer|min:0'
        ]);

        Badge::create($request->all());

        return redirect()->route('admin.events.badges')
            ->with('success', 'Badge créé avec succès!');
    }

    /**
     * API pour obtenir les événements (pour le calendrier)
     */
    public function apiEvents()
    {
        $events = Event::with(['participants'])
            ->get()
            ->map(function ($event) {
                $color = $this->getCategoryColor($event->category);
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'description' => $event->description,
                    'category' => $event->category,
                    'status' => $event->status,
                    'date' => $event->date->format('Y-m-d'),
                    'start' => $event->date->format('Y-m-d'),
                    'end' => $event->date->format('Y-m-d'),
                    'color' => $color,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'textColor' => '#ffffff',
                    'url' => route('admin.events.show', $event),
                    'extendedProps' => [
                        'category' => $event->category,
                        'status' => $event->status ? 'Actif' : 'Inactif',
                        'location' => $event->location
                    ]
                ];
            });

        return response()->json($events);
    }

    /**
     * Obtenir les catégories d'événements
     */
    private function getEventCategories()
    {
        return [
            (object)['name' => 'Collection', 'value' => 'Collection'],
            (object)['name' => 'Workshop', 'value' => 'Workshop'],
            (object)['name' => 'Awareness', 'value' => 'Awareness'],
            (object)['name' => 'Education', 'value' => 'Education'],
            (object)['name' => 'Recycling', 'value' => 'Recycling']
        ];
    }

    /**
     * Obtenir la couleur d'une catégorie
     */
    private function getCategoryColor($category)
    {
        $colors = [
            // Catégories en français (comme dans la base de données)
            'Recyclage' => '#28a745',      // Vert
            'Éducation' => '#007bff',      // Bleu
            'Sensibilisation' => '#ffc107', // Jaune
            'Collecte' => '#17a2b8',       // Cyan
            'Atelier' => '#6f42c1',        // Violet
            
            // Catégories en anglais (pour compatibilité)
            'Recycling' => '#28a745',
            'Education' => '#007bff',
            'Awareness' => '#ffc107',
            'Collection' => '#17a2b8',
            'Workshop' => '#6f42c1'
        ];

        return $colors[$category] ?? '#6c757d';
    }

    /**
     * Page publique des événements avec filtres
     */
    public function publicIndex(Request $request)
    {
        $query = Event::active()->upcoming()->with(['participants', 'creator']);

        // Filtres
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        $events = $query->orderBy('date')->paginate(6);
        $categories = $this->getEventCategories();
        
        // Statistiques
        $totalEvents = Event::active()->upcoming()->count();
        $totalParticipants = \DB::table('event_participants')->count();
        
        return view('events.index', compact('events', 'categories', 'totalEvents', 'totalParticipants'));
    }

    /**
     * Afficher un événement public
     */
    public function publicShow(Event $event)
    {
        $event->load(['participants', 'creator']);
        
        // Vérifier si l'utilisateur est déjà inscrit
        $isParticipating = false;
        $participantId = null;
        if (auth()->check()) {
            $participant = $event->participants()->where('user_id', auth()->id())->first();
            if ($participant) {
                $isParticipating = true;
                $participantId = $participant->pivot->participant_id;
            }
        }
        
        // Si l'utilisateur vient de s'inscrire, récupérer le participant_id depuis la session
        if (!$isParticipating && session('participant_id')) {
            $participantId = session('participant_id');
            $isParticipating = true;
            // Ne pas nettoyer la session immédiatement, laisser la vue l'utiliser
        }

        // Récupérer le feedback existant de l'utilisateur pour cet événement
        $existingFeedback = null;
        if (auth()->check() && $event->date < now()) {
            $existingFeedback = \App\Models\EventFeedback::where('event_id', $event->id)
                ->where('user_id', auth()->id())
                ->first();
        }

        // Événements similaires
        $similarEvents = Event::active()
            ->upcoming()
            ->where('id', '!=', $event->id)
            ->where('category', $event->category)
            ->limit(3)
            ->get();

        return view('events.show', compact('event', 'isParticipating', 'participantId', 'similarEvents', 'existingFeedback'));
    }

    /**
     * Participer à un événement
     */
    public function participate(Request $request, Event $event)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour participer à un événement.');
        }

        // Vérifier si l'événement est actif et futur
        if (!$event->status || $event->date < now()) {
            return redirect()->back()->with('error', 'Cet événement n\'est plus disponible.');
        }

        // Vérifier si l'utilisateur est déjà inscrit
        if ($event->participants()->where('user_id', auth()->id())->exists()) {
            return redirect()->back()->with('error', 'Vous êtes déjà inscrit à cet événement.');
        }

        // Vérifier la limite de participants
        if ($event->max_participants && $event->participants()->count() >= $event->max_participants) {
            return redirect()->back()->with('error', 'Cet événement est complet.');
        }

        // Inscrire l'utilisateur
        $participantId = Str::random(32);
        $event->participants()->attach(auth()->id(), [
            'participant_id' => $participantId
        ]);

        // Envoyer l'email de confirmation avec QR code
        try {
            Mail::to(auth()->user()->email)->send(new EventParticipationEmail($event, auth()->user(), $participantId));
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email participation: ' . $e->getMessage());
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Vous êtes maintenant inscrit à cet événement ! Un email de confirmation avec votre QR code a été envoyé.')
            ->with('participant_id', $participantId);
    }

    /**
     * Afficher le QR code d'un participant
     */
    public function showQrCode(Event $event, $participantId)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $participant = $event->participants()
            ->where('user_id', auth()->id())
            ->where('participant_id', $participantId)
            ->first();

        if (!$participant) {
            return redirect()->route('events.show', $event)
                ->with('error', 'QR code non trouvé.');
        }

        // Nettoyer la session après avoir affiché le QR code
        session()->forget('participant_id');

        // Préparer les données pour le QR code (informations de l'événement)
        $user = auth()->user();
        $fullName = trim($user->first_name . ' ' . $user->last_name);
        
        $qrData = [
            'event_title' => $event->title,
            'event_date' => $event->date->format('Y-m-d'),
            'event_time' => $event->time->format('H:i'),
            'event_location' => $event->location,
            'event_city' => $event->city,
            'event_category' => $event->category,
            'participant_name' => $fullName,
            'participant_email' => $user->email,
            'participant_id' => $participantId
        ];

        // Les données QR seront générées côté client avec JavaScript
        $qrCodeImage = null;

        // Debug: vérifier les données
        \Log::info('QR Data for event ' . $event->id . ':', $qrData);

        return view('events.qr-code', compact('event', 'participant', 'participantId', 'qrData', 'qrCodeImage'));
    }

    /**
     * Mes événements (page profil)
     */
    public function myEvents()
    {
        $user = auth()->user();
        
        // Get events that the user has participated in
        $participatedEvents = Event::whereHas('participants', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->with(['participants' => function($query) use ($user) {
            $query->where('user_id', $user->id);
        }])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('events.my-events', compact('participatedEvents'));
    }

}