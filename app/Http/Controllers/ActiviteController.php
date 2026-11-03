<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActiviteRequest;
use App\Models\Activite;
use App\Models\Campagne;
use App\Models\User;
use App\Services\ActiviteService;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Activite::class);

        $request->validate([
            'campagne_id' => ['nullable', 'integer'],
            'type' => ['nullable', 'string'],
            'user_id' => ['nullable', 'integer'],
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date'],
            'tri' => ['nullable', 'in:date,cout,type'],
            'sens' => ['nullable', 'in:asc,desc'],
        ]);

        $tri = $request->input('tri', 'date');
        $sens = $request->input('sens', 'desc');

        $activites = Activite::with(['campagne', 'user'])
            ->when($request->filled('campagne_id'), fn ($q) => $q->where('campagne_id', $request->campagne_id))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('user_id'), fn ($q) => $q->where('user_id', $request->user_id))
            ->when($request->filled('du'), fn ($q) => $q->whereDate('date', '>=', $request->du))
            ->when($request->filled('au'), fn ($q) => $q->whereDate('date', '<=', $request->au))
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->q.'%'))
            ->orderBy($tri, $sens)->orderByDesc('id')
            ->paginate(10)->withQueryString();

        return view('activites.index', [
            'activites' => $activites,
            'campagnes' => Campagne::orderByDesc('date_debut')->get(['id', 'libelle']),
            'utilisateurs' => User::where('exploitation_id', $request->user()->exploitation_id)->orderBy('name')->get(['id', 'name']),
            'types' => Activite::TYPES,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Activite::class);

        return view('activites.form', [
            'campagnes' => Campagne::where('statut', Campagne::EN_COURS)->get(),
            'types' => Activite::TYPES,
        ]);
    }

    public function store(StoreActiviteRequest $request, ActiviteService $service)
    {
        $service->enregistrer($request->user(), $request->validated());

        return redirect()->route('activites.index')->with('succes', 'Activité enregistrée.');
    }
}
