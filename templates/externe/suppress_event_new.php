<?php

    require_once "require_db.php";

    $id = intval($_GET["id_event"]);

    try {
        $ok = EEA_Database::removeEvent($id);

        echo json_encode(["success" => $ok]);
    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => $e->getMessage()
        ]);
    }

    exit;
?>
