<?php
    header('Content-Type: application/json');

    require_once "require_db.php";

    /* ==================================================
    VÉRIFICATION REQUÊTE
    ================================================== */

    // POST uniquement
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(["success" => false, "message" => "Requête invalide"]);
        exit;
    }


    /* ==================================================
    LECTURE JSON
    ================================================== */

    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    if (!is_array($data) || empty($data['email'])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email manquant"]);
        exit;
    }

    /* ==================================================
    DONNÉES UTILISATEUR
    ================================================== */

    $mail = trim($data['email']);

    // Format email
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email invalide"]);
        exit;
    }

    // Taille max
    if (strlen($mail) > 255) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Email invalide"]);
        exit;
    }

    /* ==================================================
    VÉRIFICATION BASE DE DONNÉES
    ================================================== */

    $userDb = EEA_Database::fetc_user_mail($mail);



    /* ==================================================
    RÉPONSE AJAX
    ================================================== */
    echo json_encode([
        "success" => true,
        "exists"  => !empty($userDb)
    ]);


  
    exit;
?>