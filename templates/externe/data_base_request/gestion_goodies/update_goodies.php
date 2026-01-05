<?php
    /************************************************************
     *  CONTROLLER : UPDATE GOODIES (AJAX / JSON)
     *  ➜ Accès réservé aux membres du bureau
     ************************************************************/

    require_once "commun/init.php";
    header("Content-Type: application/json");

    /* =========================================================
    1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
    ========================================================= */

    if (!$user) {
        header("Location: /?dest=logout");
        exit;
    }

    /* =========================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ========================================================= */

    if (empty($user['membre_bureau'])) {
        header("Location: /?dest=logout");
        exit;
    }

    /* =========================================================
    3️⃣ MÉTHODE HTTP
    ========================================================= */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    /* =========================================================
    4️⃣ LECTURE DU JSON
    ========================================================= */

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "JSON invalide"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ VALIDATION DES DONNÉES
    ========================================================= */

    $id_goodies = isset($data['id_goodies']) ? (int) $data['id_goodies'] : 0;

    $payload = [
        'nom_goodies' => trim($data['nom_goodies'] ?? ''),
        'prix'        => $data['prix'] ?? null,
        'lien'        => trim($data['lien'] ?? ''),
        'description' => trim($data['description'] ?? '')
    ];

    if (
        $id_goodies <= 0 ||
        $payload['nom_goodies'] === '' ||
        !is_numeric($payload['prix'])
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Données invalides"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ CONTRÔLES MINIMAUX
    ========================================================= */

    if (
        mb_strlen($payload['nom_goodies']) > 255 ||
        mb_strlen($payload['description']) > 3000 ||
        $payload['prix'] < 0
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Valeurs non valides"
        ]);
        exit;
    }

    /* =========================================================
    7️⃣ UPDATE EN BASE (signature respectée)
    ========================================================= */

    try {

        $ok = EEA_Database::updateGoodies(
            $id_goodies,
            [
                'nom_goodies' => $payload['nom_goodies'],
                'prix'        => (float) $payload['prix'],
                'lien'        => $payload['lien'] !== '' ? $payload['lien'] : null,
                'description' => $payload['description']
            ]
        );

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool) $ok
        ]);
        exit;

    } catch (Throwable $e) {

        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>