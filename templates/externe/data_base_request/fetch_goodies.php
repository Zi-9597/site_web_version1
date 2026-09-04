<?php
    /************************************************************
     *  CONTROLLER : FETCH GOODIES (AJAX / JSON)
     *  ➜ Accès autorisé à TOUS les utilisateurs connectés
     ************************************************************/

    // CHANGE (consistent headers/session): use the shared application bootstrap.
    require_once "commun/init.php";
    header("Content-Type: application/json");


    /* =========================================================
    2️⃣ RÉCUPÉRATION PARAMÈTRE (OPTIONNEL)
    ========================================================= */

    $id_goodies = null;

    if (isset($_GET['id_goodies'])) {

        if (!ctype_digit($_GET['id_goodies'])) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "message" => "ID goodies invalide"
            ]);
            exit;
        }

        $id_goodies = (int) $_GET['id_goodies'];
    }

    /* =========================================================
    3️⃣ RÉCUPÉRATION DES DONNÉES
    ========================================================= */

    try {

        $data = EEA_Database::fetchGoodies($id_goodies);

        if (empty($data)) {
            http_response_code(404);
            echo json_encode([
                "success" => false,
                "message" => "Goodie introuvable"
            ]);
            exit;
        }

        echo json_encode([
            "success" => true,
            "data"    => $id_goodies ? $data[0] : $data
        ]);
        exit;

    } catch (Throwable $e) {

        // ❌ Ne jamais exposer l'erreur réelle en production
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>
