<?php
    /* ============================================================================
    📌 CONTROLLER – SUPPRESSION D’UNE AIDE (SÉCURISÉ)
    - AJAX / JSON
    - Accès réservé : Membres du bureau
    - Protection CSRF + rotation
    ============================================================================ */

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ============================================================ */

    $user = require_authenticated_user($user);

    /* ============================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
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
    4️⃣ LECTURE & VALIDATION DU JSON
    ============================================================ */

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($data) ||
        empty($data['aide_id']) ||
        !ctype_digit((string)$data['aide_id'])
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID aide invalide'
        ]);
        exit;
    }

    /* ============================================================
    🛡️ 4️⃣ bis — VÉRIFICATION CSRF
    ============================================================ */

    require_csrf($data);

    $aide_id = (int) $data['aide_id'];

    /* ============================================================
    5️⃣ SUPPRESSION EN BASE + ROTATION CSRF
    ============================================================ */

    try {

        $success = EEA_Database::deleteAide($aide_id);

        if ($success) {
            // 🔁 Rotation du token CSRF après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        echo json_encode([
            'success' => (bool) $success
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
