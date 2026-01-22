<?php
    /* ==========================================================
    INITIALISATION GLOBALE
    - Sécurité des cookies de session
    - Démarrage de la session
    - Headers de sécurité navigateur
    - Chargement base de données
    ========================================================== */


    /* ==========================================================
    1) SÉCURITÉ DES COOKIES DE SESSION
    ========================================================== */

    // La session utilise uniquement des cookies (pas via URL)
    ini_set('session.use_only_cookies', 1);

    // Le cookie de session n’est PAS accessible en JavaScript
    ini_set('session.cookie_httponly', 1);

    // Le cookie de session n’est envoyé QUE via HTTPS
    ini_set('session.cookie_secure', 1);

    // Protection CSRF basique
    ini_set('session.cookie_samesite', 'Strict');


    /* ==========================================================
    2) DÉMARRAGE DE LA SESSION (UNE SEULE FOIS)
    ========================================================== */

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }


    /* ==========================================================
    3) HEADERS DE SÉCURITÉ NAVIGATEUR
    ========================================================== */

    if (!headers_sent()) {

        // ==========================================================
        // Content Security Policy (CSP)
        // ==========================================================
        // Ce header indique au NAVIGATEUR :
        //  - d'où il a le droit de charger du code
        //  - ce qu'il a le droit d'exécuter
        //  - ce qui doit être bloqué automatiquement
        //
        // Principe :
        //  TOUT est interdit par défaut
        //  SAUF ce qui est explicitement autorisé ci-dessous
        // ==========================================================

        header(
            "Content-Security-Policy: " .

            // ------------------------------------------------------
            // default-src
            // ------------------------------------------------------
            // Règle par défaut : toutes les ressources doivent venir
            // du site lui-même (même domaine).
            // Si une directive spécifique n'existe pas, celle-ci s'applique.
            "default-src 'self'; " .

            // ------------------------------------------------------
            // script-src
            // ------------------------------------------------------
            // Autorise l'exécution de JavaScript :
            //  - depuis le site lui-même
            //  - depuis les CDN réellement utilisés par le site
            //  - 'unsafe-inline' autorise le JS écrit directement
            //    dans les pages HTML (choix pragmatique)
            //
            // Tout autre script externe est BLOQUÉ.
            "script-src 'self' 'unsafe-inline' " .
                "https://cdn.jsdelivr.net " .
                "https://ajax.googleapis.com " .
                "https://maxcdn.bootstrapcdn.com; " .

            // ------------------------------------------------------
            // style-src
            // ------------------------------------------------------
            // Autorise les feuilles de style CSS :
            //  - locales
            //  - inline (nécessaire pour Bootstrap)
            //  - Google Fonts
            //  - CDN utilisés pour Bootstrap / Font Awesome
            //
            // Les styles provenant d'autres domaines sont BLOQUÉS.
            "style-src 'self' 'unsafe-inline' " .
                "https://fonts.googleapis.com " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "https://maxcdn.bootstrapcdn.com; " .

            // ------------------------------------------------------
            // font-src
            // ------------------------------------------------------
            // Autorise le chargement des polices :
            //  - locales
            //  - Google Fonts
            //  - CDN nécessaires aux icônes (Bootstrap Icons, FA)
            "font-src 'self' " .
                "https://fonts.gstatic.com " .
                "https://cdn.jsdelivr.net " .
                "https://cdnjs.cloudflare.com " .
                "https://maxcdn.bootstrapcdn.com; " .

            // ------------------------------------------------------
            // img-src
            // ------------------------------------------------------
            // Autorise les images :
            //  - locales
            //  - encodées en base64 (data:)
            //
            // Toute image externe non autorisée est BLOQUÉE.
            "img-src 'self' data:; " .

            // ------------------------------------------------------
            // connect-src
            // ------------------------------------------------------
            // Autorise les connexions réseau via JavaScript :
            //  - AJAX
            //  - fetch
            //  - WebSocket
            //
            // Ici : uniquement vers le site lui-même.
            "connect-src 'self'; " .

            // ------------------------------------------------------
            // form-action
            // ------------------------------------------------------
            // Empêche les formulaires HTML d'envoyer des données
            // vers un site externe (protection contre le phishing).
            "form-action 'self'; " .

            // ------------------------------------------------------
            // upgrade-insecure-requests
            // ------------------------------------------------------
            // Force automatiquement le passage de HTTP vers HTTPS
            // pour toutes les ressources du site.
            // Évite le chargement de contenu non chiffré.
            "upgrade-insecure-requests"
        );



        // Empêche la fuite d’URL sensibles
        header("Referrer-Policy: strict-origin-when-cross-origin");

        // Désactive caméra, micro, géolocalisation
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

        // Protection XSS legacy (utile pour les audits)
        header("X-XSS-Protection: 1; mode=block");
    }


    /* ==========================================================
    4) CONNEXION À LA BASE DE DONNÉES
    ========================================================== */

    // ⚠️ Ce fichier ne doit produire AUCUNE sortie
    require_once "require_db.php";


    /* ==========================================================
    5) SESSION UTILISATEUR
    ========================================================== */

    // Utilisateur connecté ou null
    $user = $_SESSION['user'] ?? null;

    /*
    * Sécurité :
    * si la session existe mais est invalide → logout
    */
    if ($user !== null) {
        if (!is_array($user) || empty($user['id_membre'])) {
            session_unset();
            session_destroy();
            header("Location: /?dest=logout");
            exit;
        }
    }


    /* ==========================================================
    6) TIMEOUT D’INACTIVITÉ
    ========================================================== */

    // Durée max d’inactivité (30 minutes)
    $SESSION_TIMEOUT = 1800;

    if ($user !== null) {

        // Initialisation si absent
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
        }

        // Vérification timeout
        if (time() - $_SESSION['last_activity'] > $SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header("Location: /?dest=logout");
            exit;
        }

        // Mise à jour activité
        $_SESSION['last_activity'] = time();
    }


    /* ==========================================================
    7) DONNÉES UTILISATEUR UTILES
    ========================================================== */

    $nom_prenom = '';
    if ($user && isset($user['prenom'], $user['nom'])) {
        $nom_prenom = trim($user['prenom'] . ' ' . $user['nom']);
    }
?>