<?php
    /************************************************************
     *  CONTROLLER : UPDATE EVENT (AJAX / JSON)
     *  Accès : Membres du bureau UNIQUEMENT
     *  Sécurité centralisée via init.php
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

    /* =========================================================
    4️⃣ LECTURE DU JSON
    ========================================================= */

    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "JSON invalide"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ VALIDATION DES CHAMPS
    ========================================================= */

    $id_event   = isset($data['id_event']) ? (int)$data['id_event'] : 0;
    $nom_event  = trim($data['nom_event'] ?? '');
    $date_event = trim($data['date_event'] ?? '');
    $desc_event = trim($data['desc_event'] ?? '');
    $url_form   = trim($data['url_form'] ?? '');

    if (
        $id_event <= 0 ||
        $nom_event === '' ||
        $date_event === '' ||
        $desc_event === ''
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Champs manquants ou invalides"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ NORMALISATION DE LA DATE
    HTML <input type="date"> → YYYY-MM-DD
    ========================================================= */

    $dateObj = DateTime::createFromFormat('Y-m-d', $date_event);
    if (!$dateObj) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Format de date invalide"
        ]);
        exit;
    }

    $date_event_sql = $dateObj->format('Y-m-d 00:00:00');

    /* =========================================================
    7️⃣ DONNÉES POUR LA BDD
    ========================================================= */

    $updateData = [
        'id_event'   => $id_event,
        'nom_event'  => $nom_event,
        'date_event' => $date_event_sql,
        'desc_event' => $desc_event,
        'url_form'   => $url_form
    ];

    /* =========================================================
    8️⃣ UPDATE EN BASE
    ========================================================= */

    try {

        $ok = EEA_Database::updateEvent($updateData);

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