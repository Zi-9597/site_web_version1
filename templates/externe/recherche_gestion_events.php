
<?php


    require_once "require_db.php";
    $id_event = isset($_GET["id_event"]) ? intval($_GET["id_event"]) : null;

    if (!$id_event) {
        echo json_encode([
            "success" => false,
            "message" => "ID événement manquant."
        ]);
        exit;
    }

    try {
        // Récupération de l'événement (unique)
        $result = EEA_Database::fetch_events(null, $id_event);

        if (!$result || count($result) === 0) {
            echo json_encode([
                "success" => false,
                "message" => "Aucun événement trouvé."
            ]);
            exit;
        }

        // ⚠️ id_event est UNIQUE → on renvoie DIRECTEMENT la ligne
        echo json_encode($result[0]);

    } catch (Exception $e) {
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur : " . $e->getMessage()
        ]);
    }

    exit;


?>