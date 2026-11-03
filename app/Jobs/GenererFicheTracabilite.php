<?php

namespace App\Jobs;

use App\Models\Lot;
use App\Services\TracabiliteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Génération différée de la fiche de traçabilité d'un lot (fichier HTML imprimable).
 * Pour du PDF : composer require barryvdh/laravel-dompdf et remplacer le rendu.
 */
class GenererFicheTracabilite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public Lot $lot)
    {
    }

    public function handle(TracabiliteService $tracabilite): void
    {
        $data = $tracabilite->origineDuLot($this->lot->code);

        if ($data === null) {
            Log::warning('Fiche de traçabilité : lot introuvable ou non clôturé', ['lot' => $this->lot->code]);

            return;
        }

        $html = view('lots.fiche', ['t' => $data])->render();
        Storage::disk('local')->put(self::chemin($this->lot->code), $html);

        Log::info('Fiche de traçabilité générée', ['lot' => $this->lot->code]);
    }

    public static function chemin(string $code): string
    {
        return "fiches/{$code}.html";
    }
}
