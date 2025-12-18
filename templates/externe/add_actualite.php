<?php

/* ============================================================================
   📌 CONTROLLER – AJOUT D’UNE ACTUALITÉ
   - Appelé via AJAX : /?dest=add_actualite
   - Les données arrivent en JSON
   - La date de dépôt est générée côté base (CURDATE)
============================================================================ */

    // Connexion base de données + classe EEA_Database
    include_once "require_db.php";

    // Lecture du payload JSON envoyé par fetch()
    $data = json_decode(file_get_contents("php://input"), true);

    // Sécurité minimale
    if (
        empty($data['titre_actu']) ||
        empty($data['desc_actu'])
    ) {
        echo json_encode([
            'success' => false,
            'message' => 'Champs obligatoires manquants'
        ]);
        exit;
    }

    try {
        // Appel méthode Database
        $success = EEA_Database::addActualite([
            'titre_actu' => trim($data['titre_actu']),
            'lien_actu'  => trim($data['lien_actu'] ?? ''),
            'desc_actu'  => trim($data['desc_actu'])
        ]);

        echo json_encode([
            'success' => $success
        ]);

    } catch (Exception $e) {

        // Log serveur possible ici
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
    }

    exit;
?>