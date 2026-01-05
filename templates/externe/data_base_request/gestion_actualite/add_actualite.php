<?php
    /* ============================================================================
    📌 CONTROLLER – AJOUT D’UNE ACTUALITÉ (AJAX / JSON)
    - Sécurité centralisée via init.php
    - Accès STRICT : membres du bureau uniquement
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
    3️⃣ LECTURE & VALIDATION DU JSON
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
    4️⃣ VALIDATION DES CHAMPS
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
    5️⃣ CONTRÔLES MINIMAUX (ANTI ABUS)
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
    6️⃣ INSERTION EN BASE
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