<?php
    /* ============================================================================
    📌 CONTROLLER – AJOUT D’UN GOODIES (AJAX / JSON)
    - Accès réservé : membres du bureau
    - Protection CSRF + rotation
    ============================================================================ */

    require_once "commun/init.php";
    header("Content-Type: application/json");

    /* ============================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ============================================================ */

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Non authentifié'
        ]);
        exit;
    }

    /* ============================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU
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
    4️⃣ LECTURE DU JSON
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
    🛡️ 4️⃣ bis — CSRF (pikachu)
    ============================================================ */

    $pikachu_csfr = $data['pikachu_csfr'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        empty($pikachu_csfr) ||
        !hash_equals($_SESSION['csrf_token'], $pikachu_csfr)
    ) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'CSRF invalide'
        ]);
        exit;
    }

    /* ============================================================
    5️⃣ VALIDATION DES CHAMPS
    ============================================================ */

    $nom  = trim($data['nom_goodies'] ?? '');
    $prix = $data['prix'] ?? null;
    $desc = trim($data['description'] ?? '');
    $lien = trim($data['lien'] ?? '');

    if ($nom === '' || $desc === '' || !is_numeric($prix)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Champs manquants ou invalides'
        ]);
        exit;
    }

    /* ============================================================
    6️⃣ CONTRÔLES MINIMAUX
    ============================================================ */

    if (
        mb_strlen($nom) > 255 ||
        mb_strlen($desc) > 3000 ||
        (float)$prix < 0
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données non valides'
        ]);
        exit;
    }

    /* ============================================================
    7️⃣ INSERTION + ROTATION CSRF
    ============================================================ */

    try {

        $ok = EEA_Database::addGoodies(
            [
                'nom_goodies' => $nom,
                'prix'        => (float)$prix,
                'lien'        => $lien !== '' ? $lien : null,
                'description' => $desc
            ],
            $user['id_membre']
        );

        if ($ok) {
            // 🔁 Rotation du token après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            'success' => (bool)$ok
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