# Interface Front-End EEA

## Objectif de la refonte

L'interface présente désormais l'association EEA comme un réseau fiable entre étudiants, alumni et professionnels. La refonte améliore la cohérence visuelle, la lisibilité et l'utilisation sur téléphone, tablette et ordinateur, sans modifier les routes PHP, les images existantes, les identifiants JavaScript ou les traitements métier.

## Principes graphiques

- Bleu nuit `#102A43` : structure institutionnelle, titres de page et en-têtes de tableau.
- Bleu moyen `#2D6FAE` : actions principales et états interactifs.
- Bleu très clair `#EDF5FC` : surfaces actives et états de survol discrets.
- Gris ardoise `#334E68` : textes courants à contraste élevé.
- Accent or doux `#F3A32B` : utilisé avec parcimonie dans la page d'accueil.
- Blanc et gris bleuté : arrière-plans calmes, sans dégradés agressifs.

## Changements appliqués

### Accueil et navigation

- `commun/acceuil_pres.php` : texte d'accueil recentré sur le réseau, appels à l'action explicites, statistiques renommées et contrôles de slider accessibles. Les images de présentation existantes sont conservées.
- `public/css/home_network.css` : suppression des contrastes orange-violet agressifs, fond bleu très doux, cadre photo sobre, cartes homogènes et points de slider utilisables au clavier.
- `public/css/logo_gestion.css` : bandeau institutionnel plus compact avec logos correctement dimensionnés sur tous les écrans.
- `public/css/barre_navigation_v2.css` : menu horizontal plus lisible, bouton hamburger à trois traits parfaitement centré et transformation en croix régulière. Le panneau latéral est clair, défilable et adapté aux petits écrans.
- `templates/externe/features/commun/accueil_interface.php` : ajout de la balise `viewport` pour que la page d'accueil adopte les règles responsive.

### Pied de page

- `commun/footer.php` : regroupement des partenaires, réseaux sociaux et liens légaux avec une hiérarchie claire.
- `public/css/footer.css` : logos placés sur un bandeau clair pour rester lisibles, pied de page bleu nuit plus doux, liens et icônes contrastés, empilement propre sur téléphone.

### Formulaires, filtres et actions

- `public/css/index.css` : système commun pour les titres, formulaires, filtres, cases de spécialité, tableaux, modales, notifications et boutons.
- Les anciens violets et verts saturés ont été remplacés par le bleu EEA. Le rouge est réservé aux suppressions afin de garder un sens clair des actions dangereuses.
- Les tableaux restent lisibles sur mobile grâce à un défilement horizontal contrôlé dans leur conteneur, plutôt qu'à un écrasement des colonnes.
- Les champs ont une hauteur minimale de 44 pixels, un focus visible et des libellés lisibles.

### Événements et opportunités

- `evenements_interface.php` : titre, recherche et action de participation clarifiés en français.
- `recherche_job.php` : recherche d'offres renommée « Opportunités professionnelles », filtres mieux structurés et libellés plus directs.
- `gestion_offres.php` : gestion personnelle recentrée sur les offres publiées et libellés de filtre corrigés.
- `ajout_event.php` : formulaire de création simplifié avec une explication utile et professionnelle.
- `manage_event.php` : textes corrigés pour parler d'événements, de participants et de suivi plutôt que d'offres.

## Responsive

- Ordinateur : grilles sur deux colonnes, navigation horizontale et tableaux complets.
- Tablette, sous `900px` : page d'accueil en colonne, image redimensionnée, panneaux moins espacés.
- Téléphone, sous `760px` : formulaire et filtres sur une colonne, boutons de modale pleine largeur, marges réduites.
- Petit téléphone, sous `700px` : navigation principale compacte, menu latéral en largeur relative, actions d'en-tête conservées sous forme d'icônes.

## Compatibilité conservée

- Toutes les routes `?dest=` existantes restent identiques.
- Les classes, identifiants, attributs `name` et sélecteurs utilisés par JavaScript sont conservés.
- Les images de `public/pictures` utilisées par l'accueil ne sont ni remplacées ni déplacées.
- Les pages PHP actuellement concernées déclarent `lang="fr"` pour correspondre au contenu affiché.
