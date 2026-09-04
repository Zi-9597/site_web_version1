<?php
    /************************************************************
     * CONTROLLER : SUPPRESSION OFFRE (AJAX / JSON)
     *
     * Sécurité :
     * - Utilisateur connecté
     * - Méthode POST uniquement
     * - Données JSON uniquement
     * - Protection CSRF + rotation du token
     * - Vérification propriétaire de l’offre
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    $user = require_authenticated_user($user);

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
    3️⃣ LECTURE JSON
    ========================================================= */

    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "JSON invalide"
        ]);
        exit;
    }

    /* =========================================================
    4️⃣ VÉRIFICATION CSRF
    ========================================================= */

    require_csrf($input);

    /* =========================================================
    5️⃣ VALIDATION ID OFFRE
    ========================================================= */

    $idOffre = $input['id_offre'] ?? null;

    if (!ctype_digit((string)$idOffre) || (int)$idOffre <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ID offre invalide"
        ]);
        exit;
    }

    $idOffre = (int) $idOffre;

    /* =========================================================
    6️⃣ VÉRIFICATION PROPRIÉTÉ OFFRE
    ========================================================= */

    $sessionEmail = $user['email'];

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

    if (!hash_equals($offre['email_user'], $sessionEmail)) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Accès interdit"
        ]);
        exit;
    }

    /* =========================================================
    7️⃣ SUPPRESSION + ROTATION CSRF
    ========================================================= */

    try {

        $ok = EEA_Database::removeJob($idOffre);

        if ($ok) {
            // 🔁 Rotation du token CSRF après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool) $ok
        ]);
        exit;

    } catch (Throwable $e) {

        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>
