# AgroTrace — Gestion d'une exploitation fruitière avec traçabilité des lots

Projet individuel Laravel — Licence 3 SIO (ESMIA, 2026).
Exploitation : **production fruitière (litchi)** · Module spécialisé : **récolte et lots** ·
Contrainte métier : **traçabilité parcelle → récolte → lot → vente**.

> ⚠ **Ce dépôt est un point de départ à personnaliser.** Le sujet impose un projet individuel
> défendu oralement. Relisez chaque fichier, adaptez les noms, les données de démonstration et
> les règles, et soyez capable d'expliquer chaque partie (voir « Questions de soutenance »).

## Prérequis
PHP ≥ 8.2, Composer, SQLite (par défaut) ou MySQL 8 / PostgreSQL, Node non requis (Bootstrap par CDN).

## Installation

```bash
# 1. Créer un projet Laravel 11 ou 12 vierge
composer create-project laravel/laravel agrotrace
cd agrotrace

# 2. Installer l'API (Sanctum) AVANT de copier les fichiers
php artisan install:api

# 3. Copier le contenu de cette archive PAR-DESSUS le projet
#    (app/, bootstrap/app.php, database/, resources/views/, routes/, tests/)
#    Acceptez le remplacement de User.php, UserFactory.php, DatabaseSeeder.php,
#    AppServiceProvider.php, routes/web.php, routes/api.php, routes/console.php, bootstrap/app.php.

# 4. (Optionnel) QR code du lot sur la page de détail
composer require simplesoftwareio/simple-qrcode

# 5. Configurer .env (voir .env.additions.example), puis :
php artisan migrate:fresh --seed
php artisan serve
```

Dans deux autres terminaux :

```bash
php artisan queue:work          # worker : Jobs (fiche de traçabilité) et notifications
php artisan schedule:work       # planificateur : alerte quotidienne des récoltes non affectées
```

Reconstruction propre de la base pour la démonstration : `php artisan migrate:fresh --seed`.

### Base de données
- **SQLite** (défaut) : `touch database/database.sqlite`, `DB_CONNECTION=sqlite`.
- **MySQL** : renseigner `DB_*` dans `.env`. Les contraintes `CHECK` (stock et récolte non négatifs)
  sont alors créées en base ; elles sont ignorées sous SQLite.

## Comptes de démonstration (mot de passe : `password`)

| Exploitation | Rôle | Email |
|---|---|---|
| Vergers Ambodisaina | admin | admin@ambodisaina.test |
| Vergers Ambodisaina | responsable | responsable@ambodisaina.test |
| Vergers Ambodisaina | technicien | technicien@ambodisaina.test |
| Litchis de Foulpointe | admin / responsable / technicien | admin@foulpointe.test … |

Les deux exploitations servent à démontrer l'isolation des données.

## Tests

```bash
php artisan test
```

| Fichier | Ce qui est démontré |
|---|---|
| `LotServiceTest` | Fonctionnalité métier, **transaction/rollback**, intégrité des quantités, concurrence séquentielle |
| `StockServiceTest` | Stock jamais négatif, consommation rattachée à une campagne, événement `StockCritique` |
| `IsolationTest` | **Règle critique** : l'exploitation A ne voit jamais B (web et API) |
| `AutorisationTest` | Policies, Gates, middleware de rôle (contrôle serveur) |
| `ApiTest` | Jeton Sanctum, JSON structuré, codes 201/401/404/422 |
| `TracabiliteTest` | Page publique de traçabilité (module personnel) |
| `DashboardCacheTest` | Mise en cache et invalidation par événement, indicateurs calculés |

## Architecture

| Responsabilité | Fichiers |
|---|---|
| Models + isolation SaaS | `app/Models/*`, `Concerns/BelongsToExploitation` + `ExploitationScope` (global scope) |
| Controllers (fins) | `app/Http/Controllers/*`, `Api/*` |
| Form Requests | `app/Http/Requests/*` (partagés web/API) |
| **Services (logique métier)** | `LotService`, `StockService`, `HarvestService`, `FinanceService`, `ActiviteService`, `DashboardService`, `TracabiliteService` |
| Policies / Gates | `app/Policies/*`, Gates dans `AppServiceProvider` |
| Middleware | `RoleMiddleware` (alias `role`) |
| Events / Listeners | `RecolteEnregistree`, `LotCree`, `LotVendu`, `StockCritique` → `InvalidateDashboardCache`, `JournaliserLotCree`, `NotifierStockCritique` |
| Notifications | `StockCritiqueNotification` (database + mail), `RecolteNonAffecteeNotification` (database) |
| Jobs / Queues / Scheduler | `GenererFicheTracabilite`, commande `recoltes:alerter` (quotidienne) |
| API Resources | `LotResource`, `RecolteResource`, `TracabiliteResource` |

### Modèle de données (12 tables)
`exploitations`, `users`, `parcelles`, `campagnes`, `activites`, `intrants`, `mouvements_stock`,
`recoltes`, `lots`, `lot_recoltes` (pivot), `ventes`, `transactions_financieres` (+ `notifications`, tables techniques Laravel).
Chaque table métier porte `exploitation_id` (isolation stricte) — c'est une légère évolution par rapport au MCD
initial, où seules certaines tables le portaient.

### Règles métier
1. Un lot ne contient que des récoltes de la même exploitation.
2. Le poids affecté aux lots ne dépasse jamais la quantité récoltée (`quantite_disponible_kg` ≥ 0).
3. Un lot clôturé ou vendu n'est plus modifiable ; seul un lot **clôturé** peut être vendu.
4. Une seule campagne « en cours » par parcelle ; une récolte exige une campagne en cours.
5. Le stock d'un intrant ne devient jamais négatif ; une consommation est rattachée à une campagne.
6. Une récolte déjà affectée à un lot ne peut pas être supprimée (`restrictOnDelete`).

### Transaction (exigence 19)
`LotService::composer()` : création du lot + lignes du pivot + décrément des récoltes dans un seul
`DB::transaction()`. Toute exception → rollback complet (testé).
`StockService`, `FinanceService::vendreLot()`, `ActiviteService` suivent le même principe.

### Concurrence (exigence 20)
Risque : deux agents puisent en même temps dans la même récolte (ou consomment les derniers sacs d'un intrant).
Solution : verrou pessimiste `lockForUpdate()` dans la transaction **et** décrément conditionnel
`WHERE quantite_disponible_kg >= :q` (0 ligne modifiée ⇒ refus). Le second utilisateur est sérialisé,
relit la valeur à jour et reçoit une erreur claire.

### Cache (exigence 24)
Statistiques du tableau de bord cachées 10 min par exploitation (`dashboard:exploitation:{id}`).
Invalidation : événements métier (listener unique), plus `oublier()` à la création d'une activité,
d'une campagne ou d'un mouvement de stock.

### Sécurité (exigence 26)
Validation (Form Requests, règles `exists` limitées à l'exploitation), CSRF (`@csrf`), XSS (`{{ }}`),
mass assignment (`$fillable` + données validées uniquement, `role`/`exploitation_id` fixés côté serveur),
authentification (session + Sanctum), autorisations (Policies, Gates, middleware), throttling sur login/token,
session régénérée à la connexion.

### Journalisation (exigence 28)
Échecs de connexion, accès interdits (`Gate::after`, `RoleMiddleware`), violations de règles métier,
création de lots, génération de fiches, alertes planifiées → `storage/logs/laravel.log`.

## API REST

Authentification : `POST /api/auth/token` (`email`, `password`) → jeton Bearer.

| Méthode | Route | Description | Codes |
|---|---|---|---|
| GET | `/api/lots` | Liste paginée (filtre `statut`) | 200, 401 |
| GET | `/api/lots/{code}` | Détail d'un lot | 200, 401, 404 |
| GET | `/api/lots/{code}/tracabilite` | Origine du lot (parcelles, dates) | 200, 404, 409 |
| GET | `/api/recoltes` | Liste (filtre `non_affectees=1`) | 200 |
| POST | `/api/recoltes` | Enregistrer une récolte | 201, 422 |
| PATCH | `/api/recoltes/{id}` | Modifier calibre / notes | 200, 403, 404 |

Exemple :
```bash
curl -X POST http://localhost:8000/api/recoltes \
  -H "Authorization: Bearer <jeton>" -H "Accept: application/json" \
  -d "campagne_id=1&date_recolte=2026-10-10&quantite_kg=320.5&calibre=gros"
```

## Module personnel : traçabilité publique
`GET /tracabilite/{code}` — page publique (sans connexion), accessible via le QR code d'un lot **clôturé ou vendu** :
parcelle d'origine, variété, dates de récolte. Aucune donnée financière. Justification à développer dans le rapport :
répondre à une exigence réelle des acheteurs/exportateurs de fruits.

## Indicateurs calculés (tableau de bord)
- Rendement (kg/ha) = récolte totale / superficie des parcelles.
- Part de récolte non affectée à un lot (%) = risque de perte potentielle.
- Coût de production par kg de lot = dépenses / poids total des lots.
- Marge simplifiée = recettes − dépenses.

## Suggestion d'historique Git (le dépôt doit montrer une progression réelle)
1. `chore: init projet Laravel + Sanctum`
2. `feat: migrations et modèles (exploitations, parcelles, campagnes)`
3. `feat: auth, rôles, isolation par exploitation (global scope)`
4. `feat: récoltes et HarvestService`
5. `feat: LotService (transaction + verrou) et tests`
6. `feat: stocks, mouvements, alerte stock critique (event, listener, notification)`
7. `feat: finances, ventes, tableau de bord et cache`
8. `feat: API REST + resources + tests API`
9. `feat: job fiche de traçabilité + scheduler`
10. `feat: page publique de traçabilité (module personnel)`
11. `docs: README, rapport, MCD`

## Questions de soutenance (préparez-les)
- Suivre une requête `POST /lots` de la route jusqu'à la base (FormRequest → Policy → LotService → transaction).
- Pourquoi `lockForUpdate()` **et** un décrément conditionnel ? Que se passe-t-il sans eux ?
- Comment fonctionne `ExploitationScope` ? Pourquoi `pourExploitation()` dans les services ?
- Différence Policy / Gate / middleware de rôle ; où est-elle utilisée ici ?
- Pourquoi un listener non typé pour l'invalidation du cache ?
- Modifier une validation, ajouter un champ via une migration, exécuter un test.

## Limites et perspectives (pour le rapport)
Fiche de traçabilité en HTML (PDF via dompdf possible) ; une vente par lot ; pas de gestion multi-devise ;
tests de concurrence réelle (processus parallèles) non automatisés ; perspectives : application mobile consommant l'API,
rappels de DAR (délai avant récolte), export comptable.
