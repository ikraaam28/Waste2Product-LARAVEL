<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warehouse;
use App\Models\Partner;

class WarehouseController extends Controller
{
    /**
     * Liste des entrepôts
     */
    public function index()
    {
        $warehouses = Warehouse::with('partner')->latest()->get();
        return view('admin.warehouses.index', compact('warehouses'));
    }

    /**
     * Afficher les détails d'un entrepôt
     */
    public function show(Warehouse $warehouse)
    {
        $warehouse->load('partner');
        return view('admin.warehouses.show', compact('warehouse'));
    }

    /**
     * Formulaire de création
     */
    public function create()
    {
        $partners = Partner::all();
        return view('admin.warehouses.create', compact('partners'));
    }

    /**
     * Créer un entrepôt
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'partner_id' => 'required|exists:partners,id',
            'location' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'capacity' => 'required|numeric|min:0',
            'current_occupancy' => 'nullable|numeric|min:0',
            'contact_person' => 'nullable|string|max:255',
            'contact_phone' => 'nullable|string|max:50',
            'contact_email' => 'nullable|email',
            'status' => 'required|in:active,inactive,maintenance',
            'description' => 'nullable|string',
        ]);

        // S'assurer que l'occupation ne dépasse pas la capacité
        if (isset($data['current_occupancy']) && $data['current_occupancy'] > $data['capacity']) {
            return back()->withErrors(['current_occupancy' => 'L\'occupation ne peut pas dépasser la capacité.'])->withInput();
        }

        Warehouse::create($data);

        return redirect()->route('admin.warehouses.index')
                         ->with('success', 'Entrepôt créé avec succès');
    }

    /**
     * Formulaire d'édition
     */
    public function edit(Warehouse $warehouse)
    {
        $partners = Partner::all();
        return view('admin.warehouses.edit', compact('warehouse', 'partners'));
    }

    /**
     * Mettre à jour un entrepôt
     */
    public function update(Request $request, Warehouse $warehouse)
{
    // Si c'est juste un changement de status
    if ($request->has('status') && !$request->has('name')) {
        $data = $request->validate([
            'status' => 'required|in:active,inactive,maintenance',
        ]);

        $warehouse->update($data);

        return redirect()->back()->with('success', 'Status mis à jour avec succès');
    }

    // Sinon, validation complète
    $data = $request->validate([
        'name' => 'required|string|max:255',
        'partner_id' => 'required|exists:partners,id',
        'location' => 'nullable|string|max:255',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'country' => 'nullable|string|max:100',
        'capacity' => 'required|numeric|min:0',
        'current_occupancy' => 'nullable|numeric|min:0',
        'contact_person' => 'nullable|string|max:255',
        'contact_phone' => 'nullable|string|max:50',
        'contact_email' => 'nullable|email',
        'status' => 'required|in:active,inactive,maintenance',
        'description' => 'nullable|string',
    ]);

    // Validation de l'occupation
    if (isset($data['current_occupancy']) && $data['current_occupancy'] > $data['capacity']) {
        return back()->withErrors(['current_occupancy' => 'L\'occupation ne peut pas dépasser la capacité.'])->withInput();
    }

    $warehouse->update($data);

    return redirect()->route('admin.warehouses.index')
                     ->with('success', 'Entrepôt mis à jour avec succès');
}


    /**
     * Supprimer un entrepôt
     */
    public function destroy(Warehouse $warehouse)
    {
        $warehouse->delete();

        return redirect()->route('admin.warehouses.index')
                         ->with('success', 'Entrepôt supprimé avec succès');
    }

    /**
     * API pour récupérer les entrepôts d'un partenaire (pour AJAX)
     */
    public function getByPartner(Partner $partner)
    {
        $warehouses = $partner->warehouses()->active()->get();
        return response()->json($warehouses);
    }
}