<?php

require_once "require_db.php";

header("Content-Type: application/json");

try {

    /* ============================
       📥 Données reçues en AJAX
    ============================ */
    $data = json_decode(file_get_contents("php://input"), true);

    if (!$data || empty($data['id_goodies'])) {
        echo json_encode([
            'success' => false,
            'error'   => 'ID du goodies manquant'
        ]);
        exit;
    }

    $goodiesId = (int) $data['id_goodies'];

    /* ============================
       🗑️ Suppression en base
    ============================ */
    $ok = EEA_Database::deleteGoodies($goodiesId);

    echo json_encode([
        'success' => $ok
    ]);

} catch (Exception $e) {

    echo json_encode([
        'success' => false,
        'error'   => 'Erreur serveur : '.$e
    ]);
}

exit;
