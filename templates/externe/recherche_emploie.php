<?php
header("Content-Type: application/json; charset=UTF-8");
require_once "require_db.php";

try {
    // ✅ 1) Priorité à $_POST s’il n’est pas vide
    if (!empty($_POST)) {
        $filters = $_POST;
    } 
    // ✅ 2) Sinon on récupère les données envoyées en JSON
    else {
        $raw = file_get_contents("php://input");
        $filters = json_decode($raw, true) ?? [];
    }

    // ✅ 3) Appel de la méthode de recherche
    $jobs = EEA_Database::searchJobs($filters);

    // ✅ 4) Réponse JSON de succès
    echo json_encode([
        "status" => true,
        "count"  => count($jobs),
        "jobs"   => $jobs
    ]);
} 
catch (Exception $e) {
    // ❌ Réponse JSON en cas d’erreur
    echo json_encode([
        "status"  => false,
        "message" => "Erreur lors de la recherche : " . $e->getMessage()
    ]);
}
?>
