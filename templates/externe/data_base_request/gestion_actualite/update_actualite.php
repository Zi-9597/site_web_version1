<?php
    /* ============================================================================
    📌 CONTROLLER – UPDATE D’UNE ACTUALITÉ (AJAX / JSON)
    - Accès réservé : Président & membres du bureau
    - Protection CSRF + rotation du token
    ============================================================================ */

    require_once "require_db.php";
    session_start();

    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ VÉRIFICATION DE LA SESSION
    ============================================================ */

    if (
        empty($_SESSION['user']) ||
        !is_array($_SESSION['user']) ||
        empty($_SESSION['user']['id_membre'])
    ) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur non authentifié'
        ]);
        exit;
    }

    $user = $_SESSION['user'];

    /* ============================================================
    2️⃣ VÉRIFICATION DES DROITS
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

    $actu_id = (int) ($data['actu_id'] ?? 0);
    $titre   = trim($data['titre_actu'] ?? '');
    $desc    = trim($data['desc_actu'] ?? '');
    $lien    = trim($data['lien_actu'] ?? '');

    if ($actu_id <= 0 || $titre === '' || $desc === '') {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Champs obligatoires manquants'
        ]);
        exit;
    }

    /* ============================================================
    6️⃣ CONTRÔLES ANTI-ABUS
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
    7️⃣ UPDATE BDD + ROTATION CSRF
    ============================================================ */

    try {

        $success = EEA_Database::updateActualite([
            'actu_id'    => $actu_id,
            'titre_actu' => $titre,
            'lien_actu'  => $lien,
            'desc_actu'  => $desc
        ]);

        if ($success) {
            // 🔁 Rotation du token CSRF après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($success ? 200 : 500);
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