<?php

    /* ============================================================================
    📌 CONTROLLER – SUPPRESSION D’UNE AIDE (SÉCURISÉ)
    - AJAX / JSON
    - Accès réservé : Membres du bureau
    ============================================================================ */

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ UTILISATEUR CONNECTÉ (géré par init.php)
    ============================================================ */

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur non authentifié'
        ]);
        exit;
    }

    /* ============================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ============================================================ */

    if (empty($user['membre_bureau'])) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Accès interdit'
        ]);
        exit;
    }

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

    if (!is_array($data) || empty($data['aide_id']) || !ctype_digit((string)$data['aide_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID aide invalide'
        ]);
        exit;
    }

    $aide_id = (int) $data['aide_id'];

    /* ============================================================
    5️⃣ SUPPRESSION EN BASE
    ============================================================ */

    try {
        $success = EEA_Database::deleteAide($aide_id);

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