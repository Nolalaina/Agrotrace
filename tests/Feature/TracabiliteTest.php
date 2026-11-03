<?php

namespace Tests\Feature;

use App\Services\LotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

/** Module personnel : page publique de traçabilité. */
class TracabiliteTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_la_page_publique_montre_la_parcelle_d_origine_d_un_lot_cloture(): void
    {
        [$expl, $user] = $this->contexte();
        $r = $this->recolte($expl, 500);
        $parcelle = $r->campagne->parcelle;
        $service = app(LotService::class);
        $lot = $service->composer($user, [['recolte_id' => $r->id, 'quantite_kg' => 200]]);
        $service->cloturer($user, $lot->id);

        $this->get(route('tracabilite.public', $lot->code))
            ->assertOk()
            ->assertSee($parcelle->code)
            ->assertSee($parcelle->variete)
            ->assertDontSee('Montant');
    }

    public function test_un_lot_ouvert_n_est_pas_public(): void
    {
        [$expl, $user] = $this->contexte();
        $r = $this->recolte($expl, 500);
        $lot = app(LotService::class)->composer($user, [['recolte_id' => $r->id, 'quantite_kg' => 200]]);

        $this->get(route('tracabilite.public', $lot->code))->assertNotFound();
    }
}
