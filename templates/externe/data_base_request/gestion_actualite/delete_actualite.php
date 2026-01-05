<?php
    /* ============================================================================
    📌 CONTROLLER – SUPPRESSION D’UNE ACTUALITÉ (SÉCURISÉ)
    - Appelé via AJAX : /?dest=delete_actualite
    - Accès réservé : Président & Membres du bureau
    - Reçoit JSON { actu_id }
    ============================================================================ */

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ============================================================ */

    // Utilisateur non connecté ou session invalide → logout (déjà partiellement géré par init.php)
    if (!$user) {
        header("Location: /?dest=logout");
        exit;
    }

    // Utilisateur connecté MAIS pas membre du bureau → logout immédiat
    if (empty($user['membre_bureau'])) {
        header("Location: /?dest=logout");
        exit;
    }

    /* ============================================================
    2️⃣ MÉTHODE HTTP
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
    📥 3) LECTURE & VALIDATION DU JSON
    ============================================================ */
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données JSON invalides'
        ]);
        exit;
    }

    /* ============================================================
    🧪 4) VALIDATION DES CHAMPS OBLIGATOIRES
    ============================================================ */
    $actu_id = (int) ($data['actu_id'] ?? 0);

    if ($actu_id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Identifiant invalide'
        ]);
        exit;
    }

    /* ============================================================
    💾 5) SUPPRESSION EN BASE
    ============================================================ */
    try {
        $success = EEA_Database::removeActualite($actu_id);

        echo json_encode([
            'success' => (bool) $success
        ]);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
    }

    exit;

?>