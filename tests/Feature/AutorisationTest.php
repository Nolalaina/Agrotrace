<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

/** Test d'autorisation : contrôle serveur, indépendant de l'affichage des boutons. */
class AutorisationTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_un_technicien_ne_peut_pas_composer_un_lot(): void
    {
        [$expl, $tech] = $this->contexte('technicien');
        $r = $this->recolte($expl, 500);

        $this->actingAs($tech)->post(route('lots.store'), [
            'lignes' => [['recolte_id' => $r->id, 'quantite_kg' => 100]],
        ])->assertForbidden();

        $this->assertEquals(500, $r->fresh()->quantite_disponible_kg);
    }

    public function test_un_responsable_peut_composer_un_lot(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        $r = $this->recolte($expl, 500);

        $this->actingAs($resp)->post(route('lots.store'), [
            'lignes' => [['recolte_id' => $r->id, 'quantite_kg' => 100]],
        ])->assertRedirect();

        $this->assertEquals(400, $r->fresh()->quantite_disponible_kg);
    }

    public function test_un_technicien_n_acces_pas_aux_finances(): void
    {
        [, $tech] = $this->contexte('technicien');

        $this->actingAs($tech)->get(route('finances.index'))->assertForbidden();
    }

    public function test_un_responsable_acces_aux_finances(): void
    {
        [, $resp] = $this->contexte('responsable');

        $this->actingAs($resp)->get(route('finances.index'))->assertOk();
    }

    public function test_le_technicien_ne_voit_pas_les_finances_sur_le_dashboard(): void
    {
        [, $tech] = $this->contexte('technicien');

        $this->actingAs($tech)->get(route('dashboard'))
            ->assertOk()->assertDontSee('Marge simplifiée');
    }

    public function test_seul_l_admin_gere_les_utilisateurs(): void
    {
        [, $resp] = $this->contexte('responsable');
        [, $admin] = $this->contexte('admin');

        $this->actingAs($resp)->get(route('users.index'))->assertForbidden();
        $this->actingAs($admin)->get(route('users.index'))->assertOk();
    }

    public function test_un_visiteur_non_connecte_est_redirige_vers_la_connexion(): void
    {
        $this->get(route('lots.index'))->assertRedirect(route('login'));
    }

    public function test_un_technicien_peut_enregistrer_une_recolte(): void
    {
        [$expl, $tech] = $this->contexte('technicien');
        $campagne = $this->campagneEnCours($expl);

        $this->actingAs($tech)->post(route('recoltes.store'), [
            'campagne_id' => $campagne->id,
            'date_recolte' => now()->toDateString(),
            'quantite_kg' => 250,
            'calibre' => 'moyen',
        ])->assertRedirect(route('recoltes.index'));

        $this->assertDatabaseHas('recoltes', ['campagne_id' => $campagne->id, 'quantite_disponible_kg' => 250]);
    }
}
