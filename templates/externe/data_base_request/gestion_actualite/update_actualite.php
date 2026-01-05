<?php
    /* ============================================================================
    📌 CONTROLLER – UPDATE D’UNE ACTUALITÉ (SÉCURISÉ)
    - Appelé via AJAX (JSON)
    - Accès réservé : Président & Membres du bureau
    ============================================================================ */

    require_once "require_db.php";
    session_start();

    header('Content-Type: application/json');

    /* ============================================================
    🔐 1) VÉRIFICATION DE LA SESSION
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
    🔐 2) VÉRIFICATION DES DROITS
    ➜ Autorisés : Président + membres du bureau
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
    🧼 5) NETTOYAGE MINIMAL (ANTI ABUS)
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
    💾 6) MISE À JOUR BDD
    ============================================================ */
    try {
        $success = EEA_Database::updateActualite([
            'actu_id'    => $actu_id,
            'titre_actu' => $titre,
            'lien_actu'  => $lien,
            'desc_actu'  => $desc
        ]);

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