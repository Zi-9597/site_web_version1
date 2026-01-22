<?php


    require_once "require_db.php";
    include_once 'commun/uuid_v4.php';
    require_once "mail_class.php";
    $uuid = uuid7(); 

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
        $phone      = trim($_POST['phone_e164'] ?? '');
        $pays       = trim($_POST['city'] ?? '');
        $ville      = trim($_POST['country'] ?? '');
        $profession = trim($_POST['profession'] ?? '');
        $hashed_password = password_hash($password , PASSWORD_DEFAULT);

    

        if ($section === "Autre")
        {
            $section = $autre_fil;
        }

        // Parse with expected format
        $d = DateTime::createFromFormat('d/m/Y', $date );
        $date_naissance = $d->format('Y-m-d');

      
        
        $today = date_create("now")->format('Y-m-d H:i:s');

 

        // Génération du token de confirmation
        $confirmation_token = bin2hex(random_bytes(32));

        $data = [
            'id_membre'        => $uuid,
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
            'genre'            => $civil,
            // 🔐 Confirmation
            'confirmation_token'  => $confirmation_token,
            'is_validated'        => 0
        ];

   


        if(EEA_Database::addSubscriber($data))
        {
            $mailer = EEA_Mailer::getInstance(); 
            $mailer->sendWelcome($email , $prenom , $nom ,$confirmation_token);
            header("Location: /?dest=success");
            exit;
        }
        else
        {
            header("Location: /?erreur_inscription");
            exit;
        }
    }

?>

