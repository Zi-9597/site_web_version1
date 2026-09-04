<?php
    /* ============================================================================
    📌 CONTROLLER – SUPPRESSION D’UN GOODIES (SÉCURISÉ + CSRF)
    - Appelé via AJAX (JSON)
    - Accès réservé : Membres du bureau uniquement
    - Reçoit : { id_goodies, pikachu_csrf }
    - Retourne : { success: true | false }
    ============================================================================ */

    require_once "commun/init.php";
    header("Content-Type: application/json; charset=utf-8");

    /* ============================================================
    🔐 1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
    ============================================================ */

    $user = require_authenticated_user($user);

    /* ============================================================
    🔐 2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ============================================================ */

    require_bureau_member($user);

    /* ============================================================
    📌 3️⃣ MÉTHODE HTTP
    ============================================================ */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    /* ============================================================
    📥 4️⃣ LECTURE & VALIDATION DU JSON
    ============================================================ */

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "JSON invalide"
        ]);
        exit;
    }

    /* ============================================================
    🛡️ 4️⃣ bis — VÉRIFICATION CSRF
    ============================================================ */

    require_csrf($data);

    /* ============================================================
    🧪 5️⃣ VALIDATION ID GOODIES
    ============================================================ */

    $id_goodies = $data['id_goodies'] ?? null;

    if (!ctype_digit((string)$id_goodies) || (int)$id_goodies <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Identifiant invalide"
        ]);
        exit;
    }

    $goodiesId = (int) $id_goodies;

    /* ============================================================
    🗑️ 6️⃣ SUPPRESSION EN BASE + ROTATION CSRF
    ============================================================ */

    try {

        $success = EEA_Database::deleteGoodies($goodiesId);

        if ($success) {
            // 🔁 Rotation du token après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($success ? 200 : 500);
        echo json_encode([
            "success" => (bool) $success
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
