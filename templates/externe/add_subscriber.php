<?php


    require_once "require_db.php";

    // On vérifie que le formulaire a bien été envoyé
    if ($_SERVER["REQUEST_METHOD"] === "POST")     
    {
        // On récupère et nettoie les données
        $civil      = trim($_POST['civil'] ?? '');
        $nom        = trim($_POST['nom'] ?? '');
        $prenom     = trim($_POST['prenom'] ?? '');
        $date       = trim($_POST['date'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $password   = trim($_POST['password'] ?? '');
        $membre     = trim($_POST['membre_assoc'] ?? '');
        $autre_fil = trim($_POST['autre_fil'] ?? '');
        $section    = trim($_POST['section'] ?? '');
        $phone      = trim($_POST['phone'] ?? '');
        $pays       = trim($_POST['city'] ?? '');
        $ville      = trim($_POST['country'] ?? '');
        $profession = trim($_POST['profession'] ?? '');
        $id_insc =    trim($_POST["id_insc"] ?? '');
        $hashed_password = password_hash($password , PASSWORD_DEFAULT);

    

        if ($section === "Autre")
        {
            $section = $autre_fil;
        }

        // Parse with expected format
        $d = DateTime::createFromFormat('d/m/Y', $date );

        if ($d) {
            $date_naissance = $d->format('Y-m-d');
            var_dump($date_naissance);
        } else {
            var_dump(DateTime::getLastErrors());
        }

        
        $today = date_create("now")->format('Y-m-d H:i:s');
        var_dump($today);



        $data = [
            'id_membre'        => $id_insc,
            'prenom'           => $prenom,
            'nom'              => $nom ,
            'section'          => $section,
            'membre_assoc'     => $membre,
            'membre_bureau'    => false,
            'email'            => $email,
            'phone_number'     => $phone ,
            'mot_de_passe'     => $hashed_password,
            'date_naissance'   => isset($date_naissance) ? $date_naissance : null,
            'date_inscription' => $today,
            'pays'             => $pays,
            'ville'            => $ville ,
            'metier'           => $profession,
            'genre'            => $civil
        ];

   


        if(EEA_Database::addSubscriber($data))
        {
            header("Location: /?dest=success");
            exit;
        }
        else
        {
            header("Location: /error.php");
            exit;
        }
    }

?>

