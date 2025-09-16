<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajout Évenements - Association EEA</title>
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
        <form id="formulaire-offre" method="POST" action="traitement_offre.php">
            
            <!-- Titre offre -->
            <div class="formulaire-element">
                <i class="bi-briefcase-fill"></i>
                <div>
                    <label for="titre-offre">Titre de l’offre</label>
                    <input type="text" id="titre-offre" name="titre_offre" placeholder="Ex : Alternance en informatique" required>
                </div>
            </div>

            <!-- Section (multi-choix) -->
            <div class="formulaire-element" id="membre-section">
                <i class="bi-mortarboard-fill"></i>
                <div id="element-section">
                    <label for="filiere-section">Section (choix multiples possibles)</label>
                    <select id="filiere-section" name="sections[]" multiple required>
                        <option value="Licence-3">Licence 3</option>
                        <optgroup label="Masters">
                            <option value="Master-RT">Master RT</option>
                            <option value="Master-SysCom">Master SysCom</option>
                            <option value="Master-NN">Master Nano-Technologie</option>
                            <option value="Master-GI">Master Génie Industriel</option>
                            <option value="Master-SE">Master SE</option>
                            <option value="Master-SA">Master SA</option>
                            <option value="Master-SMaRT">Master SMaRT</option>
                            <option value="Master-GR2E">Master GR2E</option>
                            <option value="Master-E2SD">Master E2SD</option>
                        </optgroup>
                    </select>
                    <small class="aide-multiple">Maintenez <b>Ctrl</b> (Windows) ou <b>Cmd</b> (Mac) pour sélectionner plusieurs sections</small>
                </div>
            </div>

            <!-- Spécialités -->
            <div class="formulaire-element">
                <i class="bi-list-check"></i>
                <div>
                    <label>Spécialités recherchées</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="specialites[]" value="Electronique"> Électronique</label>
                        <label><input type="checkbox" name="specialites[]" value="Informatique"> Informatique</label>
                        <label><input type="checkbox" name="specialites[]" value="Télécom"> Télécom</label>
                        <label><input type="checkbox" name="specialites[]" value="Energie"> Énergie</label>
                        <label><input type="checkbox" name="specialites[]" value="Automatique"> Automatique</label>
                    </div>
                </div>
            </div>

            <!-- Lien LinkedIn -->
            <div class="formulaire-element">
                <i class="bi-linkedin"></i>
                <div id="link_id">
                    <label for="linkedin">Lien LinkedIn</label>
                    <input type="url" id="linkedin" name="linkedin" placeholder="https://www.linkedin.com/in/votreprofil">
                </div>
            </div>

            <!-- Description -->
            <div class="formulaire-element" id="desc-evenmt">
                <i class="bi-file-text-fill"></i>
                <div id="element-desc">
                    <label for="description">Description de l’offre</label>
                    <textarea id="description" name="description" rows="5" placeholder="Détails sur l’offre (missions, durée, conditions...)" required></textarea>
                </div>
            </div>

            <!-- Bouton -->
            <div class="button_submit">
                <button type="submit" id="button_submit">Publier l’offre</button>
            </div>
        </form>
    </div>




    <script src="public/js/update_add_event.js"></script>

    <?php require 'commun/footer.php';?>

</body>
</html>