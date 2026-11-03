<?php

namespace Tests\Feature;

use App\Models\Lot;
use App\Models\Parcelle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

/** Règle critique : un utilisateur de l'exploitation A ne voit jamais les données de B. */
class IsolationTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_un_utilisateur_ne_peut_pas_consulter_un_lot_d_une_autre_exploitation(): void
    {
        [, $userA] = $this->contexte();
        [$explB] = $this->contexte();
        $lotB = Lot::factory()->create(['exploitation_id' => $explB->id]);

        $this->actingAs($userA)->get(route('lots.show', $lotB))->assertNotFound();
    }

    public function test_la_liste_des_parcelles_est_limitee_a_l_exploitation(): void
    {
        [$explA, $userA] = $this->contexte();
        [$explB] = $this->contexte();
        Parcelle::factory()->create(['exploitation_id' => $explA->id, 'code' => 'A-VISIBLE']);
        Parcelle::factory()->create(['exploitation_id' => $explB->id, 'code' => 'B-SECRET']);

        $this->actingAs($userA)->get(route('parcelles.index'))
            ->assertOk()->assertSee('A-VISIBLE')->assertDontSee('B-SECRET');
    }

    public function test_l_api_renvoie_404_pour_un_lot_d_une_autre_exploitation(): void
    {
        [, $userA] = $this->contexte();
        [$explB] = $this->contexte();
        $lotB = Lot::factory()->create(['exploitation_id' => $explB->id]);

        Sanctum::actingAs($userA);

        $this->getJson('/api/lots/'.$lotB->code)->assertNotFound();
    }

    public function test_modifier_une_parcelle_d_une_autre_exploitation_est_impossible(): void
    {
        [, $userA] = $this->contexte();
        [$explB] = $this->contexte();
        $parcelleB = Parcelle::factory()->create(['exploitation_id' => $explB->id]);

        $this->actingAs($userA)->put(route('parcelles.update', $parcelleB), [
            'code' => 'X', 'nom' => 'Piratée', 'superficie_ha' => 1, 'variete' => 'X', 'etat' => 'productive',
        ])->assertNotFound();

        $this->assertNotSame('Piratée', $parcelleB->fresh()->nom);
    }
}
