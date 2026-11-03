<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRecolteRequest;
use App\Models\Campagne;
use App\Models\Recolte;
use App\Services\HarvestService;
use Illuminate\Http\Request;

class RecolteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Recolte::class);

        $recoltes = Recolte::with('campagne.parcelle')
            ->when($request->filled('campagne_id'), fn ($q) => $q->where('campagne_id', $request->campagne_id))
            ->when($request->boolean('non_affectees'), fn ($q) => $q->where('quantite_disponible_kg', '>', 0))
            ->orderByDesc('date_recolte')->orderByDesc('id')
            ->paginate(10)->withQueryString();

        return view('recoltes.index', [
            'recoltes' => $recoltes,
            'campagnes' => Campagne::orderByDesc('date_debut')->get(['id', 'libelle']),
        ]);
    }

    public function create()
    {
        $this->authorize('create', Recolte::class);

        return view('recoltes.form', [
            'campagnes' => Campagne::with('parcelle')->where('statut', Campagne::EN_COURS)->get(),
            'calibres' => Recolte::CALIBRES,
        ]);
    }

    public function store(StoreRecolteRequest $request, HarvestService $service)
    {
        $service->enregistrer($request->user(), $request->validated());

        return redirect()->route('recoltes.index')->with('succes', 'Récolte enregistrée.');
    }
}
