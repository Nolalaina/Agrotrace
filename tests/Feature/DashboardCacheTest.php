<?php

namespace Tests\Feature;

use App\Services\DashboardService;
use App\Services\HarvestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

class DashboardCacheTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_les_statistiques_sont_mises_en_cache_puis_invalidees_par_un_evenement(): void
    {
        [$expl, $user] = $this->contexte();
        $campagne = $this->campagneEnCours($expl);
        $dashboard = app(DashboardService::class);

        $dashboard->stats($expl->id);
        $this->assertTrue(Cache::has(DashboardService::cle($expl->id)));

        // L'enregistrement d'une récolte déclenche RecolteEnregistree → listener → cache vidé.
        app(HarvestService::class)->enregistrer($user, [
            'campagne_id' => $campagne->id,
            'date_recolte' => now()->toDateString(),
            'quantite_kg' => 400,
        ]);

        $this->assertFalse(Cache::has(DashboardService::cle($expl->id)));
        $this->assertEquals(400, $dashboard->stats($expl->id)['recolte_totale_kg']);
    }

    public function test_les_indicateurs_calcules(): void
    {
        [$expl, $user] = $this->contexte();
        $this->recolte($expl, 1000);

        $stats = app(DashboardService::class)->stats($expl->id);

        $this->assertEquals(1000, $stats['recolte_totale_kg']);
        $this->assertEquals(100.0, $stats['taux_perte_potentielle']); // rien n'est encore affecté à un lot
        $this->assertGreaterThan(0, $stats['rendement_kg_ha']);
    }
}
