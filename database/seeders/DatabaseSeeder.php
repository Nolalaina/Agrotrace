<?php

namespace Database\Seeders;

use App\Models\Activite;
use App\Models\Campagne;
use App\Models\Exploitation;
use App\Models\Intrant;
use App\Models\MouvementStock;
use App\Models\Parcelle;
use App\Models\Recolte;
use App\Models\TransactionFinanciere;
use App\Models\User;
use App\Services\FinanceService;
use App\Services\LotService;
use App\Services\StockService;
use Illuminate\Database\Seeder;

/**
 * Jeu de démonstration : deux exploitations de litchi (pour montrer l'isolation).
 * ⚠ À PERSONNALISER : noms, lieux, quantités, scénarios doivent être les vôtres.
 * Comptes : mot de passe « password » pour tous.
 */
class DatabaseSeeder extends Seeder
{
    public function run(StockService $stock, LotService $lots, FinanceService $finance): void
    {
        $this->exploitation($stock, $lots, $finance, [
            'nom' => 'Vergers Ambodisaina',
            'localisation' => 'Mahambo, Analanjirofo',
            'superficie_ha' => 18,
            'slug' => 'ambodisaina',
            'parcelles' => [
                ['AMB-P01', 'Tanambao Haut', 3.5, 'Mauritius'],
                ['AMB-P02', 'Bas-fond Rano', 2.2, 'Tai So'],
                ['AMB-P03', 'Colline Est', 4.0, 'Mauritius'],
            ],
        ]);

        $this->exploitation($stock, $lots, $finance, [
            'nom' => 'Litchis de Foulpointe',
            'localisation' => 'Foulpointe, Atsinanana',
            'superficie_ha' => 11,
            'slug' => 'foulpointe',
            'parcelles' => [
                ['FOU-P01', 'Ankorera', 2.8, 'Mauritius'],
                ['FOU-P02', 'Vohimasina', 3.1, 'Wai Chee'],
            ],
        ]);
    }

    private function exploitation(StockService $stock, LotService $lots, FinanceService $finance, array $c): void
    {
        $expl = Exploitation::create([
            'nom' => $c['nom'], 'localisation' => $c['localisation'],
            'type' => 'production_fruitiere', 'superficie_ha' => $c['superficie_ha'], 'statut' => 'active',
        ]);
        $id = $expl->id;
        $s = $c['slug'];

        $admin = User::create(['name' => 'Admin '.$c['nom'], 'email' => "admin@{$s}.test", 'password' => 'password',
            'role' => User::ROLE_ADMIN, 'exploitation_id' => $id]);
        $resp = User::create(['name' => 'Responsable '.$c['nom'], 'email' => "responsable@{$s}.test", 'password' => 'password',
            'role' => User::ROLE_RESPONSABLE, 'exploitation_id' => $id]);
        $tech = User::create(['name' => 'Technicien '.$c['nom'], 'email' => "technicien@{$s}.test", 'password' => 'password',
            'role' => User::ROLE_TECHNICIEN, 'exploitation_id' => $id]);

        $campagnes = [];
        foreach ($c['parcelles'] as [$code, $nom, $ha, $variete]) {
            $p = Parcelle::create([
                'exploitation_id' => $id, 'code' => $code, 'nom' => $nom, 'superficie_ha' => $ha,
                'localisation' => $c['localisation'], 'variete' => $variete, 'etat' => 'productive',
                'date_creation' => now()->subYears(8)->toDateString(),
            ]);

            // Campagne passée terminée + campagne en cours
            Campagne::create(['exploitation_id' => $id, 'parcelle_id' => $p->id, 'libelle' => "Litchi {$code} 2024-2025",
                'date_debut' => '2024-09-15', 'date_fin_prevue' => '2025-01-31', 'statut' => Campagne::TERMINEE]);
            $campagnes[] = Campagne::create(['exploitation_id' => $id, 'parcelle_id' => $p->id, 'libelle' => "Litchi {$code} 2026-2027",
                'date_debut' => now()->subMonths(2)->toDateString(), 'date_fin_prevue' => now()->addMonths(3)->toDateString(),
                'statut' => Campagne::EN_COURS]);
        }

        // Stocks : un intrant volontairement proche du seuil critique
        $npk = Intrant::create(['exploitation_id' => $id, 'nom' => 'NPK 15-15-15', 'categorie' => 'engrais', 'unite' => 'kg',
            'quantite_disponible' => 0, 'seuil_minimum' => 50]);
        $caisses = Intrant::create(['exploitation_id' => $id, 'nom' => 'Caisses de 10 kg', 'categorie' => 'emballage', 'unite' => 'pièce',
            'quantite_disponible' => 0, 'seuil_minimum' => 100]);
        $cuivre = Intrant::create(['exploitation_id' => $id, 'nom' => 'Bouillie bordelaise', 'categorie' => 'phytosanitaire', 'unite' => 'kg',
            'quantite_disponible' => 0, 'seuil_minimum' => 10]);

        $stock->enregistrer($admin, $npk->id, MouvementStock::STOCK_INITIAL, 200);
        $stock->enregistrer($admin, $caisses->id, MouvementStock::STOCK_INITIAL, 600);
        $stock->enregistrer($admin, $cuivre->id, MouvementStock::STOCK_INITIAL, 40);
        $stock->enregistrer($resp, $npk->id, MouvementStock::CONSOMMATION, 145, $campagnes[0]->id); // → 55 kg, proche du seuil
        $stock->enregistrer($tech, $cuivre->id, MouvementStock::CONSOMMATION, 12, $campagnes[0]->id);

        // Activités et dépenses
        foreach ($campagnes as $i => $camp) {
            Activite::create(['exploitation_id' => $id, 'campagne_id' => $camp->id, 'user_id' => $tech->id,
                'type' => 'taille', 'date' => now()->subDays(60 - $i)->toDateString(),
                'description' => 'Taille de formation après la saison précédente', 'cout' => 90000]);
            Activite::create(['exploitation_id' => $id, 'campagne_id' => $camp->id, 'user_id' => $tech->id,
                'type' => 'traitement', 'date' => now()->subDays(35 - $i)->toDateString(),
                'description' => 'Traitement cuprique avant floraison', 'cout' => 45000]);
            TransactionFinanciere::create(['exploitation_id' => $id, 'campagne_id' => $camp->id, 'type' => 'depense',
                'categorie' => 'main_oeuvre', 'libelle' => 'Main-d\'œuvre de récolte', 'montant' => 180000,
                'date_transaction' => now()->subDays(10)->toDateString()]);
        }

        // Récoltes
        $recoltes = [];
        foreach ($campagnes as $i => $camp) {
            foreach ([[14, 520, 'gros'], [9, 410, 'moyen'], [4, 300, 'moyen']] as [$jours, $kg, $calibre]) {
                $recoltes[] = Recolte::create([
                    'exploitation_id' => $id, 'campagne_id' => $camp->id, 'user_id' => $tech->id,
                    'date_recolte' => now()->subDays($jours + $i)->toDateString(),
                    'quantite_kg' => $kg + $i * 40, 'quantite_disponible_kg' => $kg + $i * 40,
                    'calibre' => $calibre, 'notes' => 'Récolte du matin',
                ]);
            }
        }

        // Lot 1 : composé puis clôturé puis vendu ; lot 2 : clôturé ; lot 3 : ouvert
        $lot1 = $lots->composer($resp, [
            ['recolte_id' => $recoltes[0]->id, 'quantite_kg' => 500],
            ['recolte_id' => $recoltes[1]->id, 'quantite_kg' => 400],
        ], 'extra');
        $lots->cloturer($resp, $lot1->id);
        $finance->vendreLot($resp, $lot1->id, 'Exportateur Tamatave Fruits', 4_500_000);

        $lot2 = $lots->composer($resp, [['recolte_id' => $recoltes[2]->id, 'quantite_kg' => 250]], 'premiere');
        $lots->cloturer($resp, $lot2->id);

        $lots->composer($resp, [['recolte_id' => $recoltes[3]->id, 'quantite_kg' => 300]], 'standard');
    }
}
