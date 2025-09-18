<?php
        
        require_once "require_db.php"; // ton fichier de connexion PDO/MySQLi

        
        if ($_SERVER["REQUEST_METHOD"] === "POST") 
        {
                $titre       = trim($_POST['titre_offre'] ?? '');
                $linkedin    = trim($_POST['linkedin'] ?? '');
                $description = trim($_POST['description'] ?? '');
                $specialites = $_POST['specialites'] ?? [];

                // Nettoyage
                $titre       = htmlspecialchars($titre, ENT_QUOTES, 'UTF-8');
                $linkedin    = htmlspecialchars($linkedin, ENT_QUOTES, 'UTF-8');
                $description = htmlspecialchars($description, ENT_QUOTES, 'UTF-8');

                $id_comb = $_GET["id_user"]; 
                list($id_member , $id_num ) = explode("_" , $id_comb ); 


                $mail = EEA_Database::fetc_user_id($id_member)["email"];
                $date_now =  (new DateTime())->format('Y-m-d H:i:s');
                // Préparer données pour addOffre
                $data = [
                        'titre_offre' => $titre,
                        'linkedin' => $linkedin,
                        'description' => $description,
                        'email'=>$mail, 
                        'date_creation'=>$date_now
                ];

                
                try 
                {
                        if (EEA_Database::addJob($data, $specialites)) 
                        {
                               
                                http_response_code(200);
                                echo json_encode([
                                        "status"       => "success",
                                        "mail"         => $mail,
                                        "dateDepot"    => $date_now
                                ]);
                        } 
                        else 
                        {
                                http_response_code(500);
                                echo json_encode(["status" => "error", "message" => "Échec de l'ajout"]);
                        }
                } catch (Exception $e) {
                                http_response_code(500);
                                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
                        }
        }
        
?>
