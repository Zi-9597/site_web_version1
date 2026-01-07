<?php include_once "commun/init.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentions Légales - Association EEEA</title>
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
        <h1>Mentions légales</h1>
        <p>
            Association des anciens et étudiants d’EEEA – Université de Lille
        </p>
    </header>

  <!-- ===================== -->
  <!-- CONTENU PRINCIPAL -->
  <!-- ===================== -->
 
    <div class="eea-wrapper">

        <!-- PRESENTATION -->
        <section class="eea-presentation">
            <h2>Éditeur du site</h2>

            <p>
                Le présent site est édité par l’<strong>Association des anciens et étudiants d’EEEA</strong>,
                association à but non lucratif.
            </p>

            <p>
                <strong>Adresse :</strong><br>
                35 rue Ronsard<br>
                59147 Gondecourt – France
            </p>

            <p>
                <strong>Email :</strong> aaeeea@univ-lille.fr
            </p>
        </section>


        <!-- OBJECTIFS -->
        <section class="eea-objectifs">
            <h2>Responsable de la publication</h2>

            <p>
                <strong>Association des anciens et étudiants d’EEEA de l’Université de Lille</strong><br>
                Association à but non lucratif régie par la loi 1901.
            </p>

            <p>
                La responsabilité éditoriale du site est assurée collectivement par le bureau de
                l’association, dans le cadre de ses activités associatives et pédagogiques.
            </p>
        </section>

        <!-- ACTIVITES -->
       <section class="eea-activites">
            <h2>Hébergement du site</h2>

            <p>
                <strong>Université de Lille</strong><br>
                Direction Déléguée au Développement du Numérique<br>
                Domaine universitaire du <em>Pont de Bois</em><br>
                Rue du Barreau – BP 60149<br>
                59653 Villeneuve d’Ascq – France
            </p>
        </section>


        <!-- CONTACT -->
        <section class="eea-activites">
            <h2>Liens hypertextes</h2>

            <p>
                Le site peut inclure des liens vers d’autres sites Web ou d’autres sources Internet.
                Dans la mesure où l’Université de Lille ne peut contrôler ces sites et ces sources
                externes, elle ne peut être tenue pour responsable de la mise à disposition de ces
                sites et sources externes.
            </p>

            <p>
                Elle ne peut supporter aucune responsabilité quant au contenu, publicités,
                produits, services ou tout autre matériel disponible sur ou à partir de ces sites
                ou sources externes.
            </p>

            <p>
                De plus, l’Asscoiation EEEA ne pourra être tenue responsable de tous dommages
                ou pertes avérés ou allégués consécutifs ou en relation avec l’utilisation ou le fait
                d’avoir fait confiance au contenu, aux biens ou aux services disponibles sur ces
                sites ou sources externes.
            </p>
        </section>

        <section class="eea-activites">
            <h2>Droits d’auteur et propriété intellectuelle</h2>

            <p>
                L’ensemble de ce site relève de la législation française et internationale sur le
                droit d’auteur et la propriété intellectuelle. Tous les droits de reproduction sont
                réservés.
            </p>

            <p>
                La reproduction de tout ou partie de ce site sur un support électronique quel qu’il
                soit est formellement interdite sauf autorisation expresse du directeur de la
                publication.
            </p>

            <p>
                Les photographies, textes, dessins, images et autres éléments protégés par les droits
                de la propriété intellectuelle sont la propriété de l’Université de Lille ou de tiers
                ayant autorisé leur utilisation.
            </p>
        </section>

        <section class="eea-activites">
            <h2>Avertissement concernant les informations disponibles sur ce site</h2>

            <p>
                L’Association EEEA Ancien et Étudiant s’efforce d’assurer au mieux l’exactitude et la mise à jour des
                informations diffusées sur ce site, et se réserve le droit de corriger, à tout moment
                et sans préavis, le contenu.
            </p>

            <p>
                Toutefois, elle ne peut garantir l’exactitude, la précision ou l’exhaustivité des
                informations mises à disposition sur ce site.
            </p>

            <ul class="legal-list">
                <li>pour toute interruption du site ;</li>
                <li>survenance de bogues ;</li>
                <li>pour toute inexactitude ou omission portant sur des informations disponibles ;</li>
                <li>pour tous dommages résultant d’une intrusion frauduleuse d’un tiers.</li>
            </ul>

        </section>

        <section class="eea-cookies">
            <h2>Cookies et données personnelles</h2>

            <p>
                Le site de l’Association des anciens et étudiants EEEA de l’Université de Lille
                utilise uniquement des <strong>cookies techniques strictement nécessaires</strong>
                à son bon fonctionnement.
            </p>

            <p> 
                Le site de l'Université de Lille utilise uniquement des cookies/traceurs à des fins de gestion de session, personnalisation (contraste..) 
                et pour la réalisation de mesures d'audience internes et anonymes (adresses IP anonymisées).
            </p>
            <p>
                Aucun cookie de suivi, de mesure d’audience ou de publicité n’est utilisé sur ce site.
                Aucune donnée personnelle n’est collectée à des fins commerciales ou publicitaires.
            </p>

            <p>
                Les données personnelles éventuellement collectées sur ce site
                (nom, prénom, adresse email, informations de profil)
                sont utilisées exclusivement dans le cadre des activités de l’association.
            </p>
            <p>
                Toute demande de suppression de données peut être effectuée
                par simple email à l’adresse suivante :
                <strong>aaeeea@univ-lille.fr</strong>.
            </p>

            <p>
                Les données seront supprimées dans un délai raisonnable,
                sous réserve des obligations légales ou associatives en vigueur.
            </p>
        </section>
    
    </div>


    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

    <?php require 'commun/footer.php';?>



</body>
</html>
