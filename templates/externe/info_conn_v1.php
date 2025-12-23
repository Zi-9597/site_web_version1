<?php
    header('Content-Type: application/json');

    require_once "require_db.php";

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode([
            "success" => false,
            "message" => "Requête invalide"
        ]);
        exit;
    }

    // Nettoyage données
    $mail     = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($mail === '' || $password === '') {
        echo json_encode([
            "success" => false,
            "message" => "Champs manquants"
        ]);
        exit;
    }

    // Récupération utilisateur
    $test_fetch = EEA_Database::fetc_user_mail($mail);

    if (!$test_fetch) {
        echo json_encode([
            "success" => false,
            "message" => "Erreur de connexion"
        ]);
        exit;
    }

    // Vérification mot de passe
    if (!password_verify($password, $test_fetch["mot_de_passe"])) {
        echo json_encode([
            "success" => false,
            "message" => "Erreur de connexion"
        ]);
        exit;
    }

    // Succès
    $id_membre = $test_fetch["id_membre"] ?? '';
    $id_num    = $test_fetch["id"] ?? '';
    $combinedId = $id_membre . "_" . $id_num;

    echo json_encode([
        "success"  => true,
        "redirect" => "/?dest=acceuil&id_user=" . $combinedId
    ]);
    exit;