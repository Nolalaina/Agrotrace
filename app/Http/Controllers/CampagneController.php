<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCampagneRequest;
use App\Models\Campagne;
use App\Models\Parcelle;
use App\Services\DashboardService;
use Illuminate\Http\Request;

class CampagneController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Campagne::class);

        $campagnes = Campagne::with('parcelle')
            ->when($request->filled('q'), fn ($q) => $q->where('libelle', 'like', '%'.$request->q.'%'))
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('parcelle_id'), fn ($q) => $q->where('parcelle_id', $request->parcelle_id))
            ->orderByDesc('date_debut')->paginate(10)->withQueryString();

        return view('campagnes.index', [
            'campagnes' => $campagnes,
            'parcelles' => Parcelle::orderBy('code')->get(['id', 'code', 'nom']),
        ]);
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
