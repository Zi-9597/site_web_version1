<?php

    require_once "require_db.php";
    // Lire le JSON reçu
    $input = json_decode(file_get_contents("php://input"), true);

    $id     = (int) $input["id_offre"];
    $specs  = $input["specialites"];
    
    // Préparer les données pour updateJob()
    $offreData = [
        "titre_offre"  => $input["titre_offre"],
        "url_linkedin" => $input["url_linkedin"],
        "description"  => $input["description"],
        "type_contrat" => $input["type_contrat"]
    ];

    try {
        $ok = EEA_Database::updateJob($id, $offreData, $specs);

        echo json_encode([
            "success" => $ok
        ]);
        
    } catch (Exception $e) {

        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    exit;
?>