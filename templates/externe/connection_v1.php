<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Association EEA</title>

    <!-- CSS EXISTANTS -->
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="public/css/connection_page_v1.css">

    <!-- POLICES -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <!-- NAVIGATION -->
    <?php require 'commun/barre_navigation.php'; ?>

    <!-- =========================
         CARTE ERREUR (cachée)
    ========================== -->
    <div id="error-card" class="error-card">
        <div class="error-icon">⚠️</div>
        <div class="error-content">
            <div class="error-title">Erreur de connexion</div>
            <div class="error-message">
                Email ou mot de passe incorrect.
            </div>
        </div>
    </div>

    <!-- =========================
         FORMULAIRE CONNEXION
    ========================== -->
    <form id="loginConn" method="post" novalidate>

        <div class="form_total">

            <div class="descritpion-inscription">
                <h1>
                    Connectez-vous à la page de l’association
                    des anciens et étudiants de l’EEA
                    avec votre adresse mail
                </h1>
            </div>

           <div class="form_connection">
                <div id="email">
                    <i class="bi bi-envelope"></i>
                    <div class="email-contain">
                        <label>Email :</label>
                        <input type="email" placeholder="Mettez votre adresse mail" class="form-control" id="email-fill" name="email">
                    </div>
                </div>

                <div id="mdp">
                    <i class="bi bi-key"></i>
                    <div class="mdp">
                        <label>Mot de passe :</label>
                        <input type="password" placeholder="Mettez votre mot de passe" class="form-control" id="mdp-fill" name="password">
                    </div>
                </div>
            </div>


            <!-- BOUTON -->
            <div class="button_submit">
                <button
                    type="submit"
                    id="button_submit"
                    class="button_click"
                    disabled
                >
                    Se connecter
                </button>
            </div>

        </div>
    </form>

    <!-- FOOTER -->
    <?php require 'commun/footer.php'; ?>

    <!-- JS -->
    <script src="public/js/gestion_slidebar_4.js"></script>
    <script src="public/js/connection_v1.js"></script>

</body>
</html>
