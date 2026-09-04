<?php
    /* ============================================================================
    📌 CONTROLLER – UPDATE D’UNE ACTUALITÉ (AJAX / JSON)
    - Accès réservé : Président & membres du bureau
    - Protection CSRF + rotation du token
    ============================================================================ */

    // CHANGE (authorization): init revalidates the user's current database role.
    require_once "commun/init.php";

    header('Content-Type: application/json');

    /* ============================================================
    1️⃣ VÉRIFICATION DE LA SESSION
    ============================================================ */

    $user = require_authenticated_user($user);

    /* ============================================================
    2️⃣ VÉRIFICATION DES DROITS
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

    require_csrf($data);

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
    if ($lien !== '' && !filter_var($lien, FILTER_VALIDATE_URL)) {
        json_response(['success' => false, 'message' => 'Lien invalide'], 400);
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
