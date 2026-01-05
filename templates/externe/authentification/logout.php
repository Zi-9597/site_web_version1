<?php
    // 1️⃣ Démarrer la session (obligatoire pour la manipuler)
    session_start();

    // 2️⃣ Vider toutes les données de session
    $_SESSION = [];

    // 3️⃣ Supprimer le cookie de session (important)
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    // 4️⃣ Détruire complètement la session
    session_destroy();

    // 5️⃣ Rediriger vers la page d’accueil ou connexion
    header("Location: /");
    exit;

?>