<?php
    /* ============================================================================
    📌 CONTROLLER – SUPPRESSION D’UN GOODIES (SÉCURISÉ)
    - Appelé via AJAX (JSON)
    - Accès réservé : Membres du bureau uniquement
    - Reçoit : { id_goodies }
    - Retourne : { success: true | false }
    ============================================================================ */

    require_once "commun/init.php";
    header("Content-Type: application/json");

    /* ============================================================
    🔐 1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
    (init.php garantit déjà la validité de la session)
    ============================================================ */

    if (!$user) {
        header("Location: /?dest=logout");
        exit;
    }

    /* ============================================================
    🔐 2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ============================================================ */

    if (empty($user['membre_bureau'])) {
        header("Location: /?dest=logout");
        exit;
    }

    /* ============================================================
    📥 3️⃣ LECTURE & VALIDATION DU JSON
    ============================================================ */

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data) || empty($data['id_goodies'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID du goodies manquant ou invalide'
        ]);
        exit;
    }

    $goodiesId = (int) $data['id_goodies'];

    if ($goodiesId <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Identifiant invalide'
        ]);
        exit;
    }

    /* ============================================================
    🗑️ 4️⃣ SUPPRESSION EN BASE
    ============================================================ */

    try {

        $success = EEA_Database::deleteGoodies($goodiesId);

        echo json_encode([
            'success' => (bool) $success
        ]);
        exit;

    } catch (Throwable $e) {

        // ❌ Ne jamais exposer l’erreur réelle en prod
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
        exit;
    }
?>