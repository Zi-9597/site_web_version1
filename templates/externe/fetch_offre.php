<?php


require_once "require_db.php";

if (!isset($_GET['id_user'])) {
    echo json_encode(["error" => "Missing ID"]);
    exit;
}

$id = $_GET['id_user'];

$pdo = EEA_Database::getInstance();

$sql = "
    SELECT 
        o.id_offre,
        o.titre_offre,
        o.url_linkedin,
        o.description,
        o.email_user,
        o.type_contrat,
        o.date_creation,
        GROUP_CONCAT(s.nom_specialite SEPARATOR ',') AS specialites
    FROM offres o
    JOIN offre_specialite os ON o.id_offre = os.id_offre
    JOIN specialites s ON os.id_specialite = s.id_specialite
    WHERE o.id_offre = :id
    GROUP BY o.id_offre
";

$stmt = $pdo->prepare($sql);
$stmt->execute(["id" => $id]);

$result = $stmt->fetch();

echo json_encode($result);