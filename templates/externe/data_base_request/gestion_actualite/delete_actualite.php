<?php
    /* ============================================================================
    📌 CONTROLLER – SUPPRESSION D’UNE ACTUALITÉ (AJAX / JSON)
    - Accès réservé : membres du bureau
    - Protection CSRF + rotation du token
    ============================================================================ */

    // CHANGE (authorization): init revalidates the user's current database role.
    require_once "commun/init.php";

    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ SESSION VALIDE
    ============================================================ */

    $user = require_authenticated_user($user);

    /* ============================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU
    ============================================================ */

    require_bureau_member($user);

    /* ============================================================
    3️⃣ MÉTHODE HTTP
    ============================================================ */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Méthode non autorisée'
        ]);
        exit;
    }

    /* ============================================================
    4️⃣ LECTURE JSON
    ============================================================ */

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'JSON invalide'
        ]);
        exit;
    }

    /* ============================================================
    🛡️ 4️⃣ bis — CSRF
    ============================================================ */

    require_csrf($data);

    /* ============================================================
    5️⃣ VALIDATION ID ACTUALITÉ
    ============================================================ */

    $actu_id = $data['actu_id'] ?? null;

    if (!ctype_digit((string)$actu_id)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID actualité invalide'
        ]);
        exit;
    }

    $actu_id = (int) $actu_id;

    /* ============================================================
    6️⃣ SUPPRESSION + ROTATION CSRF
    ============================================================ */

    try {

        $ok = EEA_Database::removeActualite($actu_id);

        if ($ok) {
            // 🔁 Rotation du token après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            'success' => (bool) $ok
        ]);
        exit;

    } catch (Throwable $e) {

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
        exit;
    }
?>
