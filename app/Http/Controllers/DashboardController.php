<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardService $dashboard)
    {
        $stats = $dashboard->stats($request->user()->exploitation_id);

        // Les données financières ne sont pas exposées au technicien (contrôle serveur).
        if (! Gate::allows('voir-finances')) {
            unset($stats['finance'], $stats['cout_production_kg']);
        }

        return view('dashboard', [
            'stats' => $stats,
            'notifications' => $request->user()->unreadNotifications()->latest()->limit(5)->get(),
        ]);
    }
}
