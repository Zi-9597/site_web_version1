<?php require_once "commun/init.php"  ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des évènments - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/change_statut.css?v=<?= filemtime('public/css/change_statut.css') ?>">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
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
        /************************************************************
         *  PAGE : GESTION DES ÉVÉNEMENTS
         *  - init.php déjà inclus en première ligne
         *  - $user est valide ou null
         ************************************************************/

        /* =========================================================
        1️⃣ SÉCURITÉ : UTILISATEUR CONNECTÉ
        ========================================================= */

        if (!$user) {
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
        ========================================================= */

        if (empty($user['membre_bureau'])) {
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        3️⃣ BARRE DE NAVIGATION
        ========================================================= */

        if ($user['membre_bureau'] === "Président" || $user['membre_bureau'] === "Web Admin") {
            require "commun/barre_navigation_pres.php";
        } else {
            require "commun/barre_navigation_conn.php";
        }

        /* =========================================================
        4️⃣ RÉCUPÉRATION DES ÉVÉNEMENTS
        ========================================================= */

        $events = EEA_Database::fetch_events();

    ?>

    
       <!-- Titre de la page -->
    <div class="title-box">
        <h1>Gestion des événements</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Consultez les événements publiés, mettez-les à jour et suivez les inscriptions des membres.
        </p>
    </div>

    
    <!-- Bloc des filtres -->
   <!-- ===== BLOC GLOBAL : FILTRES + ACTION ===== -->
    <div class="box-with-title">
        <span class="box-title">Retrouver un événement</span>

        <!-- ==== PARTIE FILTRE ==== -->
        <div class="filtre-membre">
            <span class="titre-fm">Recherche</span>

            <div class="filtre-membre-grid">

                <!-- Prénom -->
                <div class="filter-item">
                    <label for="search-titre-offre">Nom de l'événement</label>
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
                    <th>Nom de l'événement</th>
                    <th>Date</th>
                    <th>Créé par</th>
                    <th class="col-modifier">Modifier</th>
                    <th class="col-modifier">Participants</th>
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

                            <td><?= htmlspecialchars($event['prenom'] . ' ' . $event['nom']) ?></td>
                            <!-- Bouton Modifier -->
                            <td>
                                <button class="btn-change" 
                                    data-id="<?= htmlspecialchars($event['id_event']) ?>">
                                    Modifier
                                </button>
                            </td>
                            <!-- Bouton Modifier -->
                            <td>
                                <button class="btn-participate" 
                                        data-id="<?= htmlspecialchars($event['id_event']) ?>">
                                        Participants
                                </button>
                            </td>

                            <!-- Bouton Supprimer -->
                            <td>
                                <button class="btn-delete" 
                                    data-id="<?= htmlspecialchars($event['id_event']) ?>">
                                    Supprimer
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
                    <label>Date de l'évènement (mois/jour/année):</label>
                    <input type="date" id="edit-date-event">
                </div>

                <div class="modal-field">
                    <label>Description :</label>
                    <textarea id="edit-desc-event" rows="5"></textarea>
                    <div class="char-counter" id="edit-desc-counter" style="font-size: 16px;">0 / 2500 caractères </div>
                </div>

                <div class="modal-field">
                    <label>Lien du formulaire :</label>
                    <input type="text" id="edit-url-form">
                </div>

                <div class="modal-field">
                    <label>Date de création (mois/jour/année):</label>
                    <input type="date" id="edit-date-creation" readonly disabled>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel" id="btn-cancel-modal" >Annuler</button>
                <button class="modal-btn-save" id="btn-save-offre">Valider</button>
            </div>

        </div>
    </div>

     <!-- ============================
        🔴 MODAL : CONFIRMATION SUPPRESSION ACTUALITÉ
    ============================== -->
    <div id="modal-delete-aides" class="modal-overlay">
        <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
                <h2>Confirmation de suppression</h2>
            </div>

            <!-- BODY -->
            <div class="modal-body">
                <p style="font-size:18px; font-family:'Nunito'; line-height:1.5;">
                    ⚠️ Êtes-vous sûr de vouloir supprimer cet évènement ?
                    <br><br>
                    <strong>Cette action est irréversible.</strong>
                </p>
            </div>

            <!-- FOOTER -->
            <div class="modal-footer">
                <button class="modal-btn-cancel" id="btn-cancel-delete">
                    Annuler
                </button>
                <button class="modal-btn-save" id="btn-confirm-delete">
                    Supprimer
                </button>
            </div>

        </div>
    </div>

        <!-- ============================
    🟣 MODAL : LISTE DES PARTICIPANTS
    ============================== -->
    <div id="modal-participants-event" class="modal-overlay">
        <div class="modal-content modal-large" id="modal-participants">

            <!-- ===== HEADER ===== -->
            <div class="modal-header">
                <h2 id="participants-event-title">
                    Liste des membres inscrits
                </h2>
            </div>

            <!-- ===== BODY ===== -->
            <div class="modal-body">

                <!-- Infos résumé (optionnel, prêt pour AJAX) -->
                <div class="participants-info">
                    <span id="participants-count">
                        👥 Total participants : 0
                    </span>
                </div>

                <!-- Conteneur scrollable -->
                <div class="participants-table-wrapper">

                    <table id="participants-table">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Date d’inscription</th>
                            </tr>
                        </thead>

                        <tbody id="participants-table-body">
                            
                           

                            <!-- <tr>
                                <td>Dupont</td>
                                <td>Jean</td>
                                <td>jean@email.com</td>
                                <td>+33 6 12 34 56 78</td>
                                <td>12/03/2026</td>
                            </tr> -->
                           
                        </tbody>
                    </table>

                    <!-- Message si aucun participant -->
                    <div id="participants-empty" class="no-result">
                        Aucun participant inscrit pour cet événement.
                    </div>

                </div>
            </div>

            <!-- ===== FOOTER ===== -->
            <div class="modal-footer">
                <button class="modal-btn-cancel">
                    Fermer
                </button>
                 <button class="modal-btn-save" id="csv_export">
                    Exporter en CSV
                </button>
            </div>

        </div>
    </div>

    <!-- Récupération du CSRF -->
    <input type="hidden" id="pikachu_csrf" value=<?= htmlspecialchars($_SESSION["csrf_token"]) ?> >
    <!-- Carte SUCCESS -->
    <div id="card-success" class="notif-card success">
        ✔️ Évènements mis à jour avec succès !
    </div>

    <!-- Carte ERROR -->
    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue lors de la mise à jour.
    </div>


    <?php require 'commun/footer.php';?>
    <script src="public/js/gestion_event_eea.js?v=<?= filemtime('public/js/gestion_event_eea.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>
