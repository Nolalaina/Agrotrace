<?php

namespace Tests\Feature;

use App\Events\StockCritique;
use App\Exceptions\QuantiteInsuffisanteException;
use App\Exceptions\StatutInvalideException;
use App\Models\Intrant;
use App\Models\MouvementStock;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

class StockServiceTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_le_stock_ne_devient_jamais_negatif(): void
    {
        [$expl, $user] = $this->contexte();
        $campagne = $this->campagneEnCours($expl);
        $intrant = Intrant::factory()->create(['exploitation_id' => $expl->id, 'quantite_disponible' => 5, 'seuil_minimum' => 1]);

        try {
            app(StockService::class)->enregistrer($user, $intrant->id, MouvementStock::CONSOMMATION, 8, $campagne->id);
            $this->fail('Une QuantiteInsuffisanteException était attendue.');
        } catch (QuantiteInsuffisanteException) {
        }

        $this->assertEquals(5, $intrant->fresh()->quantite_disponible);
        $this->assertSame(0, MouvementStock::withoutGlobalScopes()->count());
    }

    public function test_une_consommation_doit_etre_rattachee_a_une_campagne(): void
    {
        [$expl, $user] = $this->contexte();
        $intrant = Intrant::factory()->create(['exploitation_id' => $expl->id]);

        $this->expectException(StatutInvalideException::class);
        app(StockService::class)->enregistrer($user, $intrant->id, MouvementStock::CONSOMMATION, 1);
    }

    public function test_une_consommation_met_a_jour_le_stock_et_trace_le_mouvement(): void
    {
        [$expl, $user] = $this->contexte('technicien');
        $campagne = $this->campagneEnCours($expl);
        $intrant = Intrant::factory()->create(['exploitation_id' => $expl->id, 'quantite_disponible' => 100, 'seuil_minimum' => 10]);

        $m = app(StockService::class)->enregistrer($user, $intrant->id, MouvementStock::CONSOMMATION, 30, $campagne->id);

        $this->assertEquals(70, $intrant->fresh()->quantite_disponible);
        $this->assertEquals(70, $m->stock_apres);
        $this->assertSame($campagne->id, $m->campagne_id);
    }

    public function test_passer_sous_le_seuil_declenche_l_evenement_stock_critique(): void
    {
        Event::fake([StockCritique::class]);
        [$expl, $user] = $this->contexte();
        $campagne = $this->campagneEnCours($expl);
        $intrant = Intrant::factory()->create(['exploitation_id' => $expl->id, 'quantite_disponible' => 30, 'seuil_minimum' => 20]);

        app(StockService::class)->enregistrer($user, $intrant->id, MouvementStock::CONSOMMATION, 15, $campagne->id);

        Event::assertDispatched(StockCritique::class);
    }
}
