<?php
    require_once "require_db.php";
    header("Content-Type: application/json");

    try {

        $data = json_decode(file_get_contents("php://input"), true);

        if (
            empty($data['nom_goodies']) ||
            !isset($data['prix']) ||
            empty($data['description'])
        ) {
            echo json_encode([
                'success' => false,
                'error' => 'Champs obligatoires manquants'
            ]);
            exit;
        }

        $ok = EEA_Database::addGoodies([
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

?>