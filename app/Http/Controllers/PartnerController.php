<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;
use GuzzleHttp\Client;

class PartnerController extends Controller
{
    /**
     * Dashboard / liste des partenaires
     */
public function index()
{
    $partners = Partner::withCount('warehouses')->get();
    $types = Partner::select('type')->whereNotNull('type')->distinct()->pluck('type');
    
    $partnersWithWarehouses = $partners->where('warehouses_count', '>', 0)->count();
    $partnersWithEmail = $partners->whereNotNull('email')->count();
    $partnersWithAddress = $partners->whereNotNull('address')->count();
    
    return view('admin.partners.index', compact(
        'partners', 
        'types', 
        'partnersWithWarehouses',
        'partnersWithEmail',
        'partnersWithAddress'
    ));
}

/**
 * Afficher les détails d'un partenaire
 */
public function show(Partner $partner)
{
    return view('admin.partners.show', compact('partner'));
}


    /**
     * Formulaire de création
     */
    public function create()
    {
        return view('admin.partners.create');
    }

    /**
     * Créer un partenaire
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'nullable|email',
            'phone'=> 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        Partner::create($data);

        return redirect()->route('admin.partners.index')
                         ->with('success', 'Partenaire créé avec succès');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    /**
     * Mettre à jour un partenaire
     */
    public function update(Request $request, Partner $partner)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email'=> 'nullable|email',
            'phone'=> 'nullable|string|max:50',
            'type' => 'nullable|string|max:50',
            'address' => 'nullable|string',
        ]);

        $partner->update($data);

        return redirect()->route('admin.partners.index')
                         ->with('success', 'Partenaire mis à jour');
    }

    /**
     * Supprimer un partenaire
     */
    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('admin.partners.index')
                         ->with('success', 'Partenaire supprimé');
    }


public function front(Request $request)
{
    $query = Partner::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%'.$request->search.'%')
              ->orWhere('email', 'like', '%'.$request->search.'%')
              ->orWhere('type', 'like', '%'.$request->search.'%');
    }

    if ($request->filled('type')) {
        $query->where('type', $request->type);
    }

    if ($request->filled('city')) {
        $query->where('address', 'like', '%'.$request->city.'%');
    }

    $types = Partner::select('type')->distinct()->pluck('type')->filter();
    $partners = $query->paginate(9);

    return view('partners.index', compact('partners', 'types'));
}


public function showFront(Partner $partner)
{
    $partner->load('warehouses');
    $warehousePoints = $partner->warehouses->map(function($w){
        return [
            'id' => $w->id,
            'name' => $w->name,
            'latitude' => $w->latitude,
            'longitude' => $w->longitude,
            'address' => $w->address,
        ];
    })->values();

    return view('partners.show', compact('partner', 'warehousePoints'));
}


/**
 * Générer un rapport AI détaillé pour le partenaire (appel AJAX)
 */
public function aiReport(Request $request, Partner $partner)
{
    // Charger les entrepôts liés
    $partner->load('warehouses');
    $warehouses = $partner->warehouses->map(function($w){
        return [
            'id' => $w->id,
            'name' => $w->name,
            'address' => $w->address,
            'city' => $w->city,
            'latitude' => $w->latitude,
            'longitude' => $w->longitude,
            'capacity' => $w->capacity,
            'current_occupancy' => $w->current_occupancy,
            'status' => $w->status,
        ];
    })->toArray();

    // Construire prompt structuré pour l'IA
    $prompt = "You are an expert consultant for Waste2Product (a circular-economy project). "
        ."Analyze the following partner data and its warehouses. Produce a detailed, well-structured report (French preferred) containing:\n"
        ."- Executive summary\n- Key observations (addresses, distribution of warehouses, capacities, occupancy, statuses)\n"
        ."- Environmental suggestions (ways to be more ecological, waste reduction, energy, routing, consolidation)\n"
        ."- Operational suggestions (safety, storage optimization, address/geo recommendations)\n"
        ."- Prioritized action items (short, medium, long term)\n"
        ."- Any data inconsistencies or missing information to follow-up\n\n"
        ."Partner:\n"
        ."Name: {$partner->name}\n"
        ."Email: ".($partner->email ?? 'N/A')."\n"
        ."Phone: ".($partner->phone ?? 'N/A')."\n"
        ."Type: ".($partner->type ?? 'N/A')."\n"
        ."Address: ".($partner->address ?? 'N/A')."\n\n"
        ."Warehouses:\n"
        .json_encode($warehouses, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE)
        ."\n\nPlease provide actionable, concrete suggestions and present the report in a visually clear markdown-like layout.";

    $hfToken = env('HUGGINGFACE_CHAT_TOKEN');
    $model = env('HUGGINGFACE_CHAT_MODEL', 'gpt-4o-mini'); // fallback
    if (empty($hfToken)) {
        return response()->json(['success' => false, 'error' => 'AI token not configured.'], 500);
    }

    try {
        $client = new Client(['timeout' => 30]);
        $response = $client->post('https://router.huggingface.co/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $hfToken,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful expert consultant who writes clear actionable reports.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'max_tokens' => 1000,
            ],
        ]);

        $body = json_decode((string)$response->getBody(), true);
        $report = $body['choices'][0]['message']['content'] ?? ($body['choices'][0]['text'] ?? null);

        if (!$report) {
            return response()->json(['success' => false, 'error' => 'No content returned by AI.'], 500);
        }

        return response()->json(['success' => true, 'report' => $report]);

    } catch (\Exception $e) {
        \Log::error('AI report error: '.$e->getMessage());
        return response()->json(['success' => false, 'error' => 'AI request failed.'], 500);
    }
}


}
