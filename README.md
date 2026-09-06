# Association EEA - environnement local `web_dev` 🧪

## 🎯 Rôle de cette branche

La branche `web_dev` est destinée exclusivement au lancement local et aux tests du site. Elle permet de vérifier les pages PHP, les formulaires, les requêtes AJAX, les styles et la base MariaDB avant une publication sur une branche de livraison.

Elle n'est pas un environnement de production. Les modifications de développement sont réalisées sur `dev_branch`, puis intégrées selon le flux Git retenu par l'équipe.

## 🚀 Lancement local

Prérequis : Docker et Docker Compose doivent être installés. Les paramètres locaux sensibles doivent être placés dans `.env`; ce fichier ne doit jamais être versionné.

```bash
docker compose up --build
```

Services disponibles après le démarrage :

- Site local : `http://127.0.0.1:8080`
- phpMyAdmin local : `http://127.0.0.1:8001`
- Nginx expose le site PHP via le port local `8080`.
- MariaDB conserve ses données dans le volume Docker `mysql_data`.

Le fichier local `docker/sql/association-eea.sql` est monté pour initialiser MariaDB. Il est volontairement ignoré par Git : il doit rester une ressource locale et ne doit pas être ajouté, commité ou poussé.

Pour arrêter les services :

```bash
docker compose down
```

## ✨ Fonctionnalités

- Inscription, confirmation par e-mail, connexion et déconnexion.
- Gestion du profil et des informations personnelles.
- Consultation et gestion des actualités.
- Consultation, création, modification, suppression et inscription aux événements.
- Publication, recherche et gestion d'offres d'emploi, de stages, d'alternances et de jobs étudiants.
- Demandes d'aide étudiante et suivi par le bureau.
- Gestion des goodies, des membres et des rôles associatifs.

## 📁 Fichiers racine

| Fichier ou dossier | Rôle |
| --- | --- |
| `index.php` | Point d'entrée unique et table de routage `?dest=...`. |
| `require_db.php` | Connexion PDO et méthodes d'accès aux données MariaDB. |
| `mail_class.php` | Configuration et envoi des e-mails de l'application. |
| `.env` | Configuration locale sensible : base de données et SMTP. Ignoré par Git. |
| `.env.example` | Exemple de configuration locale sans secret. |
| `.gitignore` | Liste des fichiers locaux qui ne doivent pas être suivis par Git. |
| `docker-compose.yaml` | Définition des services Nginx, PHP, MariaDB et phpMyAdmin. |
| `README.md` | Documentation de cette branche locale de test. |
| `front_end_implementation.md` | Détail en français de la refonte graphique. |
| `AUDIT_PLAN.md` | Notes de planification et d'audit du projet. |
| `IMPLEMENTATION_NOTES.md` | Notes techniques d'implémentation. |
| `commun/` | Composants PHP communs, initialisation et bibliothèque e-mail. |
| `public/` | Ressources statiques chargées par le navigateur. |
| `templates/` | Pages PHP, écrans métier et points de traitement des requêtes. |
| `docker/` | Configuration locale des conteneurs. |

## 🐳 Dossier `docker/`

| Chemin | Rôle |
| --- | --- |
| `docker/nginx/` | Configuration Nginx utilisée par le conteneur `nginx-web`. |
| `docker/nginx/nginx.conf` | Virtual host Nginx qui transmet les requêtes PHP au conteneur PHP. |
| `docker/php/` | Image PHP locale utilisée par le service `php`. |
| `docker/php/Dockerfile` | Instructions de construction de l'image PHP. |
| `docker/sql/` | Emplacement local des scripts ou exports SQL. |
| `docker/sql/association-eea.sql` | Export SQL local attendu par Docker pour initialiser la base. Fichier ignoré par Git. |

## 🧩 Dossier `commun/`

| Fichier ou dossier | Rôle |
| --- | --- |
| `init.php` | Initialisation commune : session, CSRF, réponses JSON et utilisateur connecté. |
| `uuid_v4.php` | Génération des identifiants UUID utilisés par les membres. |
| `acceuil_pres.php` | Contenu éditorial de la page d'accueil. |
| `barre_navigation.php` | Navigation d'un visiteur non connecté. |
| `barre_navigation_conn.php` | Navigation d'un membre du bureau. |
| `barre_navigation_pres.php` | Navigation du président ou de l'administrateur web. |
| `barre_conn_etu.php` | Navigation d'un étudiant connecté. |
| `barre_conn_ancien.php` | Navigation d'un alumni connecté. |
| `footer.php` | Pied de page partagé. |
| `propos_nous.php` | Page de présentation de l'association. |
| `contact_eea.php` | Page de contact. |
| `mention_legale.php` | Mentions légales. |
| `PHPMailer/` | Bibliothèque locale d'envoi d'e-mails. |
| `PHPMailer/src/PHPMailer.php` | Classe principale d'envoi des messages. |
| `PHPMailer/src/SMTP.php` | Transport SMTP. |
| `PHPMailer/src/Exception.php` | Exceptions de la bibliothèque. |
| `PHPMailer/src/OAuth.php`, `OAuthTokenProvider.php`, `DSNConfigurator.php`, `POP3.php` | Composants complémentaires de la bibliothèque e-mail. |

## 🎨 Dossier `public/`

### 🎨 `public/css/`

| Fichier | Rôle |
| --- | --- |
| `index.css` | Styles globaux, arrière-plan et composants de formulaires partagés. |
| `eea_design_system.css` | Variables et composants du système visuel EEA. |
| `home_network.css`, `presentation_acceuil.css` | Mise en page de l'accueil et du réseau alumni. |
| `barre_navigation_v2.css`, `barre_navigation_1.css` | Styles des navigations desktop et mobile. |
| `footer.css`, `logo_gestion.css` | Pied de page et présentation des logos. |
| `connection_page.css`, `connection_page_v1.css` | Écrans de connexion. |
| `inscription_st_v1.css`, `inscription_st_v2.css` | Écrans d'inscription. |
| `parameter_user.css`, `switch_inp.css` | Paramètres utilisateur et interrupteurs. |
| `depot_offre.css`, `recherche_job.css` | Dépôt et recherche d'offres. |
| `evenement_add.css`, `fetch_event.css` | Création et consultation d'événements. |
| `actualite_style.css` | Affichage des actualités. |
| `aide_style.css`, `style_carte.css` | Demandes d'aide et cartes de contenu. |
| `change_statut.css`, `cross_change.css`, `modal.css` | Tableaux de gestion, actions et fenêtres modales. |
| `propos_nous.css`, `success.css` | Pages institutionnelles et écrans de confirmation. |

### ⚡ `public/js/`

| Fichier | Rôle |
| --- | --- |
| `gestion_slide_bar_4.js`, `gestion_slidebar_1.js` | Ouverture et fermeture du menu latéral. |
| `inscription_v2.js` | Validation et envoi du formulaire d'inscription. |
| `connection_v1.js`, `connect_interface.js` | Connexion et interactions associées. |
| `switch_control.js`, `changement_information.js` | Modification des informations du profil. |
| `depot_offre.js` | Validation et envoi des offres. |
| `fetch_offres_eea.js`, `recherche_offre.js`, `recherche_job_etudiant.js` | Recherche et filtrage des offres. |
| `update_add_event.js` | Ajout d'un événement. |
| `new_event_fetch.js`, `recherche_evenement.js` | Consultation, filtre et inscription à un événement. |
| `gestion_event_eea.js` | Gestion des événements par le bureau. |
| `display_actualitev7.js`, `gestion_actualitesv1.js` | Consultation et gestion des actualités. |
| `affichage_goodies.js`, `gestions_goodies.js` | Consultation et gestion des goodies. |
| `aide_demande.js`, `aide_demande_v2.js`, `gestion_aide_js.js` | Demandes d'aide et gestion par le bureau. |
| `membres.js` | Gestion des membres et de leurs statuts. |
| `gestion_offres_eea_v1.js` | Gestion des offres du membre connecté. |
| `acceuil_page.js` | Interactions de la page d'accueil. |

### 🖼️ `public/pictures/`

Ce dossier contient les logos de l'association et de l'Université de Lille, les fonds graphiques et les photographies utilisées par les écrans.

- Logos et fonds : `logo_*.png`, `logo_*.jpeg`, `logo_asso*`, `logo_univ_5.png`, `u_lille.png`, `univ_lille.png`, `Logo3*`, `bg_eea*`, `fond.jpg`, `user-3295.png`.
- Activités : `img_activites/` contient les images de bowling, musée, concert, cinéma, football et remise de livre d'or.
- Événements : `img_evenements/` contient les illustrations de forums, rencontres alumni, présentations et événements professionnels.

## 🧱 Dossier `templates/`

Toutes les pages sont chargées depuis les routes déclarées dans `index.php`.

### 🔐 `templates/externe/authentification/`

| Fichier | Rôle |
| --- | --- |
| `inscription.php` | Formulaire de création de compte. |
| `connection.php` | Formulaire de connexion. |
| `logout.php` | Fermeture de session. |
| `success.php`, `echec_inscription.php` | Écrans de résultat de l'inscription. |
| `confirmation_inscription.php`, `succes_token.php` | Confirmation du compte par jeton. |

### 📄 `templates/externe/features/`

| Dossier | Pages |
| --- | --- |
| `commun/` | `accueil_interface.php`, `actualite_interface.php`, `evenements_interface.php`, `goodies_interface.php`, `recherche_job.php`, `gestion_offres.php`, `parametres.php`. |
| `etudiant/` | `aides_interface.php` et `depot_job_etudiant.php`. |
| `ancien/` | `depot_contrat.php`. |
| `bureau/` | `ajout_event.php`, `manage_event.php`, `gestion_actualite.php`, `gestion_goodies.php`, `gestion_aides.php`. |
| `president/` | `gestion_adherent.php`. |

### 🗄️ `templates/externe/data_base_request/`

Ces fichiers sont les points de traitement PHP appelés par les formulaires et scripts JavaScript. Ils contrôlent la session et le CSRF avant d'appeler `EEA_Database`.

| Dossier ou fichier | Rôle |
| --- | --- |
| `add_subscriber.php`, `fetch_connexion.php`, `fetch_same_email.php` | Inscription, connexion et vérification de l'e-mail. |
| `fetch_actualites.php`, `fetch_events.php`, `fetch_goodies.php`, `fetch_emploie.php`, `fetch_membre.php`, `fetch_aides.php` | Lecture de données pour les écrans et les filtres. |
| `update_user_info.php` | Mise à jour des informations du profil. |
| `gestion_actualite/` | Ajout, modification et suppression d'actualités. |
| `gestion_event/` | Ajout, modification, suppression, inscription et liste des participants aux événements. |
| `gestion_offres/` | Ajout, modification et suppression des offres. |
| `gestion_goodies/` | Ajout, modification et suppression des goodies. |
| `gestion_aide/` | Ajout et suppression des demandes d'aide. |
| `gestion_etudiant/update_adherent.php` | Mise à jour d'un adhérent par le président. |

### 📬 Autres fichiers de `templates/externe/`

| Fichier ou dossier | Rôle |
| --- | --- |
| `error.php` | Écran d'erreur générique. |
| `mailer/mail_welcome.php` | Contenu de l'e-mail de bienvenue. |
| `mailer/mailer_test.php` | Script de test de l'envoi d'e-mails. |

## 🧭 Routes principales

| Route | Page ou action |
| --- | --- |
| `/?dest=acceuil` | Accueil. |
| `/?dest=inscription` | Inscription. |
| `/?dest=connection` | Connexion. |
| `/?dest=actualite` | Actualités. |
| `/?dest=rech_event` | Événements. |
| `/?dest=offre_emploie` | Recherche d'offres. |
| `/?dest=goodies` | Goodies. |
| `/?dest=parametres` | Paramètres du profil. |
| `/?dest=aides_etud` | Demande d'aide étudiante. |
| `/?dest=apropos` | À propos de l'association. |
| `/?dest=contact_assoc` | Contact. |
| `/?dest=mention_legale` | Mentions légales. |

Les routes d'administration et de traitement sont également déclarées dans `index.php`. Ne pas ajouter de fichier PHP directement à partir d'une valeur d'URL : le routeur utilise une liste blanche volontairement explicite.

## 🌿 Git et tests locaux

Sur cette branche, vérifier l'état avant tout test ou commit :

```bash
git status
```

Pour récupérer les changements distants de `web_dev` :

```bash
git pull origin web_dev
```

Pour lancer les tests manuels : démarrer Docker, ouvrir le site local, vérifier les pages principales, les formulaires, la connexion et les écrans d'administration adaptés au rôle connecté. Les données de test restent locales dans MariaDB.
