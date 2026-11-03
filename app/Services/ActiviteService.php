<?php

namespace App\Services;

use App\Models\Activite;
use App\Models\Campagne;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ActiviteService
{
    public function __construct(private FinanceService $finance, private DashboardService $dashboard)
    {
    }

    /** Enregistre l'activité et, si elle a un coût, la dépense associée (atomique). */
    public function enregistrer(User $user, array $data): Activite
    {
        $activite = DB::transaction(function () use ($user, $data) {
            $campagne = Campagne::pourExploitation($user->exploitation_id)->findOrFail($data['campagne_id']);

            $activite = Activite::create([
                'exploitation_id' => $user->exploitation_id,
                'campagne_id' => $campagne->id,
                'user_id' => $user->id,
                'type' => $data['type'],
                'date' => $data['date'],
                'description' => $data['description'] ?? null,
                'cout' => $data['cout'] ?? 0,
            ]);

            if ((float) $activite->cout > 0) {
                $this->finance->enregistrerDepense(
                    $user->exploitation_id, 'activite', (float) $activite->cout, $campagne->id,
                    ucfirst($activite->type).' — '.$campagne->libelle
                );
            }

            return $activite;
        });

        $this->dashboard->oublier($user->exploitation_id);

        return $activite;
    }
}
