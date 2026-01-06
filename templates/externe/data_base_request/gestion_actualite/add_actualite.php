<?php
    /* ============================================================================
    📌 CONTROLLER – AJOUT D’UNE ACTUALITÉ (AJAX / JSON)
    - Sécurité centralisée via init.php
    - Accès STRICT : membres du bureau uniquement
    - Protection CSRF + rotation du token
    ============================================================================ */

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
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

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données JSON invalides'
        ]);
        exit;
    }

    /* ============================================================
    🛡️ 4️⃣ bis — VÉRIFICATION CSRF
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

    $titre = trim($data['titre_actu'] ?? '');
    $desc  = trim($data['desc_actu'] ?? '');
    $lien  = trim($data['lien_actu'] ?? '');

    if ($titre === '' || $desc === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Titre et description obligatoires'
        ]);
        exit;
    }

    /* ============================================================
    6️⃣ CONTRÔLES MINIMAUX (ANTI ABUS)
    ============================================================ */

    if (mb_strlen($titre) > 255 || mb_strlen($desc) > 3000) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Contenu trop long'
        ]);
        exit;
    }

    /* ============================================================
    7️⃣ INSERTION EN BASE + ROTATION CSRF
    ============================================================ */

    try {

        $ok = EEA_Database::addActualite(
            [
                'titre_actu' => $titre,
                'lien_actu'  => $lien,
                'desc_actu'  => $desc
            ],
            $user['id_membre'] // 🔒 ID FORCÉ depuis la session
        );

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

        // ❌ Ne jamais exposer l’erreur réelle en prod
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
        exit;
    }
?>