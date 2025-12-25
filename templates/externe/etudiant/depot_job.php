<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dépot de job étudiant - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/index.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=20251225_3">
    <link rel="stylesheet" href="public/css/depot_offre.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
   
    
    <?php
    // Simple test to display "ancien" on the page
        require_once "require_db.php";


        $id_comb = $_GET["id_user"]; 
        list($id_member , $id_num ) = explode("_" , $id_comb ); 
    

        $found = EEA_Database::fetc_user_id($id_member);

    

        $nom_prenom = $found["prenom"]." ".$found["nom"];
        if(!empty($found["membre_bureau"]))
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
            include "commun/barre_conn_etu.php";
        }

        
    ?>


    <div class="container-formulaire">
        <!-- Bandeau violet -->
        <div class="descritpion-evenement">
            <div class="titre_h1">
                <h1>🧑‍🎓 Dépôt de job étudiant</h1>
            </div>
            <div class="descirption-courte">
                <p>
                    Publiez une offre de <strong>job étudiant</strong> pour les étudiants de l’EEA.
                </p>
            </div>
        </div>

        <!-- Formulaire -->
        <form id="formulaire-offre">
            
            <!-- Titre du job -->
            <div class="formulaire-element">
                <i class="bi-briefcase-fill"></i>
                <div>
                    <label for="titre-offre">Titre du job</label>
                    <input type="text" id="titre-offre" name="titre_offre" placeholder="Ex : Serveur week-end" required>
                </div>
            </div>

            <!-- Description -->
            <div class="formulaire-element" id="desc-evenmt">
                <i class="bi-file-text-fill"></i>
                <div id="element-desc">
                    <label for="description">Description du job</label>
                    <textarea id="description" name="description" rows="5" placeholder="Décris le travail proposé..." required></textarea>
                </div>
                <p id="char-count">0 / 500 caractères</p>
            </div>

            <!-- Bouton -->
            <div class="button_submit">
                <button type="submit" id="button_submit" disabled>Publier le job</button>
            </div>

        </form>

        <div id="block_valid"></div>
    </div>




    <script src="public/js/depot_offre.js"></script>
    <script src="public/js/gestion_slide_bar_4.js"></script>

    <?php require 'commun/footer.php';?>

</body>
</html>