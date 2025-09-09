<?php


    require_once "require_db.php";

    // On vérifie que le formulaire a bien été envoyé
    if ($_SERVER["REQUEST_METHOD"] === "POST")     
    {
        // On récupère et nettoie les données
        $mail      = trim($_POST['email'] ?? '');
        $hashed_password = trim($_POST['password'] ?? '');

        $test_fetch = EEA_Database::fetc_user_mail($mail); 
        
        if(password_verify($hashed_password , $test_fetch["mot_de_passe"]))
        {
            $id_membre = $test_fetch["id_membre"] ?? null;
            $id_num = $test_fetch["id"] ?? null;
            $combinedId = $id_membre . "_" . $id_num;
            header("Location: /?dest=acceuil&id_user=" . $combinedId);
            exit;
        }
        else
        {
            var_dump("farakh");
        }




    }

?>

