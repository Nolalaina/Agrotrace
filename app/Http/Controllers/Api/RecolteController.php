<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRecolteRequest;
use App\Http\Resources\RecolteResource;
use App\Models\Recolte;
use App\Services\HarvestService;
use Illuminate\Http\Request;

class RecolteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Recolte::class);

        return RecolteResource::collection(
            Recolte::when($request->boolean('non_affectees'), fn ($q) => $q->where('quantite_disponible_kg', '>', 0))
                ->orderByDesc('date_recolte')->paginate(15)->withQueryString()
        );
    }

    public function store(StoreRecolteRequest $request, HarvestService $service)
    {
        $recolte = $service->enregistrer($request->user(), $request->validated());

        return (new RecolteResource($recolte))->response()->setStatusCode(201);
    }

    /** Modification limitée au calibre et aux notes (la quantité est tracée par les lots). */
    public function update(Request $request, Recolte $recolte)
    {
        $this->authorize('update', $recolte);

        $data = $request->validate([
            'calibre' => ['nullable', 'in:'.implode(',', Recolte::CALIBRES)],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $recolte->update($data);

        return new RecolteResource($recolte);
    }
}
