<?php
/************************************************************
 *  CONTROLLER : SUPPRESSION D'UN ÉVÈNEMENT (AJAX / JSON)
 *  ➜ Accès réservé aux membres du bureau
 *  ➜ Protection CSRF + rotation du token
 ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur non authentifié"
        ]);
        exit;
    }

    /* =========================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ========================================================= */

    if (empty($user['membre_bureau'])) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Accès interdit"
        ]);
        exit;
    }

    /* =========================================================
    3️⃣ MÉTHODE HTTP
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
    4️⃣ LECTURE DU JSON
    ========================================================= */

    $input = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($input) ||
        empty($input['id_event']) ||
        empty($input['pikachu_csrf'])
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Données invalides"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ VÉRIFICATION CSRF
    ========================================================= */

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $input['pikachu_csrf'])
    ) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "CSRF invalide"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ VALIDATION ID ÉVÈNEMENT
    ========================================================= */

    $idEvent = $input['id_event'];

    if (!ctype_digit((string)$idEvent)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ID événement invalide"
        ]);
        exit;
    }

    $idEvent = (int) $idEvent;

    /* =========================================================
    7️⃣ SUPPRESSION + ROTATION CSRF
    ========================================================= */

    try {

        $ok = EEA_Database::removeEvent($idEvent);

        if ($ok) {
            // 🔁 Rotation du token après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool)$ok
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