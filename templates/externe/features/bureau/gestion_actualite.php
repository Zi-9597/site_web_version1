<?php require_once "commun/init.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Actualités - Association EEA</title>

    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="public/css/change_statut.css?v=<?= filemtime('public/css/change_statut.css') ?>">
    <link rel="stylesheet" href="public/css/modal.css">

    <link href="https://fonts.googleapis.com/css2?family=Nunito&family=Open+Sans&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* =========================================================
        🟢 STYLE AJOUTÉ : Bouton "Ajouter une actualité"
        Ce style concerne uniquement le bouton vert utilisé
        dans le bloc "Action Actualité" (filtre + action).
        ========================================================= */

        .btn-add-actu {
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
        .btn-add-actu:hover {
            background-color: #218838;        /* Vert plus foncé */
            transform: translateY(-1px);      /* Léger effet "lift" */
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        /* Effet lors du clic */
        .btn-add-actu:active {
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
        /************************************************************
         *  PAGE : GESTION DES ACTUALITÉS
         *  - Sécurité centralisée via init.php
         *  - Accès réservé aux membres du bureau
         ************************************************************/

        /* =========================================================
        1️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
        ========================================================= */

        if (empty($user['membre_bureau'])) {
            // État non autorisé → déconnexion immédiate
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        2️⃣ VÉRIFICATION DB (défensive)
        ➜ protège contre session obsolète ou rôle modifié
        ========================================================= */

        $found = EEA_Database::fetc_user_id($user['id_membre']);

        if (!$found || empty($found['membre_bureau'])) {
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        3️⃣ BARRE DE NAVIGATION (bureau)
        ========================================================= */

        if (
            $found['membre_bureau'] === "Président" ||
            $found['membre_bureau'] === "Web Admin"
        ) {
            require "commun/barre_navigation_pres.php";
        } else {
            require "commun/barre_navigation_conn.php";
        }

        /* =========================================================
        4️⃣ DONNÉES MÉTIER
        ========================================================= */

        // Récupération des actualités
        $actualites = EEA_Database::fetch_actualites();
    ?>
    <!-- ================= TITRE ================= -->
    <div class="title-box">
        <h1>Gestion des Actualités</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Retrouvez toutes les actualités publiées et utilisez les actions disponibles
            pour les modifier ou les supprimer.
        </p>
    </div>

    <!-- ================= FILTRE ================= -->
    <div class="box-with-title">
        <span class="box-title">Filtre et Ajout d'actualités</span>

        <div class="filtre-membre">
            <span class="titre-fm">Filtre Actualité</span>

            <div class="filtre-membre-grid">
                <div class="filter-item">
                    <label for="search-titre-actu">Titre de l’actualité :</label>
                    <input type="text" id="search-titre-actu"
                        placeholder="Rechercher le titre de l’actualité...">
                </div>
            </div>
            
        </div>

        <!-- ==== PARTIE ACTION ==== -->
        <div class="filtre-membre" style="margin-top:30px;">
            <span class="titre-fm">Ajouter une actualité</span>

            <div class="filtre-membre-grid">

                <div class="filter-item">
                    <button class="btn-add-actu">
                        ➕ Ajouter une actualité
                    </button>
                </div>

            </div>
        </div>

    </div>

    <!-- ================= TABLEAU ================= -->
    <div class="total_information">

        <table id="table-offres">
            <thead>
                <tr>
                    <th>Titre de l’actualité</th>
                    <th>Date de publication</th>
                    <th>Auteur</th>
                    <th class="col-modifier">Modifier</th>
                    <th class="col-supprimer">Supprimer</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($actualites as $actu): ?>
                <tr>
                    <td><?= htmlspecialchars($actu["titre_actu"]) ?></td>
                    <td><?= date("d/m/Y", strtotime($actu["date_depot"])) ?></td>
                    <td><?= $actu["nom"] . " " . $actu["prenom"] ?></td>

                    <!-- Modifier -->
                    <td>
                        <button class="btn-change"
                            data-id="<?= htmlspecialchars($actu["actu_id"]) ?>">
                            ✏️ Modifier
                        </button>
                    </td>

                    <!-- Supprimer -->
                    <td>
                        <button class="btn-delete"
                            data-id="<?= htmlspecialchars($actu["actu_id"]) ?>">
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

    <!-- ================= MODAL MODIFIER ================= -->
    <div id="modal-edit-event" class="modal-overlay">
        <div class="modal-content">

            <div class="modal-header">
                <h2>Informations de l’actualité</h2>
            </div>

            <div class="modal-body">

                <div class="modal-field">
                    <label>Titre :</label>
                    <input type="text" id="edit-titre-actu">
                </div>

                <div class="modal-field">
                    <label>Lien :</label>
                    <input type="text" id="edit-lien-actu">
                </div>

                <!-- ✅ DESCRIPTION AJOUTÉE -->
                <div class="modal-field">
                    <label>Description :</label>
                    <textarea
                        id="edit-desc-actu"
                        rows="6"
                        maxlength="2500"
                        placeholder="Description de l’actualité (2500 caractères maximum)"></textarea>

                    <div class="char-counter" id="edit-desc-counter" style="font-size: 16px;">0 / 2500 caratères</div>
                </div>

                <div class="modal-field">
                    <label>Date de publication :</label>
                    <input type="date" id="edit-date-depot" disabled>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel">Annuler</button>
                <button class="modal-btn-save" id="btn-save-actu" disabled>Valider</button>
            </div>

        </div>
    </div>

    <!-- ============================
        🟢 MODAL : AJOUTER UNE ACTUALITÉ
        Même forme que le modal "Modifier"
    ============================== -->
    <div id="modal-add-actu" class="modal-overlay">
        <div class="modal-content">

            <!-- ===== HEADER ===== -->
            <div class="modal-header">
                <h2>Ajouter une actualité</h2>
            </div>

            <!-- ===== BODY ===== -->
            <div class="modal-body">

                <!-- Titre actualité -->
                <div class="modal-field">
                    <label>Titre de l’actualité :</label>
                    <input 
                        type="text" 
                        id="add-titre-actu"
                        maxlength="50"
                        placeholder="Ex : Lancement du nouveau site EEA">
                </div>

                <!-- Lien actualité -->
                <div class="modal-field">
                    <label>Lien de l’actualité (Linkedin si possible):</label>
                    <input 
                        type="text" 
                        id="add-lien-actu"
                        maxlength="50"
                        placeholder="Lien Linkedin">
                </div>

                <!-- Description -->
                <div class="modal-field">
                    <label>Description :</label>
                    <textarea 
                        id="add-desc-actu"
                        rows="6"
                        maxlength="2500"
                        placeholder="Description détaillée de l’actualité (2500 caractères max)"></textarea>
                    <!-- Indication utilisateur -->
                    <div class="char-counter" id="add-desc-counter" style="font-size: 16px;">0 / 2500 caractères </div>
                </div>
            </div>

            <!-- ===== FOOTER ===== -->
            <div class="modal-footer">
                <button class="modal-btn-cancel">
                    Annuler
                </button>
                <button class="modal-btn-save" id="btn-add-actu" disabled>
                    Ajouter
                </button>
            </div>

        </div>
    </div>

    <!-- ================= NOTIFICATIONS ================= -->
    <div id="card-success" class="notif-card success">
        ✔️ Actualité mise à jour avec succès !
    </div>

    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue lors de la mise à jour.
    </div>

    <?php require 'commun/footer.php'; ?>

    <script src="public/js/gestion_actualitesv1.js?v=<?= filemtime('public/js/gestion_actualitesv1.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>
