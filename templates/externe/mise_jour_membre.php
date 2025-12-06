<?php
header("Content-Type: application/json");
require_once "require_db.php";

try {
    // Lecture JSON brut envoyé par fetch()
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input || empty($input["id"])) {
        echo json_encode([
            "success" => false,
            "message" => "Requête invalide"
        ]);
        exit;
    }

    // Exécuter la mise à jour
    $ok = EEA_Database::updateMember($input);

    if ($ok) {
        echo json_encode([
            "success" => true,
            "message" => "Mise à jour effectuée"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Échec lors de la mise à jour"
        ]);
    }

} catch (Exception $e) {

    echo json_encode([
        "success" => false,
        "message" => "Erreur serveur : " . $e->getMessage()
    ]);
}