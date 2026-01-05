<?php require_once "commun/init.php"; ?>
<!--
    Initialisation globale de l’application :
    - démarrage de la session
    - connexion à la base de données
    - récupération de l’utilisateur connecté dont le nom et prénom
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goodies - Association EEA</title>
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
</head>
<body>
    
    <?php

       /* =========================
        BARRE DE NAVIGATION
        ========================= */

        // Par défaut : utilisateur non connecté
        $navFile = "commun/barre_navigation.php";

        if ($user) {

            // Cas : membre du bureau
            if (!empty($user['membre_bureau'])) {

                if ($user['membre_bureau'] === "Président" || $user['membre_bureau'] === "Web Admin") {
                    $navFile = "commun/barre_navigation_pres.php";
                } else {
                    $navFile = "commun/barre_navigation_conn.php";
                }

            }
            // Cas : membre de l'association
            else {

                if ($user['membre_assoc'] === "Étudiant/e") {
                    $navFile = "commun/barre_conn_etu.php";
                } elseif ($user['membre_assoc'] === "Alumni/e") {
                    $navFile = "commun/barre_conn_ancien.php";
                } else {
                    $navFile = "commun/barre_navigation.php";
                }
            }
        }

        // Affichage de la barre
        require $navFile;

        /* =========================
        3. DONNÉES GOODIES
        ========================= */

        $goodies = EEA_Database::fetchGoodies();
    ?>

    
   
         <!-- ================= TITRE ================= -->
    <div class="title-box">
        <h1>Achat des Goodies</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Retrouvez tous les goodies proposés par l’association.
            En cliquant sur le lien associé à chaque produit, vous serez redirigé
            vers l’application dédiée afin de procéder à l’achat.
        </p>
    </div>

    <!-- ================= FILTRE ================= -->
    <div class="box-with-title">
        <span class="box-title">Filtre et Ajout de goodies</span>

        <div class="filtre-membre">
            <span class="titre-fm">Filtre Goodies</span>

            <div class="filtre-membre-grid">
                <div class="filter-item">
                    <label for="search-nom-goodies">Nom du goodies :</label>
                    <input
                        type="text"
                        id="search-nom-goodies"
                        placeholder="Rechercher un goodies par nom..."
                    >
                </div>
            </div>
        </div>

    </div>


   <!-- ================= TABLEAU ================= -->
    <div class="total_information">

        <table id="table-goodies">
            <thead>
                <tr>
                    <th>Nom du goodies</th>
                    <th>Prix</th>
                    <th class="col-modifier">Acheter</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($goodies as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g["nom_goodies"]) ?></td>
                    <td><?= number_format($g["prix"], 2, ',', ' ') ?> €</td>

                    <!-- Modifier -->
                    <td>
                        <button class="btn-change"
                            data-id="<?= htmlspecialchars($g["goodies_id"]) ?>">
                            💳 Achat
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <div id="no-result" class="no-result">
            Aucun goodies trouvé
        </div>
    </div>
   <!-- ================= MODAL AFFICHER GOODIES ================= -->
    <div id="modal-edit-goodies" class="modal-overlay">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Informations du goodies</h2>
            </div>

            <div class="modal-body">

                <div class="modal-field">
                    <label>Nom du goodies :</label>
                    <input type="text" id="edit-nom-goodies" disabled>
                </div>

                <div class="modal-field">
                    <label>Prix (€) :</label>
                    <input id="edit-prix-goodies" disabled>
                </div>

                <div class="modal-field">
                    <label>Lien :</label>
                    <input type="text" id="edit-lien-goodies" disabled>
                </div>

                <div class="modal-field">
                    <label>Description :</label>
                    <textarea
                        id="edit-desc-goodies"
                        rows="6"
                        maxlength="2500"
                        disabled
                        placeholder="Description du goodies">
                    </textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel">Annuler</button>
            </div>

        </div>
    </div>


    <?php require 'commun/footer.php';?>
    <script src="public/js/affichage_goodies.js"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>