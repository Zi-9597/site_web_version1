
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Définition du type de document et langage principal -->
    <meta charset="UTF-8"> <!-- Encodage des caractères en UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Permet de rendre le site responsive -->
    <title>Validation Inscription</title> <!-- Titre de la page affiché dans l'onglet du navigateur -->

    <!-- Inclusion des feuilles de style pour la mise en forme -->
    <link rel="stylesheet" href="public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">

    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="public/css/success.css">
    <!-- Importation des polices depuis Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Inclusion de bibliothèques JavaScript (jQuery et Bootstrap) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
   

    <!-- Barre de navigation incluse depuis un fichier PHP -->
    <?php include_once 'commun/barre_navigation.php'; ?>
    <script src="public/js/gestion_slidebar_1.js"></script>




    <div class="main-content">
        <div>
            <div class="container-succ">
                <div class="success-box">
                    <img src="https://cdn-icons-png.flaticon.com/512/845/845646.png" alt="validé" class="icon">
                    <h2>Inscription réussie </h2>
                </div>
            </div>

            <div class="list_avantage">
                <div class="mini-des">
                    <p>
                        Merci d’avoir rejoint l’<b>Association des Étudiants & Anciens (EEA) Lille</b>.<br>
                        Vous êtes désormais membre officiel et vous bénéficiez des avantages suivants :
                    </p>
                </div>
                <ul>
                    <li>Un <b>réseau actif</b> d’étudiants et diplômés</li>
                    <li>Des <b>événements exclusifs</b> : conférences, afterworks, visites d’entreprises</li>
                    <li>Un <b>accompagnement carrière</b> : stages, alternances, premier emploi</li>
                    <li>Des <b>séances de révisions collectives</b> pour préparer vos examens</li>
                    <li>Des <b>réductions et avantages</b> sur certaines activités étudiantes</li>
                </ul>
            </div>

            <div class="btn-container">
                <a href="/" class="btn btn-home"><i class="fas fa-home"></i> Accueil</a>
                <a href="/?dest=connection" class="btn btn-login"><i class="fas fa-sign-in-alt"></i> Connexion</a>
            </div>
        </div>
    </div>

   
    <?php require 'commun/footer.php'; ?>
</body>
</html>
