<?php include_once "commun/init.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>À propos de l'association - Association EEEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="public/css/propos_nous.css">

   
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

    
    <?php
        /* ============================================================================
        📌 GESTION DE LA BARRE DE NAVIGATION
        - init.php déjà inclus
        - Accessible à tous (connectés ou non)
        - États incohérents → logout
        - Aucune redondance session / DB
        ============================================================================ */

        /* ============================================================
        👤 1) UTILISATEUR NON CONNECTÉ
        ============================================================ */
        if (!$user) {

            // Visiteur simple
            include_once "commun/barre_navigation.php";
            
        }
        else
        {
            /* ============================================================
            🔎 2) VALIDATION UTILISATEUR EN BASE (sécurité défensive)
            ============================================================ */
            $found = EEA_Database::fetc_user_id($user['id_membre']);

            if (!$found || !is_array($found)) {
                header("Location: /?dest=logout");
                exit;
            }

            /* (optionnel) nom_prenom si nécessaire ailleurs */
            $nom_prenom = trim($found['prenom'] . ' ' . $found['nom']);

            /* ============================================================
            🧭 3) CHOIX DE LA BARRE DE NAVIGATION
            ============================================================ */

            /* 🔴 MEMBRE DU BUREAU */
            if (!empty($found['membre_bureau'])) {

                if (in_array($found['membre_bureau'], ['Président', 'Web Admin'], true)) {
                    include_once "commun/barre_navigation_pres.php";
                } else {
                    include_once "commun/barre_navigation_conn.php";
                }

            }
            /* 🔵 MEMBRE DE L’ASSOCIATION */
            elseif (!empty($found['membre_assoc'])) {

                if ($found['membre_assoc'] === "Étudiant/e") {
                    include_once "commun/barre_conn_etu.php";
                }
                elseif ($found['membre_assoc'] === "Alumni/e") {
                    include_once "commun/barre_conn_ancien.php";
                }
                else {
                    // Valeur métier inconnue
                    header("Location: /?dest=logout");
                    exit;
                }

            }
            /* ⚠️ ÉTAT IMPOSSIBLE */
            else {
                header("Location: /?dest=logout");
                exit;
            }
        }

        
    ?>



    <!-- ===================== -->
    <!-- HEADER -->
    <!-- ===================== -->
    <header class="eea-header">
        <h1>Association des anciens et étudiants d’EEEA</h1>
        <p>
        Université de Lille – Filière Électronique, Énergie Électrique et Automatique
        </p>
    </header>

  <!-- ===================== -->
  <!-- CONTENU PRINCIPAL -->
  <!-- ===================== -->
 
    <div class="eea-wrapper">

        <!-- PRESENTATION -->
        <section class="eea-presentation">
            <h2>Présentation de l’association</h2>

            <p>
                L’Association des anciens et étudiants d’EEEA de l’Université de Lille a été
                créée le <strong>6 mars 2023</strong>. Elle est née de la volonté de
                rapprocher les étudiants actuellement en formation et les anciens diplômés
                de la filière <strong>Électronique, Énergie Électrique et Automatique (EEEA)</strong>,
                en favorisant les échanges, le partage d’expériences et l’entraide entre les
                différentes promotions.
            </p>

            <p>
                L’initiative de cette association repose sur un constat simple : la richesse
                des parcours académiques et professionnels des anciens constitue une réelle
                valeur ajoutée pour les étudiants. L’association vise ainsi à créer un
                réseau actif permettant de transmettre des conseils, de partager des
                retours d’expérience et d’accompagner les étudiants dans leur orientation
                et leur insertion professionnelle.
            </p>

            <p>
                Le bureau de l’association est présidé par
                <strong>Thierry Communal</strong>, <strong>professeur agrégé</strong>, qui
                assure la coordination des activités et le suivi des actions menées au sein
                de l’association.
            </p>

            <p>
                L’association fonctionne également grâce à l’implication de
                <strong>plusieurs étudiants et anciens de la filière EEEA</strong>, engagés
                bénévolement dans différents rôles essentiels tels que la
                <strong>trésorerie</strong>, l’<strong>organisation des événements</strong>,
                la <strong>communication</strong> et la participation aux projets visant à
                renforcer la dynamique du réseau EEEA.
            </p>
        </section>

        <!-- OBJECTIFS -->
        <section class="eea-objectifs">
        <h2>Objectifs de l’association</h2>

        <p>
            L’association a pour mission principale de créer et de renforcer les liens
            entre les étudiants et les anciens de la filière EEEA. Elle cherche à
            favoriser un esprit de solidarité, de partage et de collaboration au sein
            de la communauté universitaire.
        </p>

        <p>
            À travers ses actions, l’association contribue à l’animation de la vie
            étudiante, au développement d’un réseau professionnel durable et à la
            valorisation des parcours et compétences des membres de la filière EEEA.
        </p>
        </section>

        <!-- ACTIVITES -->
        <section class="eea-activites">
        <h2>Activités</h2>

        <p>
            L’association organise régulièrement des événements visant à renforcer les
            échanges entre étudiants et anciens : rencontres conviviales, événements
            associatifs, moments de partage et actions favorisant la cohésion entre les
            différentes promotions.
        </p>

        <p>
            Elle participe également à la diffusion d’informations utiles concernant
            les parcours académiques, les opportunités professionnelles et la vie
            universitaire, contribuant ainsi à dynamiser la communauté EEEA.
        </p>
        </section>

        <!-- CONTACT -->
        <section class="eea-contact">
            <h2>Nous contacter</h2>

            <p>
                Étudiant(e) ou ancien(ne) de la filière EEEA, vous souhaitez rejoindre
                l’association ou participer à ses activités ?
                N’hésitez pas à nous contacter ou à suivre nos actualités via nos canaux de
                communication.
            </p>

            <p>
                <strong>Email :</strong> aaeeea@univ-lille.fr<br>
            </p>
            <div class="reseaux-sociaux">
                <p> <strong>Réseaux sociaux :</strong> <p>

                <div class="reseau_footer">
                    <a href="https://www.facebook.com/profile.php?viewas=100000686899395&id=100091473858926"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-facebook" viewBox="0 0 16 16">
                            <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                        </svg>
                    </a> 
                    <a href="https://www.instagram.com/association_eea_univ_de_lille/"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-instagram" viewBox="0 0 16 16">
                            <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                        </svg>
                    </a> 
                    <a href="https://www.linkedin.com/company/93664607/admin/dashboard/"> 
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-linkedin" viewBox="0 0 16 16">
                            <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854zm4.943 12.248V6.169H2.542v7.225zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248S2.4 3.226 2.4 3.934c0 .694.521 1.248 1.327 1.248zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016l.016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225z"/>
                        </svg>
                    </a> 
                </div>
            </div>

        </section>
    
    </div>


    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

    <?php require 'commun/footer.php';?>



</body>
</html>
