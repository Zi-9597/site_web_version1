<?php

/* ============================================================================
   📌 CONTROLLER – RÉCUPÉRATION D’UNE ACTUALITÉ
   - Appelé via AJAX : /?dest=get_actualite&id_actu=XX
   - Utilise fetch_actualites(?int $actu_id)
   - Retourne les données en JSON
============================================================================ */



    // Chargement de la base et de la classe EEA_Database
    include_once "require_db.php";

    // Sécurisation de l’ID
    $actuId = isset($_GET['id_actu']) ? (int) $_GET['id_actu'] : null;

    if ($actuId === null || $actuId <= 0) {
        echo json_encode([
            'success' => false,
            'message' => 'ID actualité invalide'
        ]);
        exit;
    }

    try {
        // Appel de la méthode générique
        $result = EEA_Database::fetch_actualites($actuId);

        // fetch_actualites retourne un tableau → on prend le premier élément
        $actualite = $result[0] ?? null;

        if ($actualite === null) {
            echo json_encode([
                'success' => false,
                'message' => 'Actualité introuvable'
            ]);
            exit;
        }

        // Succès : on retourne directement l’actualité
        echo json_encode($actualite);

    } catch (Exception $e) {

        // En production, éviter d’exposer le message exact
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
    }

    exit;
?>