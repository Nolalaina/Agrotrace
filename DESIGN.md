---
name: AgroTrace — Design System
description: L'Almanach des vergers — interface d'exploitation fruitière, du pilotage à la traçabilité des lots
colors:
  primary: "#20352a"
  primary-deep: "#16241c"
  primary-soft: "#e4e0cf"
  ink: "#221f1c"
  heading-green: "#1e2b24"
  paper: "#efe9d8"
  paper-raised: "#f7f5ec"
  paper-soft: "#ece7d8"
  line: "#d8cfba"
  line-strong: "#c3bba4"
  muted: "#57544a"
  oxblood: "#7c2222"
  laiton: "#b08d57"
  laiton-text: "#8a6a3a"
  success: "#2f5d3f"
  warning: "#a05a12"
  info: "#2f5468"
typography:
  display:
    fontFamily: "Alegreya, Georgia, serif"
    fontSize: "2rem"
    fontWeight: 800
    lineHeight: 1.18
    letterSpacing: "-0.015em"
  headline:
    fontFamily: "Alegreya, Georgia, serif"
    fontSize: "1.35rem"
    fontWeight: 700
    lineHeight: 1.18
    letterSpacing: "-0.015em"
  title:
    fontFamily: "Alegreya, Georgia, serif"
    fontSize: "1.1rem"
    fontWeight: 700
    lineHeight: 1.2
  body:
    fontFamily: "Alegreya Sans, system-ui, sans-serif"
    fontSize: "0.9375rem"
    fontWeight: 400
    lineHeight: 1.55
  label:
    fontFamily: "Alegreya Sans SC, Alegreya Sans, system-ui, sans-serif"
    fontSize: "0.68rem"
    fontWeight: 700
    letterSpacing: "0.14em"
rounded:
  sm: "2px"
  md: "2px"
spacing:
  xs: "6px"
  sm: "12px"
  md: "16px"
  lg: "22px"
  xl: "28px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#f7f5ec"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "40px"
  button-primary-hover:
    backgroundColor: "{colors.primary-deep}"
    textColor: "#f7f5ec"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "40px"
  button-secondary:
    backgroundColor: "transparent"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "0 16px"
    height: "40px"
  input:
    backgroundColor: "{colors.paper-raised}"
    textColor: "{colors.ink}"
    rounded: "{rounded.sm}"
    padding: "9px 12px"
    height: "42px"
  nav-link-active:
    backgroundColor: "rgba(239, 233, 216, 0.13)"
    textColor: "#f3efe0"
    rounded: "{rounded.sm}"
    padding: "8px 11px"
  badge-danger:
    backgroundColor: "{colors.oxblood}"
    textColor: "#fff"
    rounded: "{rounded.sm}"
    padding: "3px 10px"
---

# Design System: AgroTrace — L'Almanach des vergers

## Overview

**Creative North Star: "L'Almanach des vergers"**

AgroTrace se lit comme l'almanach d'un verger : un calendrier imprimé, tenu pour le travail, feuilleté chaque matin par l'exploitant à son bureau. Chaque écran est une page de campagne — la date, la phase lunaire, le jour du calendrier, la fin de campagne — où le métier parle avant l'ornement. La matière est le papier vergé (lignes couchées presque imperceptibles), l'encre, le rail vert impérial et les filets de laiton ; la voix est Alegreya (chiffres et titres) et Alegreya Sans (interface), avec des petites capitales espacées pour les étiquettes.

Le système refuse le tableau de bord SaaS générique (fond blanc, accent bleu, cartes interchangeables) et le cream-serif-terracotta par défaut des interfaces générées. Ici, la profondeur n'est pas une ombre mais un empilement de surfaces et de filets : cartes à double filet, règles pointillées des registres, un seul oxblood réservé à l'alerte.

**Key Characteristics:**
- Papier vergé à lignes couchées (3,5 % / 2,2 % selon le thème), jamais de grain ni de bruit.
- Rail vert impérial (#20352a) avec filet de laiton d'un pixel et folios de marge (01–09).
- Deux thèmes, un seul monde : clair = vergé/encre ; sombre = même monde inversé, jamais un autre style.
- Chiffres tabulaires partout où une quantité apparaît ; petites capitales pour les étiquettes.
- Oxblood (#7c2222) réservé aux alertes critiques ; laiton (#b08d57) = filets et marque, jamais du texte sur vert sans atteindre 4.5:1 (texte laiton = #d8c49a).
- L'« éphéméride » affiche uniquement des faits calculés (date, jour de l'année, phase lunaire astronomique, dates issues de la BDD) — jamais de conseil ni de date inventée.

## Colors

Palette de papier, d'encre et de caractères d'imprimerie.

### Primary
- **Vert impérial** (#20352a): rail de navigation, bouton primaire, bande éphéméride. La couleur de structure — jamais un fond de contenu.
- **Encre** (#221f1c): texte principal et filets doubles ; au thème sombre devient le fond de page inversé.
- **Oxblood** (#7c2222): alertes critiques, tampons, rubriques. Sa rareté est le point.
- **Laiton** (#b08d57): filets, marque, icônes sur fond vert ; **Laiton texte** (#8a6a3a clair / #d8c49a sombre) dès qu'il porte des mots.

### Neutral
- **Vergé** (#efe9d8, fond clair): champ de la page ; **Papier soulevé** (#f7f5ec, surfaces) ; **Vergé doux** (#ece7d8, en-têtes de tableau/fond secondaire) ; **Papier d'élevé** (#f3efe0, barre supérieure).
- **Filet** (#d8cfba) et **Filet fort** (#c3bba4): pointillés des registres, bordures.
- **Texte sourd** (#57544a): métadonnées, toujours ≥ 4.5:1 sur vergé.
- Thème sombre: **Encre verte** (#121a15 fond), **Panneau** (#18221b), **Panneau doux** (#1d2820), **Texte vergé** (#e8e4d2), **Sourdine** (#a9ae9d), rail **#0d1511** — mêmes rôles, mêmes rapports.

**The Reserve Rule.** Oxblood apparaît uniquement sur ce qui exige un geste ou signale un manque : alerte critique, tampon, rubrique. Il n'est jamais décoratif.
**The Inversion Rule.** Le thème sombre est l'almanach imprimé sur encre noire : mêmes proportions, mêmes contrastes inversés — jamais une palette différente.

## Typography

**Display Font:** Alegreya (fallback Georgia) · **Body Font:** Alegreya Sans (fallback system-ui) · **Label Font:** Alegreya Sans SC

**Character:** un vieux style taillé pour le livre, porté par deux graisses et des chiffres tabulaires ; l'interface reste franche, jamais calligraphique.

### Hierarchy
- **Display** (800, 2rem, lh 1.18): titre de page uniquement (« Tableau de bord », nom du lot).
- **Headline** (700, 1.35rem): titres de panneaux.
- **Title** (700, 1.1rem): sous-titres de section, en-têtes de liste.
- **Body** (400–600, 0.9375rem): interface courante ; mesure ≤ 75ch.
- **Label** (SC 700, 0.66–0.68rem, tracking +0.14 à 0.18em, capitales): étiquettes, rubriques, badges.
- **Chiffres:** toujours en `font-variant-numeric: tabular-nums`.

**The Folios Rule.** Les numéros de marge (01–09 rail, Rubrique I–IV) hiérarchisent la lecture sans jamais précéder d'un surtitre décoratif ; un titre ne porte pas de kicker.

## Layout

Grille app : rail 264px sticky + colonne fluide ; contenu centré `min(1240px, 100%)`, gouttières 18–28px. Rythme vertical : 12/16/18/22/26/60px ; plus d'espace au-dessus qu'en dessous d'un titre. Barre supérieure sticky (titre + date + badge campagne) ; bande éphéméride pleine largeur sous l'en-tête de page. Cartes en grilles 4/3/2 colonnes ; tables avec filets pointillés ; formulaires en grille 12 colonnes (`span-*`).

Responsive : ≤1100px (grilles 4/3 → 2), ≤980px (rail en drawer + scrim ; contenu 20px), ≤760px (grilles → 1 colonne ; actions pleine largeur), ≤560px (bande éphéméride resserrée, cap masqué, libellés 0.78rem). Le rail garde l'ordre, les folios et le focus clavier à tous les paliers.

## Elevation & Depth

**Flat par défaut — « plat, filets et papier ».** Aucune ombre portée n'exprime une hiérarchie ; la profondeur vient de l'empilement tonal (vergé → papier soulevé → encre) et des filets (1px encre/laiton/filet-fort, doubles règles 3px sous les en-têtes, règle encre 2px en tête de carte). Une seule ombre ambiante existe (`--shadow-sm`, 0 1px 0 encre 5 % + 0 10px 24px −16 encre 35 %) et un `--shadow-md` pour les dialogues. Les états de survol changent le fond, jamais la hauteur.

**The Fillet Rule.** Un filet simple ou double fait plus qu'une ombre : laiton (accent), encre (structure), pointillé (registre). Aucune carte fantôme « 1px + grosse ombre ».

## Shapes

Rayon unique **2px** partout (boutons, champs, cartes, badges, tuiles) : le tranchant de la feuille. Aucun arrondi de 10–16px, aucune pilule (les badges sont des étiquettes carrées). Marque et avatars restent carrés (38px / 34px). En-têtes de page soulignés d'une règle double (3px double filet-fort) ; tables en pointillés ; boutons primaires plats, sans dégradé ni biseau.

## Components

### Buttons
- **Shape:** 2px ; hauteur 40px ; libellé 600/0.88rem.
- **Primary:** fond vert impérial, texte papier soulevé ; hover fond encre (#16241c). Thème sombre : fond laiton #d8c49a, texte encre.
- **Secondary:** transparent, filet fort ; hover → teinte primaire douce + filet vert.
- **Danger/Warning:** oxblood plein ; **Outline-danger** transparent.
- **Focus:** anneau laiton 4px @ 45 % (`--focus`), jamais supprimé.

### Cards (statistiques)
- **Shape:** 2px ; bordure filet-fort ; **filet haut encre 2px** (laiton au sombre) ; padding 14–18px.
- **Label:** SC 700 capitales ; valeur Alegreya 800 tabulaire ; sous-texte sourd.
- **Contenu:** une seule idée + son folio (« Rubrique I · Campagnes »).

### Inputs / Fields
- **Style:** fond papier d'élevé, filet fort, 42px ; **Focus:** bordure laiton + anneau ; placeholder en italique sourd ; select à chevron SVG.
- **Error / validation:** rouge oxblood (règles métier côté serveur inchangées).

### Navigation (rail)
- **Style:** vert impérial ; sections SC capitales ; liens 500 ; **actif** = fond 13 % + filet laiton 1px + folio laiton ; Folio 01–09 à droite.
- **Mobile:** drawer + scrim ; bouton hamburger 40×40.

### Tables
Filets pointillés ; en-têtes SC capitales sur fond vergé doux ; hover = fond doux ; chiffres tabulaires ; ligne critique `is-danger`.

### Éphéméride (signature)
Bande pleine largeur, fond vert impérial, filet laiton, lune + date + jour de l'année + phase lunaire calculée + campagne (libellé réel, fin de campagne depuis la BDD). Version compacte sur la fiche lot. **Faits calculés uniquement — aucun conseil lunaire, aucune date inventée.**

### Leaders (listes à points de conduite)
Libellé — points pointillés — valeur Alegreya tabulaire ; utilisé pour « Indicateurs calculés ».

## Do's and Don'ts

### Do:
- **Do** garder deux thèmes en un seul monde : même grille, même hiérarchie, valeurs inversées.
- **Do** réserver l'oxblood aux alertes critiques et tampons.
- **Do** des chiffres tabulaires et des libellés en petites capitales pour toute donnée dense.
- **Do** des filets simples/doubles, jamais une ombre pour créer la hiérarchie.
- **Do** des éphémérides fondées sur des données réellement calculées ou stockées.
- **Do** le clavier d'abord : focus laiton visible partout, drawer mobile fermable, cibles ≥ 34px.

### Don't:
- **Don't** ajouter de surtitre décoratif (kicker) au-dessus d'un titre — le titre porte seul.
- **Don't** utiliser de bordure latérale colorée > 1px sur cartes, listes, alertes (garde la baseline 1px ou rien).
- **Don't** utiliser de dégradés de texte, d'ombres dures sans décalage, ni de pilules.
- **Don't** glisser vers le cream-sérif-terracotta ou le bleu SaaS par défaut — l'écart chromatique est le sujet.
- **Don't** inventer de dates, fenêtres de récolte ou conseils lunaires : seules les dates de la BDD et l'astronomie calculée sont affichables.
- **Don't** contredire le monde sombre (pas de second style sombre, pas de néon, pas de glow).
