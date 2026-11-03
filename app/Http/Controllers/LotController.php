<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComposerLotRequest;
use App\Http\Requests\VendreLotRequest;
use App\Jobs\GenererFicheTracabilite;
use App\Models\Lot;
use App\Models\Recolte;
use App\Services\FinanceService;
use App\Services\LotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lot::class);

        $lots = Lot::query()
            ->when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
            ->when($request->filled('q'), fn ($q) => $q->where('code', 'like', '%'.$request->q.'%'))
            ->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('lots.index', compact('lots'));
    }

    public function create()
    {
        $this->authorize('create', Lot::class);

        return view('lots.create', [
            'recoltes' => Recolte::with('campagne.parcelle')
                ->where('quantite_disponible_kg', '>', 0)->orderBy('date_recolte')->get(),
        ]);
    }

    public function store(ComposerLotRequest $request, LotService $service)
    {
        $lot = $service->composer($request->user(), $request->validated('lignes'), $request->validated('qualite'));

        return redirect()->route('lots.show', $lot)->with('succes', 'Lot '.$lot->code.' créé.');
    }

    public function show(Lot $lot)
    {
        $this->authorize('view', $lot);

        $lot->load(['recoltes.campagne.parcelle', 'vente']);

        return view('lots.show', [
            'lot' => $lot,
            'ficheDisponible' => Storage::disk('local')->exists(GenererFicheTracabilite::chemin($lot->code)),
        ]);
    }

    public function cloturer(Request $request, Lot $lot, LotService $service)
    {
        $this->authorize('cloturer', $lot);
        $service->cloturer($request->user(), $lot->id);

        return back()->with('succes', 'Lot clôturé : il n\'est plus modifiable.');
    }

    public function vendre(VendreLotRequest $request, Lot $lot, FinanceService $finance)
    {
        $finance->vendreLot($request->user(), $lot->id, $request->validated('acheteur'),
            (float) $request->validated('montant'), $request->validated('date_vente'));

        return back()->with('succes', 'Vente enregistrée.');
    }

    public function genererFiche(Lot $lot)
    {
        $this->authorize('genererFiche', $lot);
        // Exécution immédiate : la fiche est disponible tout de suite, sans worker de queue.
        GenererFicheTracabilite::dispatchSync($lot);

        return back()->with('succes', 'Fiche de traçabilité générée : vous pouvez la télécharger.');
    }

    public function telechargerFiche(Lot $lot)
    {
        $this->authorize('genererFiche', $lot);
        abort_unless(Storage::disk('local')->exists(GenererFicheTracabilite::chemin($lot->code)), 404);

        return Storage::disk('local')->download(GenererFicheTracabilite::chemin($lot->code), $lot->code.'.html');
    }
}
