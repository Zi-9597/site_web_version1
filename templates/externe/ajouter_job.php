<?php

// Inclusion du fichier de connexion à la base de données
require_once "require_db.php"; 

// Vérifie que la requête est bien envoyée en POST
if ($_SERVER["REQUEST_METHOD"] === "POST") 
{
    // -----------------------------
    // 🔹 Récupération des données du formulaire
    // -----------------------------
    $titre       = trim($_POST['titre_offre'] ?? '');
    $linkedin    = trim($_POST['linkedin'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Si aucune spécialité n’est envoyée → on met l’ID 7 (Job Étudiant)
    $specialites = !empty($_POST['specialites']) ? $_POST['specialites'] : array('7');

    // Si aucun type de contrat → par défaut "Job Étudiant"
    $type_contrat = !empty($_POST['types']) ? trim($_POST['types']) : 'Job Étudiant';

    // -----------------------------
    // 🔹 Sécurisation des données (anti XSS)
    // -----------------------------
    $titre        = htmlspecialchars($titre, ENT_QUOTES, 'UTF-8');
    $linkedin     = htmlspecialchars($linkedin, ENT_QUOTES, 'UTF-8');
    $description  = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');
    $type_contrat = htmlspecialchars($type_contrat, ENT_QUOTES, 'UTF-8');

    // -----------------------------
    // 🔹 Récupération de l’ID utilisateur depuis l’URL
    // -----------------------------
    $id_comb = $_GET["id_user"]; 
    list($id_member, $id_num) = explode("_", $id_comb); 

    // -----------------------------
    // 🔹 Récupération de l’email de l’utilisateur
    // -----------------------------
    $mail = EEA_Database::fetc_user_id($id_member)["email"];

    // Date actuelle de création
    $date_now = (new DateTime())->format('Y-m-d H:i:s');

    // -----------------------------
    // 🔹 Données envoyées à la fonction addJob()
    // -----------------------------
    $data = [
        'titre_offre'   => $titre,
        'linkedin'      => $linkedin,
        'description'   => $description,
        'email'         => $mail, 
        'type_contrat'  => $type_contrat,
        'date_creation'=> $date_now
    ];

    try 
    {
        // Tentative d’ajout du job en base de données
        if (EEA_Database::addJob($data, $specialites)) 
        {
            // ✅ Succès
            http_response_code(200);

            echo json_encode([
                "status"       => "success",
                "mail"         => $mail,
                "dateDepot"    => $date_now,
                "type_contrat" => $type_contrat
            ]);
        } 
        else 
        {
            // ❌ Erreur d’insertion
            http_response_code(500);
            echo json_encode([
                "status"  => "error", 
                "message" => "Échec de l'ajout"
            ]);
        }
    } 
    catch (Exception $e) 
    {
        // ❌ Erreur serveur
        http_response_code(500);
        echo json_encode([
            "status"  => "error", 
            "message" => $e->getMessage()
        ]);
    }
}

?>
