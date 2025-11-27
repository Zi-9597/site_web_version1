<?php
    require_once "require_db.php"; 
    header("Content-Type: application/json; charset=UTF-8");

    try {

        $sql = "SELECT * FROM evenements WHERE 1=1";
        $params = [];

        if (!empty($_POST["nom_event"])) {
            $sql .= " AND nom_event LIKE :nom";
            $params[":nom"] = "%" . $_POST["nom_event"] . "%";
        }
        if (!empty($_POST["date_event"])) {
            $sql .= " AND date_event = :date_event";
            $params[":date_event"] = $_POST["date_event"];
        }
        
        $fetch_array = EEA_Database::fetc_event($sql , $params);

        echo json_encode($fetch_array);
        } 
        catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
?>



   