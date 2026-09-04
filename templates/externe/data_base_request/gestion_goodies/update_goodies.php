<?php
    /************************************************************
     * CONTROLLER : UPDATE GOODIES (AJAX / JSON)
     * ➜ Accès réservé aux membres du bureau
     * ➜ Protection CSRF + rotation
     ************************************************************/

    require_once "commun/init.php";
    header("Content-Type: application/json");

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    $user = require_authenticated_user($user);

    /* =========================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU
    ========================================================= */

    require_bureau_member($user);

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
    🛡️ 4️⃣ bis — VÉRIFICATION CSRF (pikachu)
    ========================================================= */

    require_csrf($data);

    /* =========================================================
    5️⃣ VALIDATION DES DONNÉES
    ========================================================= */

    $id_goodies = isset($data['id_goodies']) ? (int)$data['id_goodies'] : 0;

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
    if ($payload['lien'] !== '' && !filter_var($payload['lien'], FILTER_VALIDATE_URL)) {
        json_response(['success' => false, 'message' => 'Lien invalide'], 400);
    }

    /* =========================================================
    6️⃣ CONTRÔLES MINIMAUX
    ========================================================= */

    if (
        mb_strlen($payload['nom_goodies']) > 255 ||
        mb_strlen($payload['description']) > 3000 ||
        (float)$payload['prix'] < 0
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Valeurs non valides"
        ]);
        exit;
    }

    /* =========================================================
    7️⃣ UPDATE EN BASE + ROTATION CSRF
    ========================================================= */

    try {

        $ok = EEA_Database::updateGoodies(
            $id_goodies,
            [
                'nom_goodies' => $payload['nom_goodies'],
                'prix'        => (float)$payload['prix'],
                'lien'        => $payload['lien'] !== '' ? $payload['lien'] : null,
                'description' => $payload['description']
            ]
        );

        if ($ok) {
            // 🔁 Rotation du token CSRF après modification
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool)$ok
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
