<?php require_once "commun/init.php" ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestions des aides - Association EEA</title>
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
         * PAGE : GESTION DES AIDES
         * - Sécurité centralisée via init.php
         * - Accès réservé aux membres du bureau
         ************************************************************/

        /* =========================================================
        1️⃣ AUTORISATION : UTILISATEUR CONNECTÉ
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
            include "commun/barre_navigation_pres.php";
        } else {
            include "commun/barre_navigation_conn.php";
        }

        /* =========================================================
        4️⃣ RÉCUPÉRATION DES AIDES
        ========================================================= */

        $aides = EEA_Database::fetchAides();
    ?>

    

    
       <!-- Titre de la page -->
    <div class="title-box">
        <h1>Gestion des Aides Étudiants</h1>
        <p style="font-size:20px; margin-top:10px; font-family:'Nunito';">
            Consultez les demandes d’aide envoyées par les étudiants et suivez leur traitement.
        </p>
    </div>

    
    <!-- Bloc des filtres -->
   <!-- ===== BLOC GLOBAL : FILTRES + ACTION ===== -->
    <div class="box-with-title">
        <span class="box-title">Filtre des demandes d’aide</span>

        <div class="filtre-membre">
            <span class="titre-fm">Filtrer les demandes</span>

            <div class="filtre-membre-grid">

                <div class="filter-item">
                    <label for="search-name">Nom / Prénom</label>
                    <input
                        type="text"
                        id="search-name"
                        placeholder="Rechercher par nom ou prénom..."
                    >
                </div>

                <div class="filter-item">
                    <label for="filter-type">Type d’aide</label>
                    <select id="filter-type">
                        <option value="">— Tous les types —</option>
                        <option value="Aide académique">Aide académique</option>
                        <option value="Aide administrative">Aide administrative</option>
                        <option value="Aide financière">Aide financière</option>
                        <option value="Soutien & écoute">Soutien & écoute</option>
                        <option value="Autre">Autre</option>
                    </select>
                </div>

            </div>
        </div>
    </div>

    <!-- Début du tableau -->
    <div class="total_information">

        <table id="table-aides">

            <!-- En-tête du tableau -->
            <thead>
                <tr>
                    <th>Nom & Prénom</th>
                    <th>Email</th>
                    <th>Membre</th>
                    <th>Type d’aide</th>
                    
                    <th>Date de demande</th>
                    <th class="col-modifier">Voir</th>
                    <th class="col-supprimer">Supprimer</th>
                </tr>
            </thead>


            <!-- Corps du tableau -->
            <tbody>

                <!-- Boucle pour afficher chaque membre dans une ligne -->
                <?php foreach ($aides as $aide): ?>
                    <tr>

                        <td>
                            <?= htmlspecialchars(
                                trim(($aide['prenom'] ?? '') . ' ' . ($aide['nom'] ?? ''))
                                ?: '—'
                            ) ?>
                        </td>



                        <td><?= htmlspecialchars($aide['email']) ?></td>

                        <td><?= !empty($aide['id_membre']) ? "Membre" : "Pas Membre" ?></td>

                        <td><?= htmlspecialchars($aide['type_aide']) ?></td>


                        <td><?= date("d/m/Y", strtotime($aide['date_demande'])) ?></td>

                        <!-- Voir (future modale ou page détail) -->
                        <td>
                            <button
                                class="btn-display"
                                data-id="<?= $aide['aide_id'] ?>"
                            >
                                Voir la demande
                            </button>
                        </td>

                        <!-- Supprimer -->
                        <td>
                            <button
                                class="btn-delete"
                                data-id="<?= $aide['aide_id'] ?>"
                            >
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
     <!-- ================= MODAL AFFICHER L'AIDE ================= -->
    <div id="modal-edit-aide" class="modal-overlay">
        <div class="modal-content">

            <!-- ===== HEADER ===== -->
            <div class="modal-header">
                <h2>Détails de la demande d’aide</h2>
            </div>

            <!-- ===== BODY ===== -->
            <div class="modal-body">

                <!-- Identité -->
                <div class="modal-field">
                    <label>👤 Étudiant</label>
                    <input type="text" id="edit-nom-prenom" disabled>
                </div>

                <!-- Type d’aide -->
                <div class="modal-field">
                    <label>🏷️ Type d’aide</label>
                    <input type="text" id="edit-type-aide" disabled>
                </div>

                <!-- Sujet -->
                <div class="modal-field">
                    <label>📝 Sujet</label>
                    <input type="text" id="edit-sujet-aide" disabled readonly>
                </div>

                <!-- Message -->
                <div class="modal-field">
                    <label>💬 Message de l’étudiant</label>
                    <textarea
                        id="edit-message-aide"
                        rows="7"
                        maxlength="2500"
                        placeholder="Message détaillé de la demande…" style="max-height: 150px;" disabled></textarea>
                </div>

                <!-- Contact -->
                <!-- Email -->
                <div class="modal-field">
                    <label>📧 Adresse e-mail</label>
                    <input type="text" id="edit-email-aide" disabled>
                </div>

                <!-- Téléphone -->
                <div class="modal-field">
                    <label>📞 Numéro de téléphone</label>
                    <input type="text" id="edit-telephone-aide" disabled>
                    <small style="color:#777; font-size:13px;">
                        Champ optionnel — renseigné par l’étudiant s’il souhaite être recontacté
                    </small>
                </div>


                <!-- Date -->
                <div class="modal-field">
                    <label>📅 Date de la demande</label>
                    <input type="date" id="edit-date-aide" disabled>
                    <small style="color:#777; font-size:15px; font-style: italic;">
                        Demande reçue par le Bureau EEA
                    </small>
                </div>

            </div>

            <!-- ===== FOOTER ===== -->
            <div class="modal-footer">
                <button class="modal-btn-cancel" id="btn-close-modal">
                    Fermer
                </button>
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
                    ⚠️ Êtes-vous sûr de vouloir supprimer cette demande aide ?
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


    <input type="hidden"  id="pikachu_csrf" value=<?= htmlspecialchars($_SESSION["csrf_token"]) ?> >

   <!-- ================= NOTIFICATIONS ================= -->
    <div id="card-success" class="notif-card success">
        ✔️ Suppression d'aide validée !
    </div>

    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue lors de la suppression.
    </div>


    <?php require 'commun/footer.php';?>
    <script src="public/js/gestion_aide_js.js?v=<?= filemtime('public/js/gestion_aide_js.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>
