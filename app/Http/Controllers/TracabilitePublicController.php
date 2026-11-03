<?php

namespace App\Http\Controllers;

use App\Services\TracabiliteService;

/** Module personnel : page publique de traçabilité (QR code du lot). Aucune donnée financière. */
class TracabilitePublicController extends Controller
{
    public function show(string $code, TracabiliteService $tracabilite)
    {
        $t = $tracabilite->origineDuLot($code);
        abort_if($t === null, 404);

        return view('public.tracabilite', ['t' => $t]);
    }
}
