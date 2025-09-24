<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Partner;

class PartnerController extends Controller
{
    /**
     * Dashboard / liste des partenaires
     */
    public function index()
    {
        $partners = Partner::all();
        return view('admin.partners.index', compact('partners'));
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
    return view('partners.show', compact('partner'));
}


}
