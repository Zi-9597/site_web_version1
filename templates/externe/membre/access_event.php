
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participer à un évenement - Association EEA</title>
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
</head>
<body>
    
   
    
    <?php
    // Simple test to display "ancien" on the page
        require_once "require_db.php";

        // 🔴 Récupération des events
        $events = EEA_Database::fetch_events();

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
            if ($found["membre_assoc"] == "Étudiant/e")
            {
                include "commun/barre_conn_etu.php";
            }
            elseif ($found["membre_assoc"] == "Alumni/e")
            { 
                include "commun/barre_conn_ancien.php";
            }
        }
    ?>
    
   
         <!-- ================= TITRE ================= -->
    <div class="title-box">
        <h1>Participer aux Événements</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Retrouvez tous les événements organisés par l’association.
            Cliquez sur le bouton pour consulter les détails ou accéder au lien
            d’inscription associé.
        </p>
    </div>

    <!-- ================= FILTRE ================= -->
    <div class="box-with-title">
        <span class="box-title">Filtre des événements</span>

        <div class="filtre-membre">
            <span class="titre-fm">Filtre Événement</span>

            <div class="filtre-membre-grid">
                <div class="filter-item">
                    <label for="search-nom-event">Nom de l’événement :</label>
                    <input
                        type="text"
                        id="search-nom-event"
                        placeholder="Rechercher un événement par nom..."
                    >
                </div>
            </div>
        </div>
    </div>


   <!-- ================= TABLEAU ================= -->
    <div class="total_information">

        <table id="table-events">
            <thead>
                <tr>
                    <th>Nom de l’événement</th>
                    <th>Date</th>
                    <th class="col-modifier">Consulter</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($events as $e): ?>
                <tr>
                    <td><?= htmlspecialchars($e["nom_event"]) ?></td>
                    <td><?= date("d/m/Y", strtotime($e["date_event"])) ?></td>

                    <td>
                        <button class="btn-change"
                            data-id="<?= htmlspecialchars($e["id_event"]) ?>">
                            👁️ Voir
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div id="no-result" class="no-result">
            Aucun événement trouvé
        </div>
    </div>
   <!-- ================= MODAL AFFICHER GOODIES ================= -->
    <div id="modal-edit-event" class="modal-overlay">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Détails de l’événement</h2>
            </div>

            <div class="modal-body">

                <div class="modal-field">
                    <label>Nom de l’événement</label>
                    <input type="text" id="edit-nom-event" disabled>
                </div>

                <div class="modal-field">
                    <label>Date</label>
                    <input type="date" id="edit-date-event" disabled>
                </div>

                <div class="modal-field">
                    <label>Description</label>
                    <textarea
                        id="edit-desc-event"
                        rows="6"
                        disabled>
                    </textarea>
                </div>

                <div class="modal-field">
                    <label>Lien d’inscription</label>
                    <input type="text" id="edit-url-form" disabled>
                    <small style="color:#666;">
                        🔗 Redirection vers le formulaire ou la plateforme associée
                    </small>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel">Fermer</button>
            </div>

        </div>
    </div>



    <?php require 'commun/footer.php';?>
    <script src="public/js/new_event_fetch.js"></script>
    <script src="public/js/gestion_slide_bar_4.js"></script>

</body>
</html>