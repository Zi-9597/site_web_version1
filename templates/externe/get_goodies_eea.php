<?php
require_once "require_db.php";
header("Content-Type: application/json");

try {

    $id_goodies = null;

    if (!empty($_GET['id_goodies'])) {
        $id_goodies = (int)$_GET['id_goodies'];
    }

    $data = EEA_Database::fetchGoodies($id_goodies);

    echo json_encode([
        'success' => true,
        'data' => $data[0]
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
