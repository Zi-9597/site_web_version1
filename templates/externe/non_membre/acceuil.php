<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceuil- Association EEA</title>
    <link rel="stylesheet" href="../../public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="../../public/css/index.css">
    <link rel="stylesheet" href="../../public/css/logo_gestion.css">
    <link rel="stylesheet" href="../../public/css/presentation_acceuil.css">
    <link rel="stylesheet" href="../../public/css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <script src="public/js/gestion_slidebar_1.js"></script>
    <script src="public/js/acceuil_page.js"></script>
    
    
    <?php require_once 'commun/barre_navigation.php'; ?>
    <div class="acceuil_presentation">

        <div class="img-pres">
            <p> Les buts de l’association consistent à créer des activités et des liens les  étudiants, les anciens et entre anciens-étudiants </p>
            <img class="img_evenement" src="public/pictures/img_activites/img_bowling_2.png" alt="EVenement1">
        </div>
    
        <div class="img_txt_reseau">
            <div class="mini-des">
                <p> Parrainage des nouveaux étudiants à Lille par des étudiants plus expérimentés </p>
            </div>
            <div class="img_sub_reseau">
                        
                <div class="img-div-reseau">
                    <p>Participation au forum des Master EEA </p>
                    <img class="img_evenement" src="public/pictures/img_evenements/img_master_1.jpg" alt="EVenement1">
                </div>
                <div class="img-div-reseau">
                    <p>Interventions des entreprises pour les étudiants d'EEA </p>
                    <img class="img_evenement" src="public/pictures/img_evenements/img_1.jpg" alt="Evenement2">
                </div>
                <div class="img-div-reseau">
                    <p>Expériences enrichissante à travers de visites d'entreprises</p>
                    <img class="img_evenement" src="public/pictures/img_evenements/img_pro_3.jpg" alt="Evenement3">
                </div>
                <div class="img-div-reseau">
                    <p>Organisation de réunions entre anciens et étudiants </p>
                    <img class="img_evenement" src="public/pictures/img_evenements/img_reu_anc_etu.jpg" alt="Evenement4">
                </div>
                <div class="img-div-reseau">
                    <p>Parrainage d’étudiants de Master 1 par des anciens </p>
                    <img class="img_evenement" src="public/pictures/img_evenements/img_anc_mast.jpg" alt="Evenement5">
                </div>
                    
            </div>
        </div>

        <div class="img_txt_activite">
            <div class="mini-des">
                <p> Elle organise différentes activités ludiques pour créer un lien entre étudiants (foot, visites musées, boowling,...). </p>
            </div>
            <div class="img_sub_activite">
                        
                <div class="img-div-activite">
                    <p>Activité SPortive : Football entre étudiants</p>
                    <img class="img_evenement" src="public/pictures/img_activites/img_foot.jpg" alt="EVenement6">
                </div>
                <div class="img-div-activite">
                    <p>Activité Culturelle : Visite du musée de Rébublique, Lille </p>
                    <img class="img_evenement" src="public/pictures/img_activites/img_musee.jpg" alt="Evenement2">
                </div>
                <div class="img-div-activite">
                    <p>Activité Détente : Concert à l'université de LIlle (Summer Break) </p>
                    <img class="img_evenement" src="public/pictures/img_activites/img_concert.jpg" alt="Evenement2">
                </div>
                <div class="img-div-activite">
                    <p>Activité Professeur-Étudiant : Remise des livres d'or </p>
                    <img class="img_evenement" src="public/pictures/img_activites/remise_livre_or.jpg" alt="Evenement2">
                </div>
                    
            </div>

        </div>
        <div class="list_avantage">
            <div class="mini-des">
                <p> Elle participe aux différentes activités  et subvient aux besoins des étudiants</p>
            </div>
                <ul>
                    <li>Soirée de clôture JIVE (septembre)</li>
                    <li>Séance de révision entre les étudiants </li>
                    <li>Soirée de clôture international student week (octobre) </li>
                    <li>Urban trail de Lille (Novembre), Repas de Noël (décembre), Summer break (Mai) </li>
                    <li>Elle aide les étudiants en diffcultés (aide financière, démarche administratif, intégration à la vie étudiante ,...) </p>
                </ul>
        </div>
    </div>
    <?php require 'commun/footer.php';?>
</body>
</html>