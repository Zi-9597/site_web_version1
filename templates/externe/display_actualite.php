
<?php

    require_once "require_db.php";
    header('Content-Type: application/json');

    $actu_id = (int) $_GET['actu_id'];

    $result = EEA_Database::fetch_actualites($actu_id);

    echo json_encode($result[0] ?? []);
    exit;

?>