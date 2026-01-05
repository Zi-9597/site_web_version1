<?php
    /************************************************************
     *  CONTROLLER : SUPPRESSION D'UN ÉVÈNEMENT (AJAX / JSON)
     *  ➜ Accès réservé aux membres du bureau
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* =========================================================
    1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
    ========================================================= */

    if (!$user) {
        header("Location: /?dest=logout");
        exit;
    }

    /* =========================================================
    2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
    ========================================================= */

    if (empty($user['membre_bureau'])) {
        header("Location: /?dest=logout");
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


    /************************************************************
     *  4️⃣ Lecture du JSON envoyé par fetch()
     ************************************************************/
    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input) || empty($input['id_event'])) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Données invalides"
        ]);
        exit;
    }

    /************************************************************
     *  5️⃣ Validation stricte de l'ID événement
     ************************************************************/
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

    /************************************************************
     *  6️⃣ Suppression sécurisée via la couche DB
     ************************************************************/
    try {

        $ok = EEA_Database::removeEvent($idEvent);

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool)$ok
        ]);
        exit;

    } catch (Throwable $e) {

        // ⚠️ Ne jamais exposer l’erreur réelle en prod
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }

?>