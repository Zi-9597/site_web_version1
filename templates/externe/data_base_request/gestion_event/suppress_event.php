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

    $user = require_authenticated_user($user);

    /* =========================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ========================================================= */

    require_bureau_member($user);

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

    require_csrf($input);

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

        /* CHANGE (IDOR prevention): include the creator in the DELETE condition. */
        $ok = EEA_Database::removeOwnedEvent($idEvent, $user['id_membre']);

        if ($ok) {
            $csrfToken = rotate_csrf_token();
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool)$ok,
            "csrf_token" => $csrfToken ?? null
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
