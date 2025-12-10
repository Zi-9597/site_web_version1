<?php

    require_once "require_db.php";

    $id = intval($_GET["id_offre"]);

    try {
        $ok = EEA_Database::removeJob($id);

        echo json_encode(["success" => $ok]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    exit;
?>
