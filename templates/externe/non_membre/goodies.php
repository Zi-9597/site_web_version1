
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Goodies - Association EEA</title>
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
    
    <?php require 'commun/barre_navigation.php'; ?>
    <?php
    // Simple test to display "ancien" on the page
        require_once "require_db.php";

        // 🔴 Récupération des goodies
        $goodies = EEA_Database::fetchGoodies();
    ?>
    
   
         <!-- ================= TITRE ================= -->
    <div class="title-box">
        <h1>Gestion des Goodies</h1>
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
                    <input type="text" id="edit-nom-goodies" disabled readonly>
                </div>

                <div class="modal-field">
                    <label>Prix (€) :</label>
                    <input type="number" step="0.01" id="edit-prix-goodies" disabled readonly>
                </div>

                <div class="modal-field">
                    <label>Lien :</label>
                    <input type="text" id="edit-lien-goodies" disabled readonly>
                </div>

                <div class="modal-field">
                    <label>Description :</label>
                    <textarea
                        id="edit-desc-goodies"
                        rows="6"
                        maxlength="2500"
                        placeholder="Description du goodies" disabled>
                    </textarea>
                    <small style="color:#666; font-size:13px;">
                        Maximum 2500 caractères
                    </small>
                </div>

            </div>

            <div class="modal-footer">
                <button class="modal-btn-cancel">Annuler</button>
            </div>

        </div>
    </div>


    <?php require 'commun/footer.php';?>
    <script src="public/js/affichage_goodies.js"></script>
    <script src="public/js/gestion_slide_bar_4.js"></script>

</body>
</html>