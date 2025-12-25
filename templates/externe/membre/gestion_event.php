<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des évènments - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/index.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=20251225_3">
    <link rel="stylesheet" href="public/css/change_statut.css">
    <link rel="stylesheet" href="public/css/modal.css">
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

        $email = $found["email"];

        if($found["membre_bureau"] === "Président")
        {
            include "commun/barre_navigation_pres.php";
        }
        else
        {
            include "commun/barre_navigation_conn.php";
        }

        $events = EEA_Database::fetch_events($id_member);

        
    ?>

    
       <!-- Titre de la page -->
    <div class="title-box">
        <h1>Gestion des Offres</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Retrouvez toutes vos offres déposées et utilisez les actions disponibles pour 
            les modifier, les mettre à jour ou les supprimer si nécessaire.
        </p>
    </div>

    
    <!-- Bloc des filtres -->
   <!-- ===== BLOC GLOBAL : FILTRES + ACTION ===== -->
    <div class="box-with-title">
        <span class="box-title">Filtre des offres</span>

        <!-- ==== PARTIE FILTRE ==== -->
        <div class="filtre-membre">
            <span class="titre-fm">Filtre Offre</span>

            <div class="filtre-membre-grid">

                <!-- Prénom -->
                <div class="filter-item">
                    <label for="search-titre">Nom de l'évènement :</label>
                    <input type="text" id="search-titre-offre" placeholder="Rechercher le nom de l'évènement...">
                </div>


            </div>
        </div>
    </div>

    <!-- Début du tableau -->
    <div class="total_information">

        <table id="table-offres">

            <!-- En-tête du tableau -->
            <thead>
                <tr>
                    <th>Nom de l'évènement</th>
                    <th>Date de l'évènement </th>

                    <th class="col-modifier">Modifier</th>
                    <th class="col-supprimer">Supprimer</th>
                    
                    <!-- Colonne changement (cachée par défaut) -->
                </tr>
            </thead>

            <!-- Corps du tableau -->
            <tbody>

                <!-- Boucle pour afficher chaque membre dans une ligne -->
                <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= htmlspecialchars($event["nom_event"]) ?></td>

                            <td><?= date("d/m/Y" , strtotime($event["date_event"]))?></td>

                            <!-- Bouton Modifier -->
                            <td>
                                <button class="btn-change"
                                    data-id="<?= htmlspecialchars($event['id_event']) ?>">
                                    ✏️ Modifier
                                </button>
                            </td>

                            <!-- Bouton Supprimer -->
                            <td>
                                <button class="btn-delete" 
                                    data-id="<?= htmlspecialchars($event['id_event']) ?>">
                                    🗑️ Supprimer
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
            </tbody>

        </table>
        <div id="no-result" class="no-result">
            Aucun résultat trouvé
        </div>


    </div>
     <!-- ============================
        🟣 MODAL : MODIFIER UN ÉVÈNEMENT
============================== -->
    <div id="modal-edit-event" class="modal-overlay">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Informations de l'évènement</h2>
            </div>

            <div class="modal-body">

                <div class="modal-field">
                    <label>Nom :</label>
                    <input type="text" id="edit-nom-event">
                </div>

                <div class="modal-field">
                    <label>Date de l'évènement :</label>
                    <input type="date" id="edit-date-event">
                </div>

                <div class="modal-field">
                    <label>Description :</label>
                    <textarea id="edit-desc-event" rows="5"></textarea>
                </div>

                <div class="modal-field">
                    <label>Lien du formulaire :</label>
                    <input type="text" id="edit-url-form">
                </div>

                <div class="modal-field">
                    <label>Date de création :</label>
                    <input type="date" id="edit-date-creation" readonly disabled>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel" >Annuler</button>
                <button class="modal-btn-save" id="btn-save-offre">Valider</button>
            </div>

        </div>
    </div>
    <!-- Carte SUCCESS -->
    <div id="card-success" class="notif-card success">
        ✔️ Offre mise à jour avec succès !
    </div>

    <!-- Carte ERROR -->
    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue lors de la mise à jour.
    </div>


    <?php require 'commun/footer.php';?>
    <script src="public/js/gestion_event_eea.js"></script>
    <script src="public/js/gestion_slide_bar_4.js"></script>

</body>
</html>