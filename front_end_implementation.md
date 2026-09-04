# Refonte Front-End

## Objectif

La page d'accueil a été modernisée pour présenter l'association EEA comme un réseau professionnel entre étudiants, alumni et partenaires. La refonte conserve les routes PHP, les classes HTML existantes, les identifiants JavaScript, les images déjà présentes et le fonctionnement du slider.

## Palette graphique

- Bleu nuit `#0D2146` : identité institutionnelle et éléments de structure.
- Bleu `#1D5FD1` : actions principales et liens interactifs.
- Ambre `#F3A32B` : accent visuel chaleureux.
- Dégradés bleu clair, blanc et ambre : arrière-plan doux et moderne.

## Fichiers modifiés

### `templates/externe/features/commun/accueil_interface.php`

- Correction du titre de page en `Accueil - Association EEA`.
- Chargement de la nouvelle feuille de style `home_network.css`.
- Conservation de tous les chargements CSS, scripts et inclusions PHP existants.

### `commun/acceuil_pres.php`

- Refonte éditoriale du bloc d'accueil avec un positionnement réseau professionnel.
- Ajout de deux appels à l'action utilisant les routes existantes : inscription et événements.
- Ajout de repères de confiance pour valoriser l'ouverture du réseau.
- Modernisation des textes, statistiques et titres des deux volets du slider.
- Conservation des classes existantes (`accueil_presentation`, `presentation_intro`, `stats-bar`, `slider-track`, etc.) et des identifiants JavaScript (`sliderTrack`, `prevSlide`, `nextSlide`, `sliderDots`).
- Conservation de toutes les images locales utilisées sur la page.

### `public/css/home_network.css`

- Nouvelle feuille de style dédiée à la page d'accueil.
- Création de variables de couleur afin d'assurer une identité visuelle cohérente.
- Mise en page responsive du hero, des statistiques, des cartes et du slider.
- Amélioration des contrastes, espacements, états de survol et boutons.
- Aucun sélecteur JavaScript, nom de classe existant ou comportement applicatif n'est supprimé.

### `public/css/barre_navigation_v2.css`

- Ajout, en fin de fichier, d'une surcharge de style pour une navigation plus compacte et professionnelle.
- Conservation de la structure HTML des différentes barres de navigation et du menu mobile.
- Harmonisation des états de survol, des boutons connexion/inscription et du panneau latéral mobile.

### `public/css/footer.css`

- Ajout, en fin de fichier, d'une surcharge de style pour un pied de page institutionnel bleu nuit.
- Amélioration de la lisibilité des logos, liens et réseaux sociaux.
- Adaptation du pied de page aux écrans mobiles.

## Compatibilité

- Les routes PHP existantes ne sont pas modifiées.
- Les variables PHP et JavaScript existantes ne sont pas renommées.
- Les classes et identifiants déjà utilisés sont conservés.
- Les médias proviennent uniquement du répertoire `public/pictures` déjà présent dans le projet.

## Refonte complète des formulaires

### `public/css/index.css`

- Ajout d'un bloc commenté `CHANGE` qui définit la présentation commune de tous les formulaires du site.
- Les formulaires d'inscription, connexion, paramètres, création d'événement, dépôt d'offre, demandes d'aide et formulaires de gestion utilisent maintenant une même grille visuelle : surfaces blanches, bordures bleu clair, champs lisibles et états de focus accessibles.
- Les filtres de recherche, cases de spécialités, boutons d'action, tableaux de gestion et modales utilisent la même palette bleu nuit, bleu d'action et rouge réservé aux suppressions.
- Ajout de règles responsives pour empiler les champs et filtres sur mobile.
- Les styles ciblent les classes et identifiants existants. Aucun nom de classe, `id`, attribut `name` ou sélecteur JavaScript n'a été remplacé.

### `templates/externe/authentification/inscription.php`

- Mise à jour du texte affiché sous le champ mot de passe.
- Les anciennes règles de composition ont été remplacées par des indications claires : tout caractère est accepté, aucune longueur minimale n'est imposée et le champ ne peut pas être vide.
- Les identifiants `rule_1` à `rule_4` restent présents afin de préserver les dépendances JavaScript existantes.

### `templates/externe/features/commun/parametres.php`

- Mise à jour identique des indications de mot de passe dans les paramètres utilisateur.
- Tous les éléments structurels et les boutons `data-target` utilisés pour les mises à jour AJAX sont conservés.

### `public/js/inscription_v2.js`

- La validation du mot de passe à l'inscription n'exige plus de chiffre, majuscule, minuscule, caractère spécial ou longueur minimale.
- Le bouton d'inscription est activé dès que le mot de passe est non vide et que les autres validations existantes sont satisfaites.

### `public/js/switch_control.js`

- La modification du mot de passe dans les paramètres accepte désormais toute valeur non vide.
- Les validations des autres données personnelles ne sont pas modifiées.

### `public/js/changement_information.js`

- Ancien script de validation des informations harmonisé avec la nouvelle règle : toute valeur de mot de passe non vide est acceptée.
- Les identifiants `rule_1` à `rule_4` restent utilisés pour conserver la compatibilité avec les écrans historiques.

### `public/js/connection_v1.js`

- La connexion ne bloque plus un mot de passe de moins de quatre caractères côté navigateur.
- L'adresse e-mail doit toujours avoir un format valide et le mot de passe doit être non vide.

### `templates/externe/data_base_request/add_subscriber.php`

- La validation PHP d'inscription ne demande plus huit caractères minimum.
- Le mot de passe reste obligatoire et sa taille maximale de 1 024 caractères est maintenue afin de protéger le traitement et le stockage.
- Le hachage sécurisé avec `password_hash` reste inchangé.

### `templates/externe/data_base_request/update_user_info.php`

- La validation PHP de changement de mot de passe ne demande plus huit caractères minimum.
- Le mot de passe vide reste refusé, la taille maximale est maintenue et le mot de passe continue d'être haché avant son enregistrement.

## Périmètre des interfaces harmonisées

- Inscription et connexion.
- Paramètres du profil.
- Ajout d'événements et gestion des événements.
- Dépôt, recherche et gestion des offres d'emploi, stages, alternances et jobs étudiants.
- Demandes d'aide et gestion des demandes.
- Filtres de recherche, tableaux d'administration, boutons d'action et fenêtres modales.
- Barre latérale et navigation mobile conservent leurs scripts et classes existants, avec la présentation professionnelle déjà ajoutée dans `barre_navigation_v2.css`.
