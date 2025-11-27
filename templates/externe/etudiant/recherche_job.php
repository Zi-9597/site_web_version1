<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dépot offre - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="public/css/recherche_job.css">
    <link rel="stylesheet" href="public/css/style_carte.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
   
    <script src="public/js/gestion_slidebar_1.js"></script>
    <?php
    // Simple test to display "ancien" on the page
        require_once "require_db.php";


        $id_comb = $_GET["id_user"]; 
        list($id_member , $id_num ) = explode("_" , $id_comb ); 
    

        $found = EEA_Database::fetc_user_id($id_member);

    

        $nom_prenom = $found["prenom"]." ".$found["nom"];

        include "commun/barre_navigation_conn.php"
    ?>

    <div class="container-formulaire">
        <!-- Bandeau violet -->
         <!-- Résultats -->
        
        <div class="descritpion-evenement">
            <div class="titre_h1">
                <!-- Logo + titre -->
                
                 <h1>🤝📝 Formulaire de recherche d’offre </h1>

            </div>
           <div class="descirption-courte">
                <p>
                    Toutes les offres de <strong>jobs étudiants</strong> sont affichées directement ci-dessous.
                </p>
                <p>
                    Vous pouvez parcourir librement <strong>toutes les opportunités disponibles</strong>.
                </p>
            </div>
        </div>
    </div>


    <div class="container-resultats">
        <h1>📋 Résultats de la recherche</h1>
        <div id="resultats">
            <!-- Les cartes AJAX vont s'afficher ici -->
        </div>
    </div>

    



    <script src="public/js/recherche_job_etudiant.js"></script>

    <?php require 'commun/footer.php';?>

</body>
</html>