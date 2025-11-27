<?php
require_once "require_db.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$id_user = $data["id_user"];
$field   = $data["field"];
$value   = $data["value"];

// Mapping HTML -> BDD
$allowed = [
    "mail-input" => "email",
    "mdp-inp" => "password",
    "membre-assoc" => "membre_assoc",
    "filiere-section" => "section",
    "phone" => "phone_number",
    "city-input" => "ville",
    "profession-input" => "metier"
];

if (!isset($allowed[$field])) {
    echo json_encode(["success" => false, "message" => "Champ non autorisé"]);
    exit;
}

$db_field = $allowed[$field];

// Hash mot de passe si besoin
if ($field === "mdp-inp") {
    $value = password_hash($value, PASSWORD_DEFAULT);
}

// Mise à jour BDD
$result = EEA_Database::update_user_info($id_user, $db_field, $value);

echo json_encode(["success" => $result]);