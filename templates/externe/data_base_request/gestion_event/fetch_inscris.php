<?php
    require_once "commun/init.php";
    header('Content-Type: application/json');

   /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    $user = require_authenticated_user($user);

    /* =========================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU
    ========================================================= */

    require_bureau_member($user);


    /* =========================================================
    3️⃣ MÉTHODE HTTP : GET
    ========================================================= */

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }
   /* =========================================================
    4️⃣ VALIDATION ID ÉVÈNEMENT
    ========================================================= */

    $idEvent = $_GET['id_event'] ?? null;

    if ($idEvent === null || !ctype_digit((string)$idEvent)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "id_event invalide"
        ]);
        exit;
    }

    $idEvent = (int) $idEvent;
    // CHANGE (privacy/IDOR): participant contact data is visible only to the event owner.
    if (empty(EEA_Database::fetch_events($user['id_membre'], $idEvent))) {
        json_response(['success' => false, 'message' => 'Événement introuvable'], 404);
    }

    /* =========================================================
    5️⃣ RÉCUPÉRATION DES PARTICIPANTS
    ========================================================= */

    try {

        $rows = EEA_Database::fetchParticipantsByEventId($idEvent);

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "data"    => $rows
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
