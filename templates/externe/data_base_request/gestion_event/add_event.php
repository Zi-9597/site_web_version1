<?php
    /************************************************************
     *  CONTROLLER : AJOUT D'ÉVÉNEMENT
     *  - Sécurité centralisée via init.php
     *  - Accès STRICTEMENT réservé aux membres du bureau
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json; charset=utf-8');

    /* =========================================================
    1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
    ========================================================= */

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "status"  => "error",
            "message" => "Utilisateur non authentifié"
        ]);
        exit;
    }

    /* =========================================================
    2️⃣ SÉCURITÉ : MEMBRE DU BUREAU UNIQUEMENT
    ➜ Protection même en accès direct (GET / POST manuel)
    ========================================================= */

    if (empty($user['membre_bureau'])) {
        http_response_code(403);
        echo json_encode([
            "status"  => "error",
            "message" => "Accès interdit"
        ]);
        exit;
    }

    /* =========================================================
    3️⃣ MÉTHODE HTTP
    ========================================================= */

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode([
            "status"  => "error",
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    /* =========================================================
    4️⃣ RÉCUPÉRATION DES DONNÉES
    ========================================================= */

    $nom_event  = trim($_POST["nom_event"]  ?? '');
    $date_event = trim($_POST["date"]       ?? '');
    $desc_event = trim($_POST["desc_event"] ?? '');
    $url_event  = trim($_POST["url_form"]   ?? '');

    /* =========================================================
    5️⃣ VALIDATION MINIMALE
    ========================================================= */

    if ($nom_event === '' || $date_event === '') {
        http_response_code(400);
        echo json_encode([
            "status"  => "error",
            "message" => "Champs obligatoires manquants"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ IDENTITÉ UTILISATEUR (SESSION FIABLE)
    ========================================================= */

    $id_membre = $user['id_membre'];

    /* =========================================================
    7️⃣ FORMATAGE DATE ÉVÉNEMENT
    ========================================================= */

    $d = DateTime::createFromFormat('d/m/Y', $date_event);
    $date_event = $d
        ? $d->format('Y-m-d 00:00:00')
        : (new DateTime())->format('Y-m-d 00:00:00');

    /* =========================================================
    8️⃣ DATE DE CRÉATION
    ========================================================= */

    $date_creation = (new DateTime())->format('Y-m-d H:i:s');

    /* =========================================================
    9️⃣ DONNÉES BASE
    ========================================================= */

    $data = [
        ":nom_event"     => $nom_event,
        ":date_event"    => $date_event,
        ":desc_event"    => $desc_event,
        ":id_membre"     => $id_membre,
        ":url_form"      => $url_event,
        ":date_creation" => $date_creation
    ];

    /* =========================================================
    🔟 INSERTION EN BASE
    ========================================================= */

    try {

        if (EEA_Database::addEvent($data)) {

            http_response_code(200);
            echo json_encode([
                "status" => "success"
            ]);

        } else {

            http_response_code(500);
            echo json_encode([
                "status"  => "error",
                "message" => "Échec de l'insertion"
            ]);
        }

    } catch (Throwable $e) {

        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Erreur serveur"
        ]);
    }
?>