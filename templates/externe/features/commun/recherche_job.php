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
    <title>Recherche d'offre - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/change_statut.css?v=<?= filemtime('public/css/change_statut.css') ?>">
    <link rel="stylesheet" href="public/css/modal.css">
    <link rel="stylesheet" href="public/css/depot_offre.css">
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
         *  INITIALISATION
         *  - init.php déjà inclus
         *  - $user est valide ou null
         ************************************************************/

        // Sécurité minimale : page réservée aux connectés
        if (!$user) {
            header("Location: /?dest=acceuil");
            exit;
        }

        /************************************************************
         *  RÉCUPÉRATION DES DONNÉES UTILISATEUR (BASE)
         ************************************************************/

        $id_member = $user['id_membre'];

        // Chargement DB (source fiable pour l’interface)
        $found = EEA_Database::fetc_user_id($id_member);

        // Sécurité défensive (compte supprimé / incohérent)
        if (!$found || !is_array($found)) {
            header("Location: /?dest=logout");
            exit;
        }

        /************************************************************
         *  CHOIX DE LA BARRE DE NAVIGATION
         *  ➜ Conditions basées sur la SESSION ($user)
         ************************************************************/

        if (!empty($user['membre_bureau'])) {

            if (
                $user['membre_bureau'] === "Président" ||
                $user['membre_bureau'] === "Web Admin"
            ) {
                require "commun/barre_navigation_pres.php";
            } else {
                require "commun/barre_navigation_conn.php";
            }

        } else {

            if ($user['membre_assoc'] === "Étudiant/e") {
                require "commun/barre_conn_etu.php";
            }
            elseif ($user['membre_assoc'] === "Alumni/e") {
                require "commun/barre_conn_ancien.php";
            }
            else {
                // État incohérent (sécurité défensive)
                header("Location: /?dest=logout");
                exit;
            }
        }

        /************************************************************
         *  RECHERCHE DES JOBS
         ************************************************************/

        $offres = EEA_Database::fetchUserJobs();
    ?>

    <!-- Titre de la page -->
    <div class="title-box">
        <h1>Recherche d’Offres d’Emploi</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Explorez les jobs étudiants et les offres de <strong>stage</strong>, <strong>alternance</strong> et
            <strong>emploi (CDI/CDD)</strong> proposées par les membres et partenaires de l’association.
            Utilisez les filtres disponibles pour affiner votre recherche et accéder rapidement
            aux opportunités correspondant à votre profil.
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
                        <option value="Alternance">Alternance</option>
                        <option value="Job Étudiant">Job Étudiant</option>
                    </select>
                </div>

            </div>
        </div>

        <!-- ================= FILTRE SPÉCIALITÉS ================= -->
        <div class="filtre-membre">
            <span class="titre-fm">Filtre par spécialité</span>

            <div class="specialites-grid">
                <label>
                    <input type="checkbox" class="filter-specialite" value="Electronique">
                    <span>Électronique</span>
                </label>
                <label>
                    <input type="checkbox" class="filter-specialite" value="Informatique">
                    <span>Informatique</span>
                </label>
                <label>
                    <input type="checkbox" class="filter-specialite" value="Télécom / Systèmes communicants">
                    <span>Télécom / Systèmes communicants</span>
                </label>
                <label>
                    <input type="checkbox" class="filter-specialite" value="Énergie Électrique">
                    <span>Énergie Électrique</span>
                </label>
                <label>
                    <input type="checkbox" class="filter-specialite" value="Automatique / Automatisme">
                    <span>Automatique / Automatisme</span>
                </label>
                <label>
                    <input type="checkbox" class="filter-specialite" value="Transports">
                    <span>Transports</span>
                </label>
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
                    <th>Date de publication</th>
                    <th class="col-modifier">Consulter</th>
                    
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
                                    Consulter l'offre
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
                <h2>Information sur l'offre</h2>
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
                    <input type="text" id="edit-url" disabled readonly>
                </div>

                <!-- DESCRIPTION -->
                <div class="modal-field">
                    <label for="edit-description">Description :</label>
                    <textarea id="edit-description" rows="5" disabled></textarea>
                </div>
              
                <!-- TYPE CONTRAT -->
                <div class="modal-field">
                    <label for="edit-contrat">Type de contrat :</label>
                    <select id="edit-contrat" disabled>
                        <option value="Stage">Stage</option>
                        <option value="CDD/CDI">CDD/CDI</option>
                        <option value="Alternance">Alternance</option>
                        <option value="Job Étudiant">Job Étudiant</option>

                    </select>
                </div>

                <!-- SPÉCIALITÉS (MULTI SELECT) -->
                <div class="modal-field modal-field-specialites">
                    <label for="edit-specialites">Spécialités :</label>
                    <select id="edit-specialites" multiple size="6" disabled>
                        <option value="1">Informatique</option>
                        <option value="2">Electronique</option>
                        <option value="3">Télécom / Systèmes communicants</option>
                        <option value="4">Énergie Électrique</option>
                        <option value="5">Automatique / Automatisme</option>
                        <option value="6">Transports</option>
                    </select>
                    <small>Maintenez CTRL ou CMD pour sélectionner plusieurs spécialités</small>
                </div>

                <!-- DATE -->
                <div class="modal-field">
                    <label for="edit-date">Date de création (mois/jour/année):</label>
                    <input type="date" id="edit-date" readonly>
                </div>

            </div>

            <!-- Footer (boutons) -->
            <div class="modal-footer">
                <button class="modal-btn-cancel" >Annuler</button>
                
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
    <script src="public/js/fetch_offres_eea.js?v=<?= filemtime('public/js/fetch_offres_eea.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>




</body>
</html>