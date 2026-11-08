<?php

namespace Tests\Feature;

use App\Jobs\GenererFicheTracabilite;
use App\Models\Campagne;
use App\Models\Lot;
use App\Models\Parcelle;
use App\Models\Recolte;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\CreatesAgroContext;
use Tests\TestCase;

class FeaturesAndButtonsTest extends TestCase
{
    use CreatesAgroContext, RefreshDatabase;

    public function test_un_responsable_peut_supprimer_une_recolte_non_affectee(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        $recolte = $this->recolte($expl, 300);

        $this->actingAs($resp)->delete(route('recoltes.destroy', $recolte))
            ->assertRedirect(route('recoltes.index'))
            ->assertSessionHas('succes');

        $this->assertDatabaseMissing('recoltes', ['id' => $recolte->id]);
    }

    public function test_un_responsable_ne_peut_pas_supprimer_une_recolte_affectee(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        $recolte = $this->recolte($expl, 300);

        // Affecter 100 kg à un lot
        $this->actingAs($resp)->post(route('lots.store'), [
            'lignes' => [['recolte_id' => $recolte->id, 'quantite_kg' => 100]],
        ]);

        $this->actingAs($resp)->delete(route('recoltes.destroy', $recolte))
            ->assertSessionHasErrors(['regle']);

        $this->assertDatabaseHas('recoltes', ['id' => $recolte->id]);
    }

    public function test_un_technicien_ne_peut_pas_supprimer_une_recolte(): void
    {
        [$expl, $tech] = $this->contexte('technicien');
        $recolte = $this->recolte($expl, 300);

        $this->actingAs($tech)->delete(route('recoltes.destroy', $recolte))
            ->assertForbidden();

        $this->assertDatabaseHas('recoltes', ['id' => $recolte->id]);
    }

    public function test_filtrer_parcelles_par_etat(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        Parcelle::factory()->create(['exploitation_id' => $expl->id, 'nom' => 'Parcelle Nord', 'etat' => 'productive']);
        Parcelle::factory()->create(['exploitation_id' => $expl->id, 'nom' => 'Parcelle Sud', 'etat' => 'en_repos']);

        $response = $this->actingAs($resp)->get(route('parcelles.index', ['etat' => 'productive']));
        $response->assertOk();
        $response->assertSee('Parcelle Nord');
        $response->assertDontSee('Parcelle Sud');
    }

    public function test_filtrer_campagnes_par_statut(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        Campagne::factory()->create(['exploitation_id' => $expl->id, 'libelle' => 'Campagne Active', 'statut' => 'en_cours']);
        Campagne::factory()->create(['exploitation_id' => $expl->id, 'libelle' => 'Campagne Terminee', 'statut' => 'cloturee']);

        $response = $this->actingAs($resp)->get(route('campagnes.index', ['statut' => 'en_cours']));
        $response->assertOk();
        $response->assertSee('Campagne Active');
        $response->assertDontSee('Campagne Terminee');
    }

    public function test_filtrer_recoltes_par_calibre(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        $campagne = $this->campagneEnCours($expl);

        Recolte::factory()->create([
            'exploitation_id' => $expl->id,
            'campagne_id' => $campagne->id,
            'calibre' => 'gros',
            'quantite_kg' => 120,
        ]);
        Recolte::factory()->create([
            'exploitation_id' => $expl->id,
            'campagne_id' => $campagne->id,
            'calibre' => 'petit',
            'quantite_kg' => 80,
        ]);

        $response = $this->actingAs($resp)->get(route('recoltes.index', ['calibre' => 'gros']));
        $response->assertOk();
        $response->assertSee('120.00');
        $response->assertDontSee('80.00');
    }

    public function test_apercu_fiche_tracabilite_en_ligne(): void
    {
        [$expl, $resp] = $this->contexte('responsable');
        $recolte = $this->recolte($expl, 500);

        // Composer un lot
        $this->actingAs($resp)->post(route('lots.store'), [
            'lignes' => [['recolte_id' => $recolte->id, 'quantite_kg' => 200]],
        ]);

        $lot = Lot::first();

        // Clôturer le lot
        $this->actingAs($resp)->post(route('lots.cloturer', $lot));

        // Générer la fiche
        GenererFicheTracabilite::dispatchSync($lot);

        // Télécharger
        $this->actingAs($resp)->get(route('lots.fiche.telecharger', $lot))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename='.$lot->code.'.html');

        // Aperçu direct / impression
        $this->actingAs($resp)->get(route('lots.fiche.telecharger', ['lot' => $lot, 'apercu' => 1]))
            ->assertOk()
            ->assertHeader('content-type', 'text/html; charset=utf-8')
            ->assertHeader('content-disposition', 'inline; filename="'.$lot->code.'.html"');
    }
}
