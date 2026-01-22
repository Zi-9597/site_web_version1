<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Problème d’inscription</title>

    <!-- Styles -->
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/success.css">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<?php include_once 'commun/barre_navigation.php'; ?>
<script src="public/js/gestion_slidebar_1.js"></script>

<div class="main-content">
    <div>
        <div class="container-succ">
            <div class="success-box" style="border-color:#f50105;background-color :#f50105 ; opacity : 0.7;color : white;">
                <img src="https://cdn-icons-png.flaticon.com/512/1828/1828843.png"
                     alt="attention"
                     class="icon">
                <h2>Un problème a été rencontré</h2>
            </div>
        </div>

        <div class="list_avantage">
            <div class="mini-des">
                <p>
                    Oups 😕<br><br>

                    Il semble qu’un petit souci soit survenu lors de la validation de votre inscription à l’
                    <b>Association des Étudiants & Anciens (EEA) Lille</b>,
                    ou que le lien que vous avez utilisé ne soit plus valide.<br><br>

                    Pas d’inquiétude 😊<br>
                    Ce genre de situation peut arriver, et notre équipe est là pour vous accompagner.
                    N’hésitez pas à nous contacter, nous serons ravis de vous aider à finaliser votre inscription.
                </p>
            </div>
        </div>

        <div class="btn-container">
            <a href="/" class="btn btn-home">
                <i class="fas fa-home"></i> Retour à l’accueil
            </a>
            <a href="/?dest=contact_assoc" class="btn btn-login">
                <i class="fas fa-envelope"></i> Nous contacter
            </a>
        </div>
    </div>
</div>

<?php require 'commun/footer.php'; ?>

</body>
</html>
