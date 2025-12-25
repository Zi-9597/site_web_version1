<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestions des aides - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/index.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=20251225_3">
    <link rel="stylesheet" href="public/css/change_statut.css">
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
    // Simple test to display "ancien" on the page
        require_once "require_db.php";

        $id_comb = $_GET["id_user"];
        list($id_member , $id_num ) = explode("_", $id_comb);

        $found = EEA_Database::fetc_user_id($id_member);

        $nom_prenom = $found["prenom"]." ".$found["nom"];

        if($found["membre_bureau"] === "Président") {
            include "commun/barre_navigation_pres.php";
        } else {
            include "commun/barre_navigation_conn.php";
        }

        // 🔴 Récupération des Aides
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
                    <th>Type d’aide</th>
                    <th>Sujet</th>
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

                        <td><?= htmlspecialchars($aide['type_aide']) ?></td>

                        <td><?= htmlspecialchars($aide['sujet']) ?></td>

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
                    <input type="text" id="edit-sujet-aide">
                </div>

                <!-- Message -->
                <div class="modal-field">
                    <label>💬 Message de l’étudiant</label>
                    <textarea
                        id="edit-message-aide"
                        rows="7"
                        maxlength="2500"
                        placeholder="Message détaillé de la demande…">
                    </textarea>

                    <small style="color:#666; font-size:13px;">
                        Maximum 2500 caractères
                    </small>
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
                    <small style="color:#777; font-size:13px; font-style: italic;">
                        Demande reçue par le Bureau EEA
                    </small>
                </div>

            </div>

            <!-- ===== FOOTER ===== -->
            <div class="modal-footer">
                <button class="modal-btn-cancel">
                    Fermer
                </button>
            </div>

        </div>
    </div>

        

   <!-- ================= NOTIFICATIONS ================= -->
    <div id="card-success" class="notif-card success">
        ✔️ Suppression d'aide validée !
    </div>

    <div id="card-error" class="notif-card error">
        ❌ Une erreur est survenue lors de la suppression.
    </div>


    <?php require 'commun/footer.php';?>
    <script src="public/js/gestion_aide_js.js"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>