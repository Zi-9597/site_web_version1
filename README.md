# Site de l'Association EEA - Environnement de développement

## Statut du dépôt

Ce répertoire correspond exclusivement à la partie **développement** du site de l'Association Étudiants-Anciens EEA de l'Université de Lille. Il sert à faire évoluer le code PHP, les styles CSS, les scripts JavaScript, les contenus et la documentation avant toute intégration dans un environnement distinct.

> **Consigne importante : aucun test ne doit être exécuté dans ce répertoire.**
>
> Ne pas lancer de serveur PHP, de suite de tests, de build, de script d'envoi d'e-mail, de migration, de commande de base de données ni de vérification automatisée ici. Les essais fonctionnels et techniques doivent être réalisés dans un environnement dédié, jamais dans cette copie de développement.

## Finalité de la plateforme

Le site met en relation les étudiants, alumni et professionnels de la filière EEA. Il propose notamment :

- L'inscription, la connexion et la gestion du profil des adhérents.
- La publication et la consultation d'actualités.
- L'organisation et la participation à des événements.
- Le dépôt, la recherche et la gestion d'offres, stages, alternances et jobs étudiants.
- Les demandes d'aide étudiante et leur suivi par le bureau.
- La diffusion de goodies et de ressources associatives.
- Les pages institutionnelles, les mentions légales et le contact.

## Architecture générale

```text
.
├── commun/                 Composants PHP partagés et bibliothèque PHPMailer locale
├── public/                 Ressources front-end accessibles au navigateur
│   ├── css/                Feuilles de style
│   ├── js/                 Scripts d'interface et appels AJAX
│   └── pictures/           Logos, fonds et visuels locaux
├── templates/externe/      Pages, contrôleurs et points de terminaison PHP
│   ├── authentification/   Parcours visiteur et session
│   ├── data_base_request/  Actions serveur et réponses AJAX
│   ├── features/           Interfaces selon le rôle de l'utilisateur
│   └── mailer/             Modèles et script d'e-mail
├── index.php               Routeur frontal
├── require_db.php          Accès aux données MySQL via PDO
└── mail_class.php          Service d'envoi SMTP
```

## Fichiers à la racine

- `.env.example` : modèle des variables MySQL et SMTP. Ne contient pas de secret réel.
- `.gitignore` : règles d'exclusion Git pour `.env`, les dépendances et les journaux.
- `AUDIT_PLAN.md` : audit de sécurité/qualité et plan d'évolution.
- `IMPLEMENTATION_NOTES.md` : notes sur les correctifs de sécurité déjà appliqués.
- `README.md` : documentation de développement de ce dépôt.
- `front_end_implementation.md` : historique et règles de la refonte front-end.
- `index.php` : point d'entrée et routeur des destinations `?dest=`.
- `require_db.php` : classe PDO et méthodes d'accès aux membres, actualités, événements, offres, aides et goodies.
- `mail_class.php` : configuration et service SMTP utilisant PHPMailer.

## Dossier `commun/`

Ce dossier contient les composants inclus par plusieurs pages PHP.

- `init.php` : initialise la session, les en-têtes, le jeton CSRF, l'utilisateur connecté, l'échappement HTML et les réponses JSON.
- `acceuil_pres.php` : contenu de l'accueil, hero, statistiques et carrousel réseau/activités.
- `barre_navigation.php` : barre de navigation publique.
- `barre_navigation_conn.php` : navigation des membres du bureau connectés.
- `barre_navigation_pres.php` : navigation des présidents et web administrateurs.
- `barre_conn_etu.php` : navigation des étudiants connectés.
- `barre_conn_ancien.php` : navigation des alumni connectés.
- `footer.php` : pied de page partagé, partenaires, réseaux sociaux, liens légaux et chargement du système visuel commun.
- `propos_nous.php` : page institutionnelle de présentation de l'association.
- `contact_eea.php` : page de contact et liens vers les réseaux sociaux.
- `mention_legale.php` : mentions légales, données personnelles, cookies et hébergement.
- `uuid_v4.php` : utilitaire de génération d'identifiants UUID v7 malgré son nom historique.

### `commun/PHPMailer/src/`

Cette copie locale est une dépendance tierce utilisée par `mail_class.php`. Ne pas la modifier pour une évolution métier du site.

- `PHPMailer.php` : classe principale d'envoi des e-mails.
- `SMTP.php` : client SMTP.
- `POP3.php` : client POP3.
- `Exception.php` : exceptions propres à PHPMailer.
- `OAuth.php` : prise en charge de l'authentification OAuth.
- `OAuthTokenProvider.php` : contrat de fournisseur de jeton OAuth.
- `DSNConfigurator.php` : configuration SMTP à partir d'une DSN.

## Dossier `templates/externe/`

Il regroupe les pages PHP, les interfaces métier et les points de terminaison serveur.

### `templates/externe/authentification/`

- `inscription.php` : formulaire de création de compte et sélecteur de téléphone international.
- `connection.php` : formulaire de connexion.
- `confirmation_inscription.php` : écran de confirmation après inscription.
- `succes_token.php` : écran de validation réussie d'un jeton.
- `success.php` : écran générique de réussite.
- `echec_inscription.php` : écran d'échec d'inscription.
- `logout.php` : fermeture de session et déconnexion.

### `templates/externe/features/commun/`

- `accueil_interface.php` : page d'accueil et choix de la navigation selon le rôle.
- `actualite_interface.php` : consultation des actualités et modal de lecture.
- `evenements_interface.php` : recherche et inscription aux événements.
- `goodies_interface.php` : recherche et consultation des goodies.
- `recherche_job.php` : recherche des offres et opportunités professionnelles.
- `gestion_offres.php` : gestion des offres publiées par l'utilisateur.
- `parametres.php` : consultation et modification du profil.

### `templates/externe/features/etudiant/`

- `aides_interface.php` : demande d'aide réservée aux étudiants.
- `depot_job_etudiant.php` : dépôt d'un job étudiant.

### `templates/externe/features/ancien/`

- `depot_contrat.php` : dépôt d'une offre par un alumni.

### `templates/externe/features/bureau/`

- `ajout_event.php` : création d'un événement.
- `manage_event.php` : administration des événements et des participants.
- `gestion_actualite.php` : administration des actualités.
- `gestion_goodies.php` : administration des goodies.
- `gestion_aides.php` : administration des demandes d'aide.

### `templates/externe/features/president/`

- `gestion_adherent.php` : administration des adhérents, statuts et rôles.

### `templates/externe/data_base_request/`

Ces fichiers sont des points de terminaison PHP appelés par les formulaires ou JavaScript. Ils manipulent les données et doivent conserver leurs contrôles d'autorisation et CSRF.

- `add_subscriber.php` : création d'un adhérent.
- `fetch_connexion.php` : authentification d'un utilisateur.
- `fetch_same_email.php` : vérification de l'existence d'une adresse e-mail.
- `update_user_info.php` : mise à jour des informations du profil.
- `fetch_actualites.php` : lecture des actualités.
- `fetch_goodies.php` : lecture des goodies.
- `fetch_events.php` : lecture des événements.
- `fetch_emploie.php` : recherche d'offres.
- `fetch_membre.php` : lecture des adhérents.
- `fetch_aides.php` : lecture des demandes d'aide.

#### `data_base_request/gestion_actualite/`

- `add_actualite.php` : création d'une actualité.
- `update_actualite.php` : modification d'une actualité.
- `delete_actualite.php` : suppression d'une actualité.

#### `data_base_request/gestion_aide/`

- `add_aide.php` : création d'une demande d'aide.
- `suppress_aides.php` : suppression d'une demande d'aide.

#### `data_base_request/gestion_etudiant/`

- `update_adherent.php` : mise à jour d'un adhérent par un administrateur.

#### `data_base_request/gestion_event/`

- `add_event.php` : création d'un événement.
- `update_event.php` : modification d'un événement.
- `suppress_event.php` : suppression d'un événement.
- `add_inscris.php` : inscription d'un participant à un événement.
- `fetch_inscris.php` : lecture des participants d'un événement.

#### `data_base_request/gestion_goodies/`

- `add_goodies.php` : création d'un goodies.
- `update_goodies.php` : modification d'un goodies.
- `suppress_goodies.php` : suppression d'un goodies.

#### `data_base_request/gestion_offres/`

- `ajout_contrat.php` : création d'une offre ou d'un contrat.
- `update_offre.php` : modification d'une offre.
- `suppress_offre.php` : suppression d'une offre.

### `templates/externe/mailer/`

- `mail_welcome.php` : modèle HTML de l'e-mail de bienvenue.
- `mailer_test.php` : script historique de test SMTP. Il ne doit pas être exécuté dans ce répertoire.

### Autre fichier de `templates/externe/`

- `error.php` : page d'erreur générique.

## Dossier `public/css/`

- `index.css` : styles globaux historiques, composants communs et formulaires.
- `eea_design_system.css` : système visuel partagé de la refonte, chargé en dernier via le pied de page.
- `home_network.css` : styles de l'accueil orienté réseau professionnel.
- `barre_navigation_1.css` : ancienne feuille de style de navigation.
- `barre_navigation_v2.css` : navigation actuelle, menu mobile et panneau latéral.
- `logo_gestion.css` : disposition des logos institutionnels.
- `footer.css` : styles du pied de page.
- `presentation_acceuil.css` : styles du carrousel et des blocs de présentation de l'accueil.
- `propos_nous.css` : styles des pages institutionnelles.
- `connection_page.css` : styles historiques de connexion.
- `connection_page_v1.css` : variante active des styles de connexion.
- `inscription_st_v1.css` : styles historiques d'inscription.
- `inscription_st_v2.css` : variante active des styles d'inscription.
- `success.css` : styles des écrans de confirmation et réussite.
- `parameter_user.css` : styles des paramètres utilisateur.
- `switch_inp.css` : styles des contrôles d'édition et de bascule.
- `change_statut.css` : styles des tableaux et actions de gestion des statuts.
- `modal.css` : styles des fenêtres modales.
- `style_carte.css` : styles des cartes de contenu dynamiques.
- `actualite_style.css` : styles du mur d'actualités.
- `evenement_add.css` : styles du formulaire de création d'événement.
- `fetch_event.css` : feuille réservée à la consultation des événements.
- `aide_style.css` : styles des demandes d'aide.
- `depot_offre.css` : styles des dépôts d'offres.
- `recherche_job.css` : styles de recherche d'offres.
- `cross_change.css` : styles des actions de modification et suppression.

## Dossier `public/js/`

- `acceuil_page.js` : interactions de l'accueil.
- `connection_v1.js` : validation client de la connexion.
- `connect_interface.js` : interactions de l'interface de connexion.
- `inscription_v2.js` : validation de l'inscription, téléphone international et drapeau de secours.
- `changement_information.js` : validation historique de modification du profil.
- `switch_control.js` : édition AJAX des informations de profil.
- `gestion_slidebar_1.js` : comportement d'une ancienne version de menu latéral.
- `gestion_slide_bar_4.js` : comportement de la navigation latérale actuelle.
- `recherche_offre.js` : affichage et recherche sécurisée d'offres.
- `recherche_job_etudiant.js` : recherche de jobs étudiants.
- `fetch_offres_eea.js` : récupération des offres de l'utilisateur.
- `gestion_offres_eea_v1.js` : modification et suppression d'offres.
- `depot_offre.js` : soumission d'un dépôt d'offre.
- `recherche_evenement.js` : affichage et recherche d'événements.
- `new_event_fetch.js` : récupération et inscription aux événements.
- `update_add_event.js` : création et modification d'événements.
- `gestion_event_eea.js` : actions d'administration des événements.
- `display_actualitev7.js` : affichage détaillé des actualités.
- `gestion_actualitesv1.js` : administration des actualités.
- `affichage_goodies.js` : affichage des goodies.
- `gestions_goodies.js` : administration des goodies.
- `aide_demande.js` : validation historique des demandes d'aide.
- `aide_demande_v2.js` : variante de gestion des demandes d'aide.
- `gestion_aide_js.js` : administration des aides.
- `membres.js` : administration des adhérents.

## Dossier `public/pictures/`

Ce dossier contient uniquement des ressources graphiques locales. Les noms ci-dessous permettent d'identifier chaque fichier sans le déplacer ni le renommer.

- `logo_v7.jpeg`, `logo_v6.jpeg`, `logo_v8.jpeg` : variantes de logos, dont la dernière est aussi utilisée comme favicon.
- `logo_5.png` : logo principal de l'association.
- `Logo3_no.png`, `Logo3.jpg`, `logo_4.png` : variantes de logo de l'association.
- `univ_lille.png`, `logo_univ_5.png`, `u_lille.png` : variantes du logo de l'Université de Lille.
- `logo_asso.jpeg`, `logo_asso_no.png` : logos alternatifs de l'association.
- `bg_eea.png`, `bg_eea_2.png`, `bg_eea_3.png`, `fond.jpg` : fonds graphiques.
- `user-3295.png` : icône utilisateur.

### `public/pictures/img_activites/`

- `img_bowling.jpeg`, `img_bowling_2.jpeg`, `img_bowling_2.png` : visuels de bowling, dont la version PNG est utilisée par l'accueil.
- `img_musee.jpg` : visuel d'activité culturelle.
- `img_concert.jpg` : visuel de concert.
- `cinema_activite.jpeg` : visuel de cinéma.
- `img_foot.jpg` : visuel de football.
- `remise_livre_or.jpg` : visuel de remise ou livre d'or.

### `public/pictures/img_evenements/`

- `img_pro_1.jpg`, `img_pro_2.jpg`, `img_pro_3.jpg` : visuels de rencontres et visites professionnelles.
- `pres_1.jpg` : visuel de présentation.
- `img_reu_anc_etu.jpg` : visuel de rencontre alumni-étudiants.
- `img_master_1.jpg` : visuel de forum Master.
- `img_1.jpg`, `img_2.jpg` : visuels d'événements génériques.
- `img_anc_mast.jpg` : visuel alumni/Master.

## Technologies et règles de maintenance

- Front-end : HTML, CSS et JavaScript natif, avec des bibliothèques chargées par CDN selon les pages.
- Back-end : PHP et PDO.
- Données : MySQL, configuré par variables d'environnement.
- E-mails : PHPMailer avec SMTP.
- Style : la feuille `public/css/eea_design_system.css` complète les styles historiques sans renommer les classes, identifiants ou routes existants.
- Sécurité : conserver les contrôles de session, d'autorisation, d'échappement HTML et de jeton CSRF lors de chaque évolution.
- Tests : interdits dans ce répertoire de développement. Aucune commande d'exécution ne doit être lancée ici.

## Éléments absents du dépôt

Le dépôt ne contient pas de manifeste Composer ou npm, de schéma ou migration SQL, de dossier de tests ni de configuration de déploiement suivie. Les captures d'écran locales non suivies par Git ne font pas partie du code source ni de cette documentation.
