<?php

    require_once "require_db.php";
    header('Content-Type: application/json');

    try {

        if (empty($_GET['aide_id'])) {
            echo json_encode([
                'success' => false,
                'error' => 'ID manquant'
            ]);
            exit;
        }

        $aide_id = (int) $_GET['aide_id'];

        $ok = EEA_Database::deleteAide($aide_id);

        echo json_encode([
            'success' => $ok
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }

?>