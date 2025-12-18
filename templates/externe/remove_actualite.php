<?php
/* ============================================================================
   📌 CONTROLLER – SUPPRESSION D’UNE ACTUALITÉ
   - Appelé via AJAX : /?dest=remove_actualite
   - Reçoit l’ID de l’actualité
   - Retourne { success: true | false }
============================================================================ */



    include_once "require_db.php";

    $data = json_decode(file_get_contents("php://input"), true);

    // Vérification minimale
    if (empty($data['actu_id'])) {
        echo json_encode(['success' => false]);
        exit;
    }

    try {
        $success = EEA_Database::removeActualite((int)$data['actu_id']);
        echo json_encode(['success' => $success]);

    } catch (Exception $e) {
        echo json_encode(['success' => false]);
    }

    exit;

?>

