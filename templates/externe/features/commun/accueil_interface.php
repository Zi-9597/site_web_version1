<?php require_once "commun/init.php"; ?>
<!--
    Initialisation globale de l’application :
    - démarrage de la session
    - connexion à la base de données
    - récupération de l’utilisateur connecté dont le nom et prénom
-->
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <!-- CHANGE: enables the responsive homepage layout on phones and tablets. -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil - Association EEA</title>
        <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
        <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
        <link rel="stylesheet" href="../../public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
        <link rel="stylesheet" href="../../public/css/logo_gestion.css">
        <link rel="stylesheet" href="../../public/css/presentation_acceuil.css?v=20251225_2">
        <!-- CHANGE: refonte visuelle de la page d'accueil sans modifier les classes existantes. -->
        <link rel="stylesheet" href="public/css/home_network.css?v=<?= filemtime('public/css/home_network.css') ?>">
        <link rel="stylesheet" href="../../public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
        <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
        
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        <link
            rel="stylesheet"
            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
            integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
            crossorigin="anonymous"
            referrerpolicy="no-referrer"
        />
    </head>

    <body>

        <?php
            // Choix de la barre de navigation
            if (!$user) {

                // Utilisateur non connecté
                require "commun/barre_navigation.php";

            } else {

                // Utilisateur connecté
                if (!empty($user["membre_bureau"])) {

                    // Membre du bureau
                    if ($user["membre_bureau"] === "Président" || $user["membre_bureau"] === "Web Admin") {
                        require "commun/barre_navigation_pres.php";
                    } else {
                        require "commun/barre_navigation_conn.php";
                    }

                } else {

                    // Membre de l'association
                    if ($user["membre_assoc"] === "Étudiant/e") {
                        require "commun/barre_conn_etu.php";
                    } elseif ($user["membre_assoc"] === "Alumni/e") {
                        require "commun/barre_conn_ancien.php";
                    } else {
                        require "commun/barre_navigation.php";
                    }
                }
            }
        ?>

        <!-- Contenu principal -->
        <?php require_once 'commun/acceuil_pres.php'; ?>

        <!-- Script JS -->
        <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

        <!-- Footer -->
        <?php require 'commun/footer.php'; ?>

    </body>
</html>
