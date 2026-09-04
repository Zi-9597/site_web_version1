<?php
    /************************************************************
     *  CONTROLLER : AJOUT D'ÉVÉNEMENT
     *  - Sécurité centralisée via init.php
     *  - Accès STRICTEMENT réservé aux membres du bureau
     *  - Protection CSRF + rotation du token
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json; charset=utf-8');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    $user = require_authenticated_user($user);

    /* =========================================================
    2️⃣ MEMBRE DU BUREAU UNIQUEMENT
    ========================================================= */

    require_bureau_member($user);

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
    🛡️ 4️⃣ VÉRIFICATION CSRF
    ========================================================= */

    require_csrf($_POST);

    /* =========================================================
    5️⃣ RÉCUPÉRATION DES DONNÉES
    ========================================================= */

    $nom_event  = trim($_POST["nom_event"]  ?? '');
    $date_event = trim($_POST["date"]       ?? '');
    $desc_event = trim($_POST["desc_event"] ?? '');
    $url_event  = trim($_POST["url_form"]   ?? '');

    /* =========================================================
    6️⃣ VALIDATION MINIMALE
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
    7️⃣ IDENTITÉ UTILISATEUR
    ========================================================= */

    $id_membre = $user['id_membre'];

    /* =========================================================
    8️⃣ FORMATAGE DATE ÉVÉNEMENT
    ========================================================= */

    $d = DateTime::createFromFormat('!d/m/Y', $date_event);
    if (!$d || $d->format('d/m/Y') !== $date_event) {
        json_response(['status' => 'error', 'message' => 'Date invalide'], 400);
    }
    $date_event = $d->format('Y-m-d 00:00:00');

    /* =========================================================
    9️⃣ DATE DE CRÉATION
    ========================================================= */

    $date_creation = (new DateTime())->format('Y-m-d H:i:s');

    /* =========================================================
    🔟 DONNÉES BASE
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
    🚀 INSERTION + ROTATION CSRF
    ========================================================= */

    try {

        if (EEA_Database::addEvent($data)) {

            // 🔁 ROTATION DU TOKEN CSRF (bonne pratique)
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

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
