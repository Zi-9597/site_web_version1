<?php

    require_once "require_db.php";

    header('Content-Type: application/json');

    try {

        /* =========================================
        📥 Données envoyées en AJAX
        ========================================= */
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            echo json_encode([
                'success' => false,
                'error'   => 'Données invalides'
            ]);
            exit;
        }

        /* =========================================
        🚫 Domaine email interdit
        ========================================= */
        if (str_ends_with(strtolower($data['email']), '@univ-lille.fr')) {
            echo json_encode([
                'success' => false,
                'error'   => 'Domaine email non autorisé'
            ]);
            exit;
        }

        /* =========================================
        👤 RÉCUPÉRATION id_membre (OPTIONNEL)
        ========================================= */
        $id_membre = null;

        if (!empty($_GET['id_user'])) {
            [$id_membre] = explode("_", $_GET['id_user']);
        }

        /* =========================================
        💾 INSERTION EN BASE
        ========================================= */
        $ok = EEA_Database::addAide(
            $id_membre,
            [
                'nom'           => $data['nom'] ?? null,
                'prenom'        => $data['prenom'] ?? null,
                'email'         => $data['email'],
                'telephone_num' => $data['telephone'] ?? null,
                'type_aide_id'  => (int)$data['type_aide_id'],
                'sujet'         => $data['sujet'],
                'message'       => $data['message']
            ]
        );

        echo json_encode([
            'success' => $ok
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'error'   => 'Erreur serveur'
        ]);
    }

    exit;

?>