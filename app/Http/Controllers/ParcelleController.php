<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreParcelleRequest;
use App\Models\Parcelle;
use App\Services\TracabiliteService;
use Illuminate\Http\Request;

class ParcelleController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Parcelle::class);

        $parcelles = Parcelle::query()
            ->when($request->string('q')->toString(), fn ($q, $s) => $q->where(fn ($w) => $w
                ->where('nom', 'like', "%{$s}%")->orWhere('code', 'like', "%{$s}%")))
            ->when($request->filled('etat'), fn ($q) => $q->where('etat', $request->etat))
            ->orderBy('code')->paginate(10)->withQueryString();

        return view('parcelles.index', compact('parcelles'));
    }

    public function create()
    {
        $this->authorize('create', Parcelle::class);

        return view('parcelles.form', ['parcelle' => new Parcelle(['etat' => 'productive'])]);
    }

    public function store(StoreParcelleRequest $request)
    {
        Parcelle::create($request->validated());

        return redirect()->route('parcelles.index')->with('succes', 'Parcelle créée.');
    }

    public function edit(Parcelle $parcelle, TracabiliteService $tracabilite)
    {
        $this->authorize('update', $parcelle);

        return view('parcelles.form', [
            'parcelle' => $parcelle,
            'lots' => $tracabilite->lotsDeLaParcelle($parcelle), // traçabilité descendante
        ]);
    }

    public function update(StoreParcelleRequest $request, Parcelle $parcelle)
    {
        $parcelle->update($request->validated());

        return redirect()->route('parcelles.index')->with('succes', 'Parcelle mise à jour.');
    }

    public function destroy(Parcelle $parcelle)
    {
        $this->authorize('delete', $parcelle);

        if ($parcelle->campagnes()->exists()) {
            return back()->withErrors(['regle' => 'Suppression impossible : la parcelle possède des campagnes.']);
        }

        $parcelle->delete();

        return redirect()->route('parcelles.index')->with('succes', 'Parcelle supprimée.');
    }
}
