---
version: 1
slug: "dashboard"
primary_target: "dashboard"
related_targets: []
---

Scope: refonte globale de l'interface AgroTrace (shell + toutes les vues Blade). Surface principale : tableau de bord. Mode : Operate — l'exploitant complète des tâches, ne contemple pas. Audience : exploitant agricole au bureau sur ordinateur, quotidien chargé (récoltes, lots, stocks) ; rôle responsable/admin. Job : voir où en est la campagne et agir en 1–2 gestes.

## Direction contract

THESIS : L'exploitation se lit comme un almanach du verger — chaque écran est une page de campagne, pas un widget de SaaS ; la référence refusée est le dashboard blanc à cartes et accent bleu de la catégorie.

OWN-WORLD : rail vert impérial #20352a sur fond vergé #efe9d8 ; encre #221f1c ; oxblood #7c2222 (alertes, accents) ; laiton #b08d57 (fins filets, marque) ; Alegreya (titres, chiffres tracés) + Alegreya Sans (UI) ; petites capitales espacées, points de conduite, folios ; léger vergé en texture ; thème sombre = même monde inversé (vert très sombre, texte vergé), jamais un autre.

STORY : le matin, l'exploitant ouvre l'outil, voit en une lecture où en est la campagne (fenêtre de récolte, lune, jour), ce qui exige un geste (stock critique NPK, 3 récoltes non affectées), agit, et repart.

FIRST VIEWPORT : rail de navigation à gauche (sections Exploitation / Pilotage, folios 01–09) ; en-tête avec fil « AgroTrace · Tableau de bord » + badge « Campagne lychee 2026–27 » ; grand titre serif « Tableau de bord » ; bande éphéméride (lune, jour julien, fenêtre 12–28 déc.) ; 4 cartes « Rubrique I–IV » ; panneau Indicateurs à points de conduite ; panneau Alertes persistant ; table des stocks. Action primaire : « Enregistrer une récolte », en haut à droite.

FORM : assigned (direction du tirage). seed:020179b52cef1a95

FINISH : unreviewed and undocumented is unfinished; this build ends with the finish review, the verdict, DESIGN.md, and every shipping raster carrying its provenance.

Contraintes : structure fonctionnelle inchangée (routes, formulaires, règles métier, isolation d'exploitation, contrôles serveur) ; thème clair/sombre conservé ; nom AgroTrace conservé ; français uniquement ; accessibilité (clavier, focus visible, contraste ≥ AA) ; responsive conservé (drawer mobile). Unresolved : logo final (feuille conservée provisoirement), densité exacte à valider à l'écran.
Signature move : la bande éphéméride de campagne sous la barre supérieure (lune, fenêtre de récolte, jour julien) — présente sur le dashboard, reprise en version compacte sur fiche lot.
