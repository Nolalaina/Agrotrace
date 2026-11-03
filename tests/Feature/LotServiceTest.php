<?php

namespace Tests\Feature;

use App\Exceptions\QuantiteInsuffisanteException;
use App\Exceptions\RegleMetierException;
use App\Exceptions\StatutInvalideException;
use App\Models\Lot;
use App\Models\LotRecolte;
use App\Services\LotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

/** Test de fonctionnalité métier + règles critiques (intégrité, transaction, concurrence). */
class LotServiceTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_composer_un_lot_decremente_les_recoltes_et_calcule_le_poids(): void
    {
        [$expl, $user] = $this->contexte();
        $r1 = $this->recolte($expl, 500);
        $r2 = $this->recolte($expl, 300);

        $lot = app(LotService::class)->composer($user, [
            ['recolte_id' => $r1->id, 'quantite_kg' => 400],
            ['recolte_id' => $r2->id, 'quantite_kg' => 300],
        ], 'extra');

        $this->assertSame(Lot::OUVERT, $lot->statut);
        $this->assertEquals(700, $lot->poids_total_kg);
        $this->assertEquals(100, $r1->fresh()->quantite_disponible_kg);
        $this->assertEquals(0, $r2->fresh()->quantite_disponible_kg);
        $this->assertSame(2, LotRecolte::where('lot_id', $lot->id)->count());
    }

    public function test_quantite_insuffisante_annule_toute_la_transaction(): void
    {
        [$expl, $user] = $this->contexte();
        $r1 = $this->recolte($expl, 500);
        $r2 = $this->recolte($expl, 100);

        try {
            app(LotService::class)->composer($user, [
                ['recolte_id' => $r1->id, 'quantite_kg' => 200], // valide
                ['recolte_id' => $r2->id, 'quantite_kg' => 150], // dépasse le disponible
            ]);
            $this->fail('Une QuantiteInsuffisanteException était attendue.');
        } catch (QuantiteInsuffisanteException) {
            // attendu
        }

        // Rollback : ni lot, ni ligne de pivot, et la première récolte est intacte.
        $this->assertSame(0, Lot::withoutGlobalScopes()->count());
        $this->assertSame(0, LotRecolte::count());
        $this->assertEquals(500, $r1->fresh()->quantite_disponible_kg);
        $this->assertEquals(100, $r2->fresh()->quantite_disponible_kg);
    }

    public function test_une_recolte_ne_peut_pas_etre_sur_affectee_a_deux_lots(): void
    {
        [$expl, $user] = $this->contexte();
        $r = $this->recolte($expl, 500);
        $service = app(LotService::class);

        $service->composer($user, [['recolte_id' => $r->id, 'quantite_kg' => 300]]);

        $this->expectException(QuantiteInsuffisanteException::class);
        $service->composer($user, [['recolte_id' => $r->id, 'quantite_kg' => 300]]); // 300 > 200 restants
    }

    public function test_une_recolte_dune_autre_exploitation_est_refusee(): void
    {
        [, $user] = $this->contexte();
        [$autre] = $this->contexte();
        $rEtrangere = $this->recolte($autre, 500);

        $this->expectException(RegleMetierException::class);
        app(LotService::class)->composer($user, [['recolte_id' => $rEtrangere->id, 'quantite_kg' => 100]]);
    }

    public function test_un_lot_cloture_ne_peut_plus_etre_cloture_ni_modifie(): void
    {
        [$expl, $user] = $this->contexte();
        $r = $this->recolte($expl, 500);
        $service = app(LotService::class);

        $lot = $service->composer($user, [['recolte_id' => $r->id, 'quantite_kg' => 100]]);
        $service->cloturer($user, $lot->id);

        $this->assertFalse($lot->fresh()->estModifiable());
        $this->expectException(StatutInvalideException::class);
        $service->cloturer($user, $lot->id);
    }
}
