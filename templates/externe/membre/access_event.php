<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherhce Évenements - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="public/css/evenement_add.css">
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
        
        <!-- Bandeau titre + description -->
        <div class="descritpion-evenement">
            <div class="titre_h1">
                <h1>🔎 Recherche d'événements</h1>
            </div>
            <div class="descirption-courte">
                <p>Ce formulaire vous permet de <strong>rechercher des événements</strong> organisés par l’association.</p>
                <p>Tous les champs sont <strong>facultatifs</strong> : si vous laissez le formulaire vide, la recherche affichera <strong>tous les événements disponibles</strong>.</p>
            </div>
        </div>

        <!-- Formulaire -->
        <form id="formulaire-evenement">
            
            <!-- Nom de l'événement -->
            <div class="formulaire-element">
                <i class="fa fa-calendar"></i>
                <div>
                    <label for="nom-event">Nom de l'événement</label>
                    <input type="text" id="nom-event" name="nom_event" placeholder="Ex: Tournoi de football">
                </div>
            </div>

            <!-- Date de l'événement -->
                <div class="formulaire-element" id="event-date">
                    <div class="form-element" id="naissance">
                        <i class="fa fa-clock"></i> <!-- Icône horloge -->
                        <div id="element-date">
                            <label for="date_event">Date de l'événement</label>
                            <input type="text" id="evenmt_date" placeholder="Date de l'événement" name="date">
                        </div>
                    </div>
                    <p class="form_annee" id="forme_id_event" style="display: none;">Format de l'année de naissance : jj/mm/aaaa</p>
                </div>
            <!-- Lieu -->
            <div class="formulaire-element">
                <i class="fa fa-map-marker-alt"></i>
                <div>
                    <label for="lieu-event">Lieu</label>
                    <input type="text" id="lieu-event" name="lieu_event" placeholder="Ex: Parc du Héron, Local P4, ...">
                </div>
            </div>


            <!-- Catégorie -->
            <div class="formulaire-element">
                <i class="fa fa-tags"></i>
                <div id="select_categories">
                    <label for="categorie">Catégorie</label>
                    <select id="categorie" name="categorie">
                        <option value="" selected disabled hidden>Sélectionner</option>
                        <option value="Sport">Sport</option>
                        <option value="Culture">Culture</option>
                        <option value="Formation">Formation</option>
                        <option value="Networking">Networking</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
            </div>

            <!-- Bouton -->
            <div class="button_submit">
                <button type="submit" id="button_submit">Rechercher l'événement</button>
            </div>
        
        </form>
       <!-- Résultats AJAX -->
        
   
      
    </div>
    <!-- Résultats -->
    <div class="container-resultats">
        <h2>📋 Résultats de la recherche</h2>
        <div id="resultats">
            <!-- Les cartes AJAX vont s'afficher ici -->
        </div>
    </div>


    <script src="public/js/recherche_evenement.js"></script>
    <?php require 'commun/footer.php';?>

</body>
</html>