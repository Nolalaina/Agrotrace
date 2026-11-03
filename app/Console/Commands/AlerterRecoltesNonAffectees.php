<?php

namespace App\Console\Commands;

use App\Models\Exploitation;
use App\Models\Recolte;
use App\Notifications\RecolteNonAffecteeNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AlerterRecoltesNonAffectees extends Command
{
    protected $signature = 'recoltes:alerter {--jours=3 : Délai avant alerte}';

    protected $description = 'Alerte les responsables des récoltes non affectées à un lot depuis N jours';

    public function handle(): int
    {
        $jours = (int) $this->option('jours');

        foreach (Exploitation::all() as $exploitation) {
            $query = Recolte::pourExploitation($exploitation->id)
                ->where('quantite_disponible_kg', '>', 0)
                ->whereDate('date_recolte', '<=', now()->subDays($jours));

            $nombre = (clone $query)->count();
            if ($nombre === 0) {
                continue;
            }

            $quantite = (float) (clone $query)->sum('quantite_disponible_kg');
            Notification::send(
                $exploitation->responsables()->get(),
                new RecolteNonAffecteeNotification($nombre, $quantite, $jours)
            );

            Log::info('Alerte récoltes non affectées', ['exploitation_id' => $exploitation->id, 'nombre' => $nombre]);
            $this->info("{$exploitation->nom} : {$nombre} récolte(s) en attente d'affectation.");
        }

        return self::SUCCESS;
    }
}
