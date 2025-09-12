<?php
        
        require_once "require_db.php"; // ton fichier de connexion PDO/MySQLi

        var_dump("farakh");
        if ($_SERVER["REQUEST_METHOD"] === "POST") 
        {

            

            // Récupération des champs
            $nom_event   = trim($_POST["nom_event"]);
            $date_event  = trim($_POST["date"]);
            $lieu_event  = trim($_POST["lieu_event"]);
            $desc_event  = trim($_POST["desc_event"]);
            $url_event   = trim($_POST["url_form"]);
            $categorie   = trim($_POST["categorie"]);
    

            // Récupération du membre connecté (par ex. depuis la session ou l’URL)
            // Ici on suppose que tu as mis l'id dans $_GET["id_user"] comme dans ta page
            list($id_membre, $id_num) = explode("_", $_GET["id_user"]);

        

            // Parse with expected format
            $d = DateTime::createFromFormat('d/m/Y', $date_event );

            if ($d) {
                $date_event = $d->format('Y-m-d 00:00:00');
            
            } else {
                var_dump(DateTime::getLastErrors());
            }


            // Date de création (aujourd’hui)
            $today = date_create("now")->format('Y-m-d H:i:s');

            // Tableau associatif prêt à être injecté
            $data = [
                ":nom_event"     => $nom_event,
                ":date_event"    => $date_event,
                ":lieu_event"    => $lieu_event,
                ":desc_event"    => $desc_event,
                ":categorie"     => $categorie,
                ":id_membre"     => $id_membre,   // ou $id_combi si tu veux garder la forme "12_45"
                ":url_form"      => $url_event,
                ":date_creation" => $today
            ];

            if(EEA_Database::addEvent($data))
            {
                http_response_code(200); 
            }
            else
            {
                http_response_code(500); 
            }


        }

?>
