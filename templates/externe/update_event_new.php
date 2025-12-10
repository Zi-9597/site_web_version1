<?php

    require_once "require_db.php";

    $data = json_decode(file_get_contents("php://input"), true);

    try {
        $ok = EEA_Database::updateEvent($data);
        echo json_encode(["success" => $ok]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }

    exit;


?>