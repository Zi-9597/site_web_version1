<?php require_once "commun/init.php" ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des offres - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/change_statut.css?v=<?= filemtime('public/css/change_statut.css') ?>">
    <link rel="stylesheet" href="public/css/modal.css">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
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
         * PAGE : GESTION DES OFFRES UTILISATEUR
         * - init.php est déjà inclus en première ligne
         * - $user est soit valide, soit null
         * - Sécurité session + timeout déjà gérés
         ************************************************************/

        /* =========================================================
        1️⃣ UTILISATEUR CONNECTÉ
        ========================================================= */

        if (!$user) {
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        2️⃣ DONNÉES UTILISATEUR (SESSION)
        ========================================================= */

        $id_member = $user['id_membre'];
        $email     = $user['email'] ?? null;

        /* =========================================================
        3️⃣ VÉRIFICATION UTILISATEUR EN BASE
        ➜ Sécurité défensive (compte supprimé, session ancienne, etc.)
        ========================================================= */

        $found = EEA_Database::fetc_user_id($id_member);

        if (!$found || !is_array($found)) {
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        4️⃣ RÉCUPÉRATION DES OFFRES DE L’UTILISATEUR
        ========================================================= */

        $offres = EEA_Database::fetchUserJobs($email);

        /* =========================================================
        5️⃣ CHOIX DE LA BARRE DE NAVIGATION
        ========================================================= */

        if (!empty($found['membre_bureau'])) {

            // 🔵 Membres du bureau
            if ($found['membre_bureau'] === "Président" || $found['membre_bureau'] === "Web Admin") {
                require "commun/barre_navigation_pres.php";
            } else {
                require "commun/barre_navigation_conn.php";
            }

        } else {

            // 🟢 Membres de l’association
            if ($found['membre_assoc'] === "Étudiant/e") {
                require "commun/barre_conn_etu.php";
            }
            elseif ($found['membre_assoc'] === "Alumni/e") {
                require "commun/barre_conn_ancien.php";
            }
            else {
                // 🔴 État incohérent → déconnexion
                header("Location: /?dest=logout");
                exit;
            }
        }
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
                    <label for="search-titre">Nom Offre :</label>
                    <input type="text" id="search-titre-offre" placeholder="Rechercher le nom de l'offre...">
                </div>

                <!-- Section -->
                <div class="filter-item">
                    <label for="filiere-type">Type d'offre :</label>
                    <select id="filiere-section">
                        <option value="">Tous type d'offres</option>
                        <option value="Stage">Stage</option>
                        <option value="CDD/CDI">CDD/CDI</option>
                        <option value="Job Étudiant">Alternance</option>
                        <option value="Job Étudiant">Job Étudiant</option>
                    </select>
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
                    <th>Titre</th>
                    <th>Spécialités</th>
                    <th>Contrat</th>
                    <th>Date de création</th>
                    <th class="col-modifier">Modifier</th>
                    <th class="col-supprimer">Supprimer</th>
                    
                    <!-- Colonne changement (cachée par défaut) -->
                </tr>
            </thead>

            <!-- Corps du tableau -->
            <tbody>

                <!-- Boucle pour afficher chaque membre dans une ligne -->
                <?php foreach ($offres as $offre): ?>
                        <tr>
                            <td><?= htmlspecialchars($offre["titre_offre"]) ?></td>

                            <td><?= htmlspecialchars($offre["specialites"]) ?></td>

                            <td><?= htmlspecialchars($offre["type_contrat"]) ?></td>


                            <td><?= date("d/m/Y" , strtotime($offre["date_creation"]))?></td>

                            <!-- Bouton Modifier -->
                            <td>
                                <button class="btn-change"
                                    data-id="<?= htmlspecialchars($offre['id_offre']) ?>">
                                    ✏️ Modifier
                                </button>
                            </td>

                            <!-- Bouton Supprimer -->
                            <td>
                                <button class="btn-delete" 
                                    data-id="<?= htmlspecialchars($offre['id_offre']) ?>">
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
        🟣 MODAL : MODIFIER UNE OFFRE
    ============================== -->
    <div id="modal-edit-offre" class="modal-overlay">

        <div class="modal-content">

            <!-- Bandeau violet -->
            <div class="modal-header">
                <h2>Modifier l'offre</h2>
            </div>

            <!-- Corps -->
            <div class="modal-body">


                <!-- TITRE -->
                <div class="modal-field">
                    <label for="edit-titre-offre">Titre de l'offre :</label>
                    <input type="text" id="edit-titre-offre">
                </div>

                <!-- URL LINKEDIN -->
                <div class="modal-field">
                    <label for="edit-url">Lien LinkedIn :</label>
                    <input type="text" id="edit-url">
                </div>

                <!-- DESCRIPTION -->
                <div class="modal-field">
                    <label for="edit-description">Description :</label>
                    <textarea id="edit-description" rows="5"></textarea>
                </div>
              
                <!-- TYPE CONTRAT -->
                <div class="modal-field">
                    <label for="edit-contrat">Type de contrat :</label>
                    <select id="edit-contrat">
                        <option value="Stage">Stage</option>
                        <option value="CDD/CDI">CDD/CDI</option>
                        <option value="Alternance">Alternance</option>
                        <option value="Job Étudiant">Job Étudiant</option>

                    </select>
                </div>

                <!-- SPÉCIALITÉS (MULTI SELECT) -->
                <div class="modal-field modal-field-specialites">
                    <label for="edit-specialites">Spécialités :</label>
                    <select id="edit-specialites" multiple size="6">
                        <option value="1">Electronique</option>
                        <option value="2">Informatique</option>
                        <option value="3">Télécom / Systèmes communicants</option>
                        <option value="4">Énergie Électrique</option>
                        <option value="5">Automatique / Automatisme</option>
                        <option value="6">Transports</option>
                    </select>
                    <small>Maintenez CTRL ou CMD pour sélectionner plusieurs spécialités</small>
                </div>

                <!-- DATE -->
                <div class="modal-field">
                    <label for="edit-date">Date de création :</label>
                    <input type="date" id="edit-date" readonly disabled>
                </div>

            </div>

            <!-- Footer (boutons) -->
            <div class="modal-footer">
                <button class="modal-btn-cancel" >Annuler</button>
                <button class="modal-btn-save" id="btn-save-offre">Valider</button>
            </div>

        </div>
    </div>

    <input type="hidden" id="pikachu_csrf" value=<?=  htmlspecialchars($_SESSION["csrf_token"]) ?>>
    

    <!-- Carte SUCCESS -->
    <div id="card-success" class="notif-card success">
        ✔️ Offre mise à jour avec succès !
    </div>

    <!-- Carte ERROR -->
    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue lors de la mise à jour.
    </div>

    <?php require 'commun/footer.php';?>
    <script src="public/js/gestion_offres_eea_v1.js?v=<?= filemtime('public/js/gestion_offres_eea_v1.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>