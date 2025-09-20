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
    <link rel="stylesheet" href="public/css/depot_offre.css">
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
        <div class="descritpion-evenement">
            <div class="titre_h1">
                <!-- Logo + titre -->
                
                 <h1>💼 Dépôt d’offre</h1>
            </div>
            <div class="descirption-courte">
                <p>
                    Remplissez ce formulaire pour partager vos opportunités de 
                    <strong>stage</strong>, <strong>alternance</strong> ou <strong>emploi</strong> 
                    avec les étudiants et diplômés de l’EEA.
                </p>
            </div>
        </div>

        <!-- Formulaire -->
        <form id="formulaire-offre">
            
            <!-- Titre offre -->
            <div class="formulaire-element">
                <i class="bi-briefcase-fill"></i>
                <div>
                    <label for="titre-offre">Titre de l’offre</label>
                    <input type="text" id="titre-offre" name="titre_offre" placeholder="Ex : Alternance en informatique" required>
                </div>
            </div>

            <!-- Spécialités -->
            <div class="formulaire-element">
                <i class="bi-list-check"></i>
                <div>
                    <label>Spécialités recherchées</label>
                    <div class="specialites-grid">
                        <label>
                            <input type="checkbox" name="specialites[]" value="1">
                            <span>Électronique</span>
                        </label>
                        <label>
                            <input type="checkbox" name="specialites[]" value="2">
                            <span>Informatique </span>
                        </label>
                        
                        <label>
                            <input type="checkbox" name="specialites[]" value="3">
                            <span>Télécom / Système communicants</span>
                        </label>
                        <label>
                            <input type="checkbox" name="specialites[]" value="4">
                            <span>Énergie Électrique</span>
                        </label>
                        <label>
                            <input type="checkbox" name="specialites[]" value="5">
                            <span>Automatique / Automatisme</span>
                        </label>
                        <label>
                            <input type="checkbox" name="specialites[]" value="6">
                            <span>Transports </span>
                        </label>
                    </div>
                </div>
            </div>
            <!-- Lieu et Département -->
            <div class="formulaire-element">
                <i class="bi-geo-alt-fill"></i>
                <div id="div_localisation">
                    <!-- Code postal -->
                    <label for="departement">Code postal</label>
                    <input type="text" id="departement" name="departement" placeholder="Ex : 75000" maxlength="5" required>
                    <p class="form_code">Le code saisi est incorrect.</p>

                    <!-- Résultats affichés juste en dessous -->
                    <div id="infos_cp">
                        
                        <!-- Commune -->
                        <div style="width:  40%;">
                            <label for="commune">Commune</label>
                            <input type="text" id="commune" name="commune" placeholder="Commune" readonly >
                        </div>

                        <!-- Département -->
                        <div>
                            <label for="departement_nom">Département</label>
                            <input type="text" id="departement_nom" name="departement_nom" placeholder="Département" readonly>
                        </div>
                        <!-- Région -->
                        <div>
                            <label for="region">Région</label>
                            <input type="text" id="region" name="region" placeholder="Région" readonly>
                        </div>

                    </div>
                </div>
            </div>

             <!-- Lien LinkedIn -->
            <div class="formulaire-element">
                <i class="bi-linkedin"></i>
                <div id="link_id">
                    <label for="linkedin">Lien LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" placeholder="https://www.linkedin.com/in/votreprofil">
                    <p class="form_link">Le lien de l'offre Linkedin n'est pas valide.</p>
                </div>
            </div>



            <!-- Description -->
            <div class="formulaire-element" id="desc-evenmt">
                <i class="bi-file-text-fill"></i>
                <div id="element-desc">
                    <label for="description">Description de l’offre</label>
                    <textarea id="description" name="description" rows="5" placeholder="Détails sur l’offre (missions, durée, conditions...)" required></textarea>
                </div>
                 <p id="char-count">0 / 500 caractères</p>
            </div>

            <!-- Bouton -->
            <div class="button_submit">
                <button type="submit" id="button_submit" disabled>Publier l’offre</button>
            </div>
        </form>

        <div id="block_valid"></div>
    </div>




    <script src="public/js/depot_offre.js"></script>

    <?php require 'commun/footer.php';?>

</body>
</html>