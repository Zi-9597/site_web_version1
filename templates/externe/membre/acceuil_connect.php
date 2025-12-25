
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Se Connecter- Ancien EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=20251225_2">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="../../public/css/index.css?v=20251225_2">
    <link rel="stylesheet" href="../../public/css/logo_gestion.css">
    <link rel="stylesheet" href="../../public/css/presentation_acceuil.css?v=20251225_2">
    <link rel="stylesheet" href="../../public/css/footer.css?v=20251225_3">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
</head>

<body>
    
    <?php
    // Simple test to display "ancien" on the page
        require_once "require_db.php";


        $id_comb = $_GET["id_user"]; 
        list($id_member , $id_num ) = explode("_" , $id_comb ); 
    

        $found = EEA_Database::fetc_user_id($id_member);



        $nom_prenom = $found["prenom"]." ".$found["nom"];
        if (!empty($found["membre_bureau"]))
        {
            if($found["membre_bureau"] === "Président")
            {
                include "commun/barre_navigation_pres.php";
            }
            else
            {
                include "commun/barre_navigation_conn.php";
            }
           
        }
        else
        {
            if ($found["membre_assoc"] === "Étudiant/e")
            {
                include "commun/barre_conn_etu.php";
            }
            elseif ($found["membre_assoc"] == "Alumni/e")
            { 
                include "commun/barre_conn_ancien.php";
            }
        }
    ?>

    <?php require_once 'commun/acceuil_pres.php'; ?>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>
    <script src="public/js/acceuil_page.js"></script>


    <?php require 'commun/footer.php';?>

</body>


