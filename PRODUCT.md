# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Exploitant agricole (utilisateur principal) : gérant ou opérateur d'une exploitation fruitière
(litchi), utilisant l'outil au bureau sur ordinateur, au quotidien. Trois rôles authentifiés :
admin (gestion complète, utilisateurs), responsable (pilotage, finances), technicien (saisie et
consultation terrain). Une audience publique sans authentification accède à la page de
traçabilité via le QR code d'un lot clôturé ou vendu (acheteurs / vérificateurs).

## Product Purpose

AgroTrace permet à une exploitation fruitière de gérer sa production et de garantir la
traçabilité complète **parcelle → récolte → lot → vente**. Le succès se mesure à la capacité de
retracer, pour chaque lot vendu, ses parcelles d'origine, ses dates de récolte, ses poids et ses
mouvements — sans jamais mélanger les données de deux exploitations distinctes, et en gardant
les quantités physiques cohérentes (pas de poids affecté supérieur au récolté, stock jamais
négatif).

## Positioning

Traçabilité de bout en bout vérifiable publiquement (page `/tracabilite/{code}` + QR code) :
la preuve d'origine d'un lot est consultable par un acheteur sans compte. L'isolation stricte
des données par exploitation (global scope `ExploitationScope`, `BelongsToExploitation`) est le
socle non négociable du produit.

## Operating Context

- Quotidien au bureau sur ordinateur ; interface en français.
- Modules : tableau de bord, parcelles, campagnes, activités, récoltes, stocks (intrants), lots,
  finances, utilisateurs.
- Flux clé : enregistrement d'une récolte → création d'un lot (transaction, verrou pessimiste) →
  clôture → vente → fiche de traçabilité (job en file d'attente) → QR code public.
- Deux exploitations de démonstration (Vergers Ambodisaina, Litchis de Foulpointe) servent à
  illustrer l'isolation ; comptes seedés (voir README).
- Worker (`queue:work`) pour les jobs et notifications ; planificateur (`schedule:work`) pour
  l'alerte quotidienne des récoltes non affectées.
- `storage/logs/laravel.log` journalise connexions échouées, accès interdits, violations de
  règles métier, création de lots et fiches.

## Capabilities and Constraints

Fonctionnalités confirmées (README et code) : CRUD parcelles/campagnes/activités/récoltes,
gestion des intrants et mouvements de stock, composition/clôture/vente de lots, tableau de bord
avec cache (10 min par exploitation), finances (recettes/dépenses, marge simplifiée), gestion des
utilisateurs (admin), API REST Sanctum (`/api/lots`, `/api/recoltes`, `/api/auth/token`), page
publique de traçabilité.

Règles métier à préserver :

1. Un lot ne contient que des récoltes de la même exploitation.
2. Le poids affecté aux lots ne dépasse jamais la quantité récoltée (`quantite_disponible_kg`).
3. Un lot clôturé ou vendu n'est plus modifiable ; seul un lot **clôturé** peut être vendu.
4. Une seule campagne « en cours » par parcelle ; une récolte exige une campagne en cours.
5. Le stock d'un intrant ne devient jamais négatif ; une consommation est rattachée à une
   campagne.
6. Une récolte déjà affectée à un lot ne peut pas être supprimée (`restrictOnDelete`).

Contraintes techniques : Laravel (PHP 8.4) + Blade + Vite + Tailwind CSS v4, base SQLite (défaut)
ou MySQL 8 / PostgreSQL, authentification session web + Sanctum pour l'API, Policies/Gates et
middleware de rôle pour les autorisations, `lockForUpdate()` et décrément conditionnel pour la
concurrence, `DB::transaction()` pour la composition des lots. Isolation par exploitation non
contournable (requêtes et validations `exists` limitées à l'exploitation).

Décisions produit non arbitrées : cible de déploiement en production et éventuelle ouverture
commerciale (le README mentionne un projet individuel de soutenance ESMIA 2026) ; la refonte
visuelle ne doit pas les présumer.

## Brand Commitments

- Le **nom AgroTrace est conservé** (décision explicite de l'utilisateur).
- Identité visuelle : l'utilisateur a explicitement accepté un rebranding complet ; la direction
  visuelle sera arbitrée lors de la refonte, pas ici.
- Interface en français ; tutoiement/vouvoiement à conserver tel quel dans les textes existants.
- Aucun logo officiel fourni ; le pictogramme feuille actuel est remplaçable.

## Evidence on Hand

- Code complet dans le dépôt (migrations, modèles, services métier, tests Pest/PHPUnit,
  vues Blade, `resources/css/app.css`, `resources/js/app.js`).
- README : installation, comptes de démonstration (mot de passe `password`), règles métier,
  documentation des exigences (18 à 28), API REST.
- Données : base seedée pour la démonstration (deux exploitations).
- Absences à ne pas fabriquer : aucune donnée client réelle, aucun témoignage, aucune métrique
  commerciale, aucune marque tierce partenaire. La page publique de traçabilité n'affiche aucune
  donnée financière.

## Product Principles

1. **La traçabilité est la preuve** : toute interface doit rendre le chemin récolte → lot → vente
   visible et vérifiable, jamais décoratif.
2. **Isolation stricte** : aucune vue ne doit laisser entrevoir les données d'une autre
   exploitation ; les contrôles restent côté serveur.
3. **Bureau d'abord** : densité maîtrisée et scannabilité sur ordinateur priment sur l'effet ;
   le terrain (saisie rapide) reste servi par des formulaires courts.
4. **Cohérence avant nouveauté** : une action métier doit être au même endroit et se comporter
   pareil partout (lots, stocks, récoltes).
5. **Français, clair, honnête** : pas de jargon inventé, pas de données fictives dans les écrans
   livrés.

## Accessibility & Inclusion

Aucune exigence spécifique établie par le produit ; l'interface est en français et utilisée
principalement sur ordinateur au bureau. La refonte conserve néanmoins des contrastes lisibles,
une navigation clavier complète et des états focus visibles.
