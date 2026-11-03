<?php

namespace App\Http\Controllers;

use App\Models\Campagne;
use App\Models\TransactionFinanciere;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class FinanceController extends Controller
{
    public function index(Request $request, FinanceService $finance)
    {
        Gate::authorize('voir-finances');

        $campagneId = $request->integer('campagne_id') ?: null;

        return view('finances.index', [
            'resume' => $finance->resume($request->user()->exploitation_id, $campagneId),
            'transactions' => TransactionFinanciere::with('campagne')
                ->when($campagneId, fn ($q) => $q->where('campagne_id', $campagneId))
                ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
                ->orderByDesc('date_transaction')->orderByDesc('id')->paginate(10)->withQueryString(),
            'campagnes' => Campagne::orderByDesc('date_debut')->get(['id', 'libelle']),
        ]);
    }
}
