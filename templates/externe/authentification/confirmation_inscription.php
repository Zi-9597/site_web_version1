<?php

    require_once "require_db.php";

    if (!isset($_GET['token']) || empty($_GET['token'])) {
        header("Location: /?dest=erreur_inscription");
        exit;
    }

    $token = trim($_GET['token']);

    // Validation stricte du token
    if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
        header("Location: /?dest=erreur_inscription");
        exit;
    }

    // Confirmation
    $success = EEA_Database::confirmSubscriber($token);

    if ($success) {
        header("Location: /?dest=confirmed");
        exit;
    } else {
        header("Location: /?dest=erreur_inscription");
        exit;
    }
?>