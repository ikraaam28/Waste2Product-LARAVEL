<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\EventFeedback;
use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            ->with(['participants', 'products'])
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
        $events = Event::with(['participants', 'products', 'creator'])
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
        $events = Event::with(['participants', 'products', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('admin.events.manage', compact('events'));
    }

    /**
     * Afficher un événement
     */
    public function show(Event $event)
    {
        $event->load(['participants.user', 'products.category', 'feedbacks.user', 'creator']);
        return view('admin.events.show', compact('event'));
    }

    /**
     * Formulaire de création d'événement
     */
    public function create()
    {
        $categories = $this->getEventCategories();
        $products = Product::with('category')->get();
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
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id'
        ]);

        $data = $request->all();
        $data['qr_code'] = Str::random(32);
        $data['created_by'] = auth()->id() ?? 1; // Fallback pour les tests

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event = Event::create($data);

        if ($request->has('products')) {
            $event->products()->attach($request->products);
        }

        return redirect()->route('admin.events.manage')
            ->with('success', 'Événement créé avec succès!');
    }

    /**
     * Formulaire d'édition d'événement
     */
    public function edit(Event $event)
    {
        $categories = $this->getEventCategories();
        $products = Product::with('category')->get();
        $event->load('products');
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
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id'
        ]);

        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($data);

        if ($request->has('products')) {
            $event->products()->sync($request->products);
        } else {
            $event->products()->detach();
        }

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
            'qr_code' => 'required|string'
        ]);

        $event = Event::where('qr_code', $request->qr_code)->first();
        
        if (!$event) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code invalide'
            ]);
        }

        $participant = $event->participants()
            ->where('user_id', $request->user_id)
            ->first();

        if (!$participant) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non inscrit à cet événement'
            ]);
        }

        if ($participant->pivot->scanned_at) {
            return response()->json([
                'success' => false,
                'message' => 'QR Code déjà scanné',
                'scanned_at' => $participant->pivot->scanned_at
            ]);
        }

        $participant->pivot->update(['scanned_at' => now()]);

        return response()->json([
            'success' => true,
            'message' => 'QR Code scanné avec succès',
            'event' => $event,
            'participant' => $participant
        ]);
    }

    /**
     * Feedback et impact
     */
    public function feedback()
    {
        try {
            $events = Event::where('date', '<', now())
                ->with(['feedbacks.user', 'participants'])
                ->get();
                
            $totalCo2Saved = EventFeedback::sum('co2_saved') ?? 0;
            $totalRecycled = EventFeedback::sum('recycled_quantity') ?? 0;
            $averageRating = EventFeedback::avg('rating') ?? 0;
            
            return view('admin.events.feedback', compact(
                'events',
                'totalCo2Saved',
                'totalRecycled',
                'averageRating'
            ));
        } catch (\Exception $e) {
            \Log::error('Error in feedback method: ' . $e->getMessage());
            return view('admin.events.feedback', [
                'events' => collect(),
                'totalCo2Saved' => 0,
                'totalRecycled' => 0,
                'averageRating' => 0
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
        $events = Event::with(['participants', 'products'])
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
            (object)['name' => 'Recyclage', 'value' => 'Recycling'],
            (object)['name' => 'Éducation', 'value' => 'Education'],
            (object)['name' => 'Sensibilisation', 'value' => 'Awareness'],
            (object)['name' => 'Collecte', 'value' => 'Collection'],
            (object)['name' => 'Atelier', 'value' => 'Workshop']
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
}