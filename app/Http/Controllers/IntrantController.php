<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIntrantRequest;
use App\Http\Requests\StoreMouvementRequest;
use App\Models\Campagne;
use App\Models\Intrant;
use App\Models\MouvementStock;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class IntrantController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Intrant::class);

        return view('intrants.index', [
            'intrants' => Intrant::orderBy('nom')->get(),
            'mouvements' => MouvementStock::with(['intrant', 'campagne'])->latest()->limit(15)->get(),
            'campagnes' => Campagne::where('statut', Campagne::EN_COURS)->get(['id', 'libelle']),
            'categories' => Intrant::CATEGORIES,
            'types' => MouvementStock::TYPES,
        ]);
    }

    public function store(StoreIntrantRequest $request, StockService $stock)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $request, $stock) {
            $intrant = Intrant::create([
                'nom' => $data['nom'],
                'categorie' => $data['categorie'],
                'unite' => $data['unite'],
                'seuil_minimum' => $data['seuil_minimum'],
                'quantite_disponible' => 0,
            ]);

            if (! empty($data['stock_initial']) && $data['stock_initial'] > 0) {
                $stock->enregistrer($request->user(), $intrant->id, MouvementStock::STOCK_INITIAL, (float) $data['stock_initial']);
            }
        });

        return redirect()->route('intrants.index')->with('succes', 'Intrant créé.');
    }

    public function mouvement(StoreMouvementRequest $request, Intrant $intrant, StockService $stock)
    {
        $d = $request->validated();

        $stock->enregistrer(
            $request->user(), $intrant->id, $d['type'], (float) $d['quantite'],
            isset($d['campagne_id']) ? (int) $d['campagne_id'] : null,
            isset($d['cout_total']) ? (float) $d['cout_total'] : null,
            $d['commentaire'] ?? null
        );

        return redirect()->route('intrants.index')->with('succes', 'Mouvement enregistré.');
    }
}
