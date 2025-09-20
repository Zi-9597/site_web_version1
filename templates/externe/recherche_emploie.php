<?php
    header("Content-Type: application/json; charset=UTF-8");
    require_once "require_db.php";

    try {
        // Récupérer les données envoyées par le formulaire
        $filters = $_POST;

        // Appel de la méthode de recherche
        $jobs = EEA_Database::searchJobs($filters);

        // Réponse JSON
        echo json_encode([
            "status" => true,
            "count"  => count($jobs),
            "jobs"   => $jobs
        ]);
    } catch (Exception $e) {
        echo json_encode([
            "status"  => false,
            "message" => "Erreur lors de la recherche : " . $e->getMessage()
        ]);
    }

?>