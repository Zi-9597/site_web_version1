<?php
    /************************************************************
     *  API : Récupération d’une offre (AJAX)
     *  - Sécurité centralisée via init.php
     *  - Accès réservé aux utilisateurs connectés
     *  - Réponse JSON
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json; charset=utf-8');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    // init.php garantit : session + timeout + cohérence minimale
    $user = require_authenticated_user($user);

    /* =========================================================
    2️⃣ VALIDATION DU PARAMÈTRE
    ========================================================= */

    if (
        !isset($_GET['id_job']) ||
        !ctype_digit($_GET['id_job'])
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Identifiant d'offre invalide"
        ]);
        exit;
    }

    $idJob = (int) $_GET['id_job'];

    /* =========================================================
    3️⃣ RÉCUPÉRATION DE L’OFFRE
    ========================================================= */

    try {

        // id_job est unique → on attend 0 ou 1 résultat
        $result = EEA_Database::fetchUserJobs(null, $idJob);

        if (empty($result)) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Offre introuvable"
            ]);
            exit;
        }

        /* =====================================================
        4️⃣ RÉPONSE OK
        ===================================================== */

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "data"    => $result[0]
        ]);
        exit;

    } catch (Throwable $e) {

        // Sécurité : ne jamais exposer l’erreur interne
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>
