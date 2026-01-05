<?php
    /* ==========================================================
    INITIALISATION GLOBALE
    - Sécurité des cookies de session
    - Démarrage de la session
    - Chargement de la base de données
    ========================================================== */

    // Sécurité minimale des cookies de session
    // → session uniquement via cookies (pas via URL)
    // → cookie inaccessible en JavaScript
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_httponly', 1);

    // (À activer uniquement si le site est en HTTPS)
    // ini_set('session.cookie_secure', 1);

    // Empêche tout affichage avant l'envoi des headers HTTP
    //ob_start();

    // Démarrage de la session (UNE seule fois)
    session_start();

    // Connexion à la base de données
    // ⚠️ Ce fichier ne doit produire AUCUNE sortie (echo, HTML…)
    require_once "require_db.php";

    /* ==========================================================
    SESSION UTILISATEUR
    ========================================================== */

    // Utilisateur connecté ou null
    $user = $_SESSION['user'] ?? null;

    /*
    * Fallback simple :
    * si une session existe mais est invalide → logout
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
    TIMEOUT D’INACTIVITÉ
    ========================================================== */

    // Durée max d’inactivité (30 minutes)
    $SESSION_TIMEOUT = 1800;

    if ($user !== null) 
    {

        // Si last_activity n'existe pas encore (sécurité)
        if (!isset($_SESSION['last_activity'])) {
            $_SESSION['last_activity'] = time();
        }

        // Vérification du timeout
        if (time() - $_SESSION['last_activity'] > $SESSION_TIMEOUT) {
            session_unset();
            session_destroy();
            header("Location: /?dest=logout");
            exit;
        }

        // Mise à jour de l’activité
        $_SESSION['last_activity'] = time();
    }

    /* ==========================================================
    DONNÉES UTILISATEUR UTILES
    ========================================================== */

    // Nom et prénom (toujours défini, jamais null)
    $nom_prenom = '';
    if ($user && isset($user['prenom'], $user['nom'])) {
        $nom_prenom = trim($user['prenom'] . ' ' . $user['nom']);
    }
?>