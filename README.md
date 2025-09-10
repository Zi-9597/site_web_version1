# 🌐 Site Web – Association EEA

## 📝 Contexte et objectif
L’**Association Étudiants-Anciens EEA** regroupe des étudiants en cours de formation et des diplômés qui souhaitent garder un lien avec leur école et leur réseau.  
Afin de faciliter les échanges, valoriser les activités et renforcer la communauté, une plateforme web a été créée.  

Ce site a pour mission de :  
- Offrir un **point de rencontre numérique** entre les étudiants et les anciens  
- Mettre en avant les **événements et actualités de l’association**  
- Créer un **réseau d’entraide** pour les opportunités professionnelles  
- Conserver et partager les **souvenirs** liés à la vie de l’association  

---

## 📌 Description de la plateforme
Le site est pensé pour être simple, intuitif et accessible à tous les membres.  
Il permet notamment :  
- L’**inscription et la connexion** des adhérents  
- La **publication et consultation d’actualités**  
- La **gestion et participation aux événements** (séminaires, ateliers, sorties, etc.)  
- La **diffusion d’offres de stages, alternances et emplois**  
- L’accès à une **galerie souvenirs** (photos, vidéos, projets passés)  
- Un **formulaire de contact** pour échanger avec l’association  

---

## 📂 Organisation du projet

Le projet est structuré en plusieurs dossiers, chacun ayant un rôle précis :  

- **`public/`**  
  Contient tous les fichiers accessibles par les utilisateurs :  
  - **`css/`** → Feuilles de style pour la mise en page et le design  
  - **`js/`** → Scripts JavaScript pour les interactions dynamiques  
  - **`images/`** → Logos, icônes et illustrations utilisées sur le site  

- **`templates/`**  
  Regroupe les pages HTML/PHP du site, organisées selon les rôles :  
  - **`externe/`** → Pages accessibles aux visiteurs non connectés  
    - `ancien/` → Pages destinées aux anciens étudiants (avant inscription)  
    - `non_membre/` → Pages visibles par tout internaute (partie publique)  
  - **`interne/`** → Pages réservées aux membres inscrits et connectés  
  - **`commun/`** → Éléments réutilisables (barre de navigation, pied de page, en-têtes, etc.)  

- **`config/`**  
  Paramètres de configuration (ex. connexion base de données).  

- **`includes/`**  
  Scripts PHP réutilisables : fonctions, gestion de la sécurité, logique d’authentification.  

- **`sql/`**  
  Sauvegardes et scripts liés à la base de données (structure des tables, exports).  

- **`index.php`**  
  La page d’accueil principale qui sert de point d’entrée au site.  

- **`README.md`**  
  Ce document de présentation du projet.  

---

## 🚀 Fonctionnalités principales
- 🔑 Authentification (inscription, connexion, déconnexion)  
- 📰 Actualités et annonces de l’association  
- 📅 Gestion et participation aux événements  
- 💼 Diffusion d’offres professionnelles (stages, alternances, emplois)  
- 📸 Galerie multimédia (souvenirs, photos, vidéos)  
- 📩 Formulaire de contact  

---

## 🛠️ Technologies utilisées
- **Frontend** : HTML5, CSS3, JavaScript  
- **Mise en forme** : CSS personnalisé + [Bootstrap 5](https://getbootstrap.com/)  
- **Backend** : PHP 7.4+  
- **Base de données** : MySQL 5.7+  
- **Serveur** : Apache  
- **Outils de développement** : Git, GitHub, VS Code  
