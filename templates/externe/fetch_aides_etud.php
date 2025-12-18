<?php

    require_once "require_db.php";

    header('Content-Type: application/json');

    try {

        /* =========================================
        🔎 Récupération de l’ID aide
        ========================================= */
        if (empty($_GET['aide_id']) || !ctype_digit($_GET['aide_id'])) {
            echo json_encode([
                'success' => false,
                'error'   => 'Identifiant aide invalide'
            ]);
            exit;
        }

        $aide_id = (int) $_GET['aide_id'];

        /* =========================================
        📥 Récupération depuis la base
        ========================================= */
        $result = EEA_Database::fetchAides($aide_id);

        if (empty($result)) {
            echo json_encode([
                'success' => false,
                'error'   => 'Aide introuvable'
            ]);
            exit;
        }

        // fetch_aide retourne un tableau → on prend la première ligne
        $aide = $result[0];

        /* =========================================
        ✅ Réponse OK
        ========================================= */
        echo json_encode([
            'success' => true,
            'aide_id'        => $aide['aide_id'],
            'nom'            => $aide['nom'],
            'prenom'         => $aide['prenom'],
            'email'          => $aide['email'],
            'telephone_num'  => $aide['telephone_num'],
            'type_aide_id'   => $aide['type_aide_id'],
            'type_libelle'   => $aide['type_aide'], // jointure type_aide
            'sujet'          => $aide['sujet'],
            'message'        => $aide['message'],
            'date_demande'   => $aide['date_demande']
        ]);

    } catch (Exception $e) {

        echo json_encode([
            'success' => false,
            'error'   => 'Erreur serveur'
        ]);
    }

    exit;

?>