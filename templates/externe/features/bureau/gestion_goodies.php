<?php require_once "commun/init.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Goodies - Association EEA</title>

    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/change_statut.css?v=<?= filemtime('public/css/change_statut.css') ?>">
    <link rel="stylesheet" href="public/css/modal.css">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Nunito&family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    /* =========================================================
    🟢 STYLE AJOUTÉ : Bouton "Ajouter une actualité"
    Ce style concerne uniquement le bouton vert utilisé
    dans le bloc "Action Actualité" (filtre + action).
    ========================================================= */

    .btn-add-goodies{
        background-color: #28a745;        /* Vert succès */
        color: #ffffff;                   /* Texte blanc */
        border: none;                     /* Supprime la bordure */
        padding: 12px 20px;               /* Taille du bouton */
        font-size: 20px;
        font-weight: bold;
        font-family: 'Nunito', sans-serif;
        border-radius: 6px;               /* Coins arrondis */
        cursor: pointer;
        transition: 
            background-color 0.2s ease,
            transform 0.1s ease,
            box-shadow 0.1s ease;
    }

    /* Effet visuel au survol */
    .btn-add-goodies:hover {
        background-color: #218838;        /* Vert plus foncé */
        transform: translateY(-1px);      /* Léger effet "lift" */
        box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    }

    /* Effet lors du clic */
    .btn-add-goodies:active {
        transform: translateY(0);
        box-shadow: none;
    }
    /*Effet Disabled*/
    .modal-btn-save:disabled {
        background-color: gray;
    }
    </style>

    


</head>

<body>

    <?php

        // ID membre issu de la session (déjà validée par init.php)
        $id_member = $user['id_membre'];

        /************************************************************
         *  VÉRIFICATION UTILISATEUR EN BASE
         *  ➜ Sécurité défensive : compte toujours existant
         ************************************************************/
        $found = EEA_Database::fetc_user_id($id_member);

        if (!$found || !is_array($found)) {
            header("Location: /?dest=logout");
            exit;
        }

        /************************************************************
         *  AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
         ************************************************************/
        if (empty($found['membre_bureau'])) {
            // ❌ Tout non-membre du bureau est expulsé
            header("Location: /?dest=logout");
            exit;
        }

        /************************************************************
         *  BARRE DE NAVIGATION (BUREAU)
         ************************************************************/
        if (
            $found['membre_bureau'] === "Président" ||
            $found['membre_bureau'] === "Web Admin"
        ) {
            require "commun/barre_navigation_pres.php";
        } else {
            require "commun/barre_navigation_conn.php";
        }

        /************************************************************
         *  DONNÉES GOODIES
         ************************************************************/
        $goodies = EEA_Database::fetchGoodies();
    ?>

    <!-- ================= TITRE ================= -->
    <div class="title-box">
        <h1>Gestion des Goodies</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Retrouvez tous les goodies proposés par l’association et utilisez
            les actions disponibles pour les modifier ou les supprimer.
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

        <!-- ==== PARTIE ACTION ==== -->
        <div class="filtre-membre" style="margin-top:30px;">
            <span class="titre-fm">Ajouter un goodies</span>

            <div class="filtre-membre-grid">
                <div class="filter-item">
                    <button class="btn-add-goodies" >
                        ➕ Ajouter un goodies
                    </button>
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
                    <th>Auteur</th>
                    <th class="col-modifier">Modifier</th>
                    <th class="col-supprimer">Supprimer</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($goodies as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g["nom_goodies"]) ?></td>
                    <td><?= number_format($g["prix"], 2, ',', ' ') ?> €</td>
                    <td><?= $g["nom"] . " " . $g["prenom"] ?></td>

                    <!-- Modifier -->
                    <td>
                        <button class="btn-change"
                            data-id="<?= htmlspecialchars($g["goodies_id"]) ?>">
                            ✏️ Modifier
                        </button>
                    </td>

                    <!-- Supprimer -->
                    <td>
                        <button class="btn-delete"
                            data-id="<?= htmlspecialchars($g["goodies_id"]) ?>">
                            🗑️ Supprimer
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


   <!-- ================= MODAL MODIFIER GOODIES ================= -->
    <div id="modal-edit-goodies" class="modal-overlay">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Informations du goodies</h2>
            </div>

            <div class="modal-body">

                <div class="modal-field">
                    <label>Nom du goodies :</label>
                    <input type="text" id="edit-nom-goodies">
                </div>

                <div class="modal-field">
                    <label>Prix (€) :</label>
                    <input type="number" step="0.01" id="edit-prix-goodies">
                </div>

                <div class="modal-field">
                    <label>Lien :</label>
                    <input type="text" id="edit-lien-goodies">
                </div>

                <div class="modal-field">
                    <label>Description :</label>
                    <textarea
                        id="edit-desc-goodies"
                        rows="6"
                        maxlength="2500"
                        placeholder="Description du goodies"></textarea>

                    <div class="char-counter" id="edit-desc-counter" style="font-size: 16px;">0 / 2500 caratères</div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel">Annuler</button>
                <button class="modal-btn-save" id="btn-save-goodies">Valider</button>
            </div>

        </div>
    </div>



    <!-- ================= MODAL AJOUTER GOODIES ================= -->
    <div id="modal-add-goodies" class="modal-overlay">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Ajouter un goodies</h2>
            </div>

            <div class="modal-body">

                <div class="modal-field">
                    <label>Nom du goodies :</label>
                    <input 
                        type="text"
                        id="add-nom-goodies"
                        maxlength="100"
                        placeholder="Ex : Sweat EEA">
                </div>

                <div class="modal-field">
                    <label>Prix (€) :</label>
                    <input 
                        type="number"
                        step="0.01"
                        id="add-prix-goodies"
                        placeholder="Ex : 25.00">
                </div>

                <div class="modal-field">
                    <label>Lien :</label>
                    <input 
                        type="text"
                        id="add-lien-goodies"
                        placeholder="Lien vers la boutique ou infos">
                </div>

                <div class="modal-field">
                    <label>Description :</label>
                    <textarea 
                        id="add-desc-goodies"
                        rows="6"
                        maxlength="2500"
                        placeholder="Description détaillée du goodies"></textarea>
                    <div class="char-counter" id="add-desc-counter" style="font-size: 16px;">0 / 2500 caractères </div>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel">Annuler</button>
                <button class="modal-btn-save" id="btn-add-goodies" disabled>
                    Ajouter
                </button>
            </div>

        </div>
    </div>



    <!-- ================= NOTIFICATIONS ================= -->
    <div id="card-success" class="notif-card success">
        ✔️ Action réalisée avec succès !
    </div>

    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue.
    </div>


    <?php require 'commun/footer.php'; ?>

    <script src="public/js/gestions_goodies.js?v=<?= filemtime('public/js/gestions_goodies.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>
