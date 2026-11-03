<?php

namespace Tests\Feature;

use App\Services\LotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

/** Test d'API : authentification, JSON structuré, codes HTTP, validation. */
class ApiTest extends TestCase
{
    use RefreshDatabase, CreatesAgroContext;

    public function test_les_routes_protegees_exigent_un_jeton(): void
    {
        $this->getJson('/api/lots')->assertUnauthorized();
        $this->postJson('/api/recoltes', [])->assertUnauthorized();
    }

    public function test_obtenir_un_jeton_avec_des_identifiants_valides(): void
    {
        [, $user] = $this->contexte();

        $this->postJson('/api/auth/token', ['email' => $user->email, 'password' => 'password'])
            ->assertCreated()->assertJsonStructure(['token', 'token_type']);

        $this->postJson('/api/auth/token', ['email' => $user->email, 'password' => 'faux'])
            ->assertUnauthorized();
    }

    public function test_consulter_un_lot_par_son_code(): void
    {
        [$expl, $user] = $this->contexte();
        $r = $this->recolte($expl, 500);
        $lot = app(LotService::class)->composer($user, [['recolte_id' => $r->id, 'quantite_kg' => 120]]);

        Sanctum::actingAs($user);

        $this->getJson('/api/lots/'.$lot->code)
            ->assertOk()
            ->assertJsonPath('data.code', $lot->code)
            ->assertJsonPath('data.poids_total_kg', 120.0)
            ->assertJsonStructure(['data' => ['id', 'code', 'statut', 'poids_total_kg', 'recoltes']]);
    }

    public function test_creer_une_recolte_via_l_api(): void
    {
        [$expl, $user] = $this->contexte('technicien');
        $campagne = $this->campagneEnCours($expl);
        Sanctum::actingAs($user);

        $this->postJson('/api/recoltes', [
            'campagne_id' => $campagne->id,
            'date_recolte' => now()->toDateString(),
            'quantite_kg' => 320.5,
            'calibre' => 'gros',
        ])->assertCreated()
          ->assertJsonPath('data.quantite_kg', 320.5)
          ->assertJsonPath('data.quantite_disponible_kg', 320.5);
    }

    public function test_une_donnee_invalide_renvoie_422(): void
    {
        [$expl, $user] = $this->contexte();
        $campagne = $this->campagneEnCours($expl);
        Sanctum::actingAs($user);

        $this->postJson('/api/recoltes', [
            'campagne_id' => $campagne->id,
            'date_recolte' => now()->toDateString(),
            'quantite_kg' => -5,
        ])->assertUnprocessable()->assertJsonValidationErrors(['quantite_kg']);
    }

    public function test_une_recolte_sur_une_campagne_terminee_est_refusee(): void
    {
        [$expl, $user] = $this->contexte();
        $campagne = $this->campagneEnCours($expl);
        $campagne->update(['statut' => 'terminee']);
        Sanctum::actingAs($user);

        $this->postJson('/api/recoltes', [
            'campagne_id' => $campagne->id,
            'date_recolte' => now()->toDateString(),
            'quantite_kg' => 100,
        ])->assertUnprocessable()->assertJsonStructure(['message']);
    }
}
