<?php
    /* ============================================================================
    📌 CONTROLLER – AJOUT D’UN GOODIES (SÉCURISÉ)
    - Appelé via AJAX (JSON)
    - Accès réservé : Membres du bureau uniquement
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

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données JSON invalides'
        ]);
        exit;
    }

    /* ============================================================
    🧪 4️⃣ VALIDATION DES CHAMPS
    ============================================================ */

    $nom  = trim($data['nom_goodies'] ?? '');
    $prix = $data['prix'] ?? null;
    $desc = trim($data['description'] ?? '');
    $lien = trim($data['lien'] ?? '');

    if ($nom === '' || $desc === '' || !is_numeric($prix)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Champs obligatoires manquants ou invalides'
        ]);
        exit;
    }

    /* ============================================================
    🧼 5️⃣ CONTRÔLES MINIMAUX
    ============================================================ */

    if (
        mb_strlen($nom) > 255 ||
        mb_strlen($desc) > 3000 ||
        $prix < 0
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Données non valides'
        ]);
        exit;
    }

    /* ============================================================
    💾 6️⃣ INSERTION EN BASE
    ============================================================ */

    try {

        $success = EEA_Database::addGoodies(
            [
                'nom_goodies' => $nom,
                'prix'        => (float) $prix,
                'lien'        => $lien !== '' ? $lien : null,
                'description' => $desc
            ],
            $user['id_membre']
        );

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