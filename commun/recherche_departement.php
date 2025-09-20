<?php
    header("Content-Type: application/json; charset=UTF-8");
    require_once "require_db.php";

    $data = json_decode(file_get_contents("php://input"), true);
    $cp = $data["code_postal"] ?? "";

    
    if (preg_match("/^[0-9]{5}$/", $cp)) {
        $result = EEA_Database::getLocalisationByCP($cp);

        if ($result) {
            echo json_encode([
                "success" => true,
                "commune" => $result["nom_commune"],
                "departement" => $result["nom_departement"],
                "region" => $result["nom_region"]
            ]);
            exit;
        }
    }

    echo json_encode(["success" => false]);
