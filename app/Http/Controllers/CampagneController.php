<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampagneRequest;
use App\Models\Campagne;
use App\Models\Parcelle;
use App\Services\DashboardService;

class CampagneController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Campagne::class);

        $campagnes = Campagne::with('parcelle')->orderByDesc('date_debut')->paginate(10);

        return view('campagnes.index', compact('campagnes'));
    }

    public function create()
    {
        $this->authorize('create', Campagne::class);

        return view('campagnes.form', [
            'campagne' => new Campagne(['statut' => Campagne::EN_COURS]),
            'parcelles' => Parcelle::orderBy('code')->get(),
        ]);
    }

    public function store(StoreCampagneRequest $request, DashboardService $dashboard)
    {
        Campagne::create($request->validated());
        $dashboard->oublier($request->user()->exploitation_id);

        return redirect()->route('campagnes.index')->with('succes', 'Campagne créée.');
    }

    public function edit(Campagne $campagne)
    {
        $this->authorize('update', $campagne);

        return view('campagnes.form', ['campagne' => $campagne, 'parcelles' => Parcelle::orderBy('code')->get()]);
    }

    public function update(StoreCampagneRequest $request, Campagne $campagne, DashboardService $dashboard)
    {
        $campagne->update($request->validated());
        $dashboard->oublier($request->user()->exploitation_id);

        return redirect()->route('campagnes.index')->with('succes', 'Campagne mise à jour.');
    }

    public function destroy(Campagne $campagne)
    {
        $this->authorize('delete', $campagne);

        if ($campagne->recoltes()->exists() || $campagne->activites()->exists()) {
            return back()->withErrors(['regle' => 'Suppression impossible : la campagne contient des activités ou des récoltes.']);
        }

        $campagne->delete();

        return redirect()->route('campagnes.index')->with('succes', 'Campagne supprimée.');
    }
}
