<?php
/* ============================================================================
   📌 CONTROLLER – UPDATE ACTUALITÉ (SANS DATE)
============================================================================ */
    include_once "require_db.php";

    $data = json_decode(file_get_contents("php://input"), true);

    // Vérification minimale
    if (
        empty($data['actu_id']) ||
        empty($data['titre_actu']) ||
        empty($data['desc_actu'])
    ) {
        echo json_encode(['success' => false]);
        exit;
    }

    try {
        $success = EEA_Database::updateActualite([
            'actu_id'    => (int)$data['actu_id'],
            'titre_actu' => trim($data['titre_actu']),
            'lien_actu'  => trim($data['lien_actu'] ?? ''),
            'desc_actu'  => trim($data['desc_actu'])
        ]);

        echo json_encode(['success' => $success]);

    } catch (Exception $e) {
        echo json_encode(['success' => false]);
    }

    exit;
?>
