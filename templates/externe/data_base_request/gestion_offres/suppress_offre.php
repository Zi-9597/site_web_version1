<?php
    /************************************************************
     *  CONTROLLER : SUPPRESSION OFFRE (AJAX)
     *  - Sécurisé via init.php
     *  - Méthode : POST
     *  - ID offre : GET
     *  - Vérification propriétaire (email)
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    (init.php garantit déjà la validité de la session)
    ========================================================= */

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Non authentifié"
        ]);
        exit;
    }

    /* =========================================================
    2️⃣ MÉTHODE HTTP
    ========================================================= */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    /* =========================================================
    3️⃣ VALIDATION ID OFFRE (GET)
    ========================================================= */

    $idOffre = $_GET['id_offre'] ?? null;

    if (!ctype_digit((string)$idOffre)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ID offre invalide"
        ]);
        exit;
    }

    $idOffre = (int) $idOffre;

    /* =========================================================
    4️⃣ EMAIL UTILISATEUR (depuis la session)
    ========================================================= */

    $sessionEmail = $user['email'] ?? null;

    if (!$sessionEmail) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Session invalide"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ VÉRIFICATION PROPRIÉTÉ OFFRE
    ========================================================= */

    $pdo = EEA_Database::getInstance();

    $stmt = $pdo->prepare("
        SELECT email_user
        FROM offres
        WHERE id_offre = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $idOffre]);
    $offre = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$offre) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Offre introuvable"
        ]);
        exit;
    }

    // Sécurité contre timing attack
    if (!hash_equals((string)$offre['email_user'], (string)$sessionEmail)) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Accès interdit"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ SUPPRESSION
    ========================================================= */

    try {

        $ok = EEA_Database::removeJob($idOffre);

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool) $ok
        ]);
        exit;

    } catch (Throwable $e) {

        // ⚠️ En prod : log serveur uniquement
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>