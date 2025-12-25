<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualité - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/index.css?v=20251225_2">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=20251225_3">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="public/css/actualite_style.css">
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


        $id_comb = $_GET["id_user"]; 
        list($id_member , $id_num ) = explode("_" , $id_comb ); 
    

        $found = EEA_Database::fetc_user_id($id_member);

                // 🔴 Récupération des actualités
        $actualites = EEA_Database::fetch_actualites();

        $nom_prenom = $found["prenom"]." ".$found["nom"];

          // Inclus la barre de navigation
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
    
   
            <!-- 📰 BLOC TITRE DU MUR -->
     <!-- ================= TITRE ================= -->
    <div class="title-box">
        <h1>Actualités de l’Association EEA</h1>
        <p>
            Découvrez les dernières actualités et annonces de l’association EEA,
            et restez informé des événements et initiatives en cours.
        </p>
    </div>

    <!-- ============================
    📰 MUR D’ANNONCES – ASSOCIATION EEA
    ============================= -->
    <!-- =====================================================
    📰 WRAPPER : FOND FIXE + LOGO FILIGRANE
    (NE SCROLLE PAS)
    ====================================================== -->


    <div class="annonces-wrapper">
        <!-- =================================================
             📰 CONTENEUR SCROLLABLE : CONTENU
        ================================================== -->
        <div class="annonces-container">

                    <!-- ============================
                🔁 BOUCLE ACTUALITÉS (DB)
            ============================= -->
            <?php foreach ($actualites as $actu): ?>

                <div class="annonce-card">

                    <div class="card-header">
                        <span class="card-date">
                            <?= date("d/m/Y", strtotime($actu["date_depot"])) ?>
                        </span>
                        <h3 class="card-title">
                            <?= htmlspecialchars($actu["titre_actu"]) ?>
                        </h3>
                    </div>

                    <p class="card-text">
                        <?= nl2br(htmlspecialchars(mb_strimwidth($actu["desc_actu"], 0, 220, "..."))) ?>
                    </p>

                    <!-- 🔘 BOUTON LIRE LA SUITE -->
                    <!-- (data-id PRÉPARÉ POUR LE MODAL PLUS TARD) -->
                    <button class="btn-lire-plus"
                            data-id="<?= $actu['actu_id'] ?>">
                        Lire la suite
                    </button>

                </div>

            <?php endforeach; ?>

            <!-- Cas où il n’y a aucune actualité -->
            <?php if (empty($actualites)): ?>
                <p style="text-align:center; color:#666;">
                    Aucune actualité n’est disponible pour le moment.
                </p>
            <?php endif; ?>
        </div>
        <!-- FIN annonces-container -->
    </div>
    <!-- FIN annonces-wrapper -->
    <!-- ============================
     🟣 MODAL : LIRE UNE ACTUALITÉ
    ============================== -->
  <!-- ============================
     🟣 MODAL : LIRE UNE ACTUALITÉ
     (Contenu injecté en JS)
============================== -->
    <div id="modal-read-actu" class="modal-overlay">
        <div class="modal-content">

            <!-- ===== HEADER ===== -->
            <div class="modal-header">
                <h2 id="modal-actu-title"></h2>
            </div>

            <!-- ===== BODY ===== -->
            <div class="modal-body">

                <!-- 📅 Date de publication -->
                <p class="modal-date" id="modal-actu-date"></p>

                <!-- 📝 Description complète -->
                <div class="modal-description" id="modal-actu-desc"></div>

                <!-- 🔗 Lien complémentaire -->
                <div class="modal-link-box" id="modal-link-box" style="display:none;">
                    <a id="modal-actu-link" href="#" target="_blank"></a>
                </div>

                <!-- ✍️ Signature -->
                <div class="modal-signature">
                    <span id="modal-actu-signature"></span>
                </div>

            </div>

            <!-- ===== FOOTER ===== -->
            <div class="modal-footer">
                <button class="modal-btn-cancel">Fermer</button>
            </div>

        </div>
    </div>




    <?php require 'commun/footer.php';?>
    <script src="public/js/display_actualitev7.js"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>