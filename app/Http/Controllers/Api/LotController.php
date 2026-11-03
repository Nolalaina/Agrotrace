<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LotResource;
use App\Http\Resources\TracabiliteResource;
use App\Models\Lot;
use App\Services\TracabiliteService;
use Illuminate\Http\Request;

class LotController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Lot::class);

        return LotResource::collection(
            Lot::when($request->filled('statut'), fn ($q) => $q->where('statut', $request->statut))
                ->orderByDesc('created_at')->paginate(15)->withQueryString()
        );
    }

    public function show(string $code)
    {
        $lot = Lot::with('recoltes')->where('code', $code)->firstOrFail(); // scope : 404 si autre exploitation
        $this->authorize('view', $lot);

        return new LotResource($lot);
    }

    public function tracabilite(string $code, TracabiliteService $tracabilite)
    {
        $lot = Lot::where('code', $code)->firstOrFail(); // vérifie l'appartenance à l'exploitation
        $this->authorize('view', $lot);

        $data = $tracabilite->origineDuLot($lot->code);

        return $data === null
            ? response()->json(['message' => 'La traçabilité n\'est disponible que pour un lot clôturé ou vendu.'], 409)
            : new TracabiliteResource($data);
    }
}
