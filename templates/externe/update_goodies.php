<?php
require_once "require_db.php";
header("Content-Type: application/json");

try {

    if (empty($_GET['id_goodies'])) {
        throw new Exception("ID goodies manquant");
    }

    $id_goodies = (int)$_GET['id_goodies'];
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        empty($data['nom_goodies']) ||
        !isset($data['prix']) ||
        empty($data['description'])
    ) {
        throw new Exception("Données invalides");
    }

    $ok = EEA_Database::updateGoodies($id_goodies, [
        'nom_goodies' => $data['nom_goodies'],
        'prix'        => (float)$data['prix'],
        'lien'        => $data['lien'] ?? null,
        'description' => $data['description']
    ]);

    echo json_encode([
        'success' => $ok
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
