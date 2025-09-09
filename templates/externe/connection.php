<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="public/css/connection_page.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
   
    <script src="public/js/gestion_slidebar_1.js"></script>
    <script src="public/js/connect_interface.js"></script>
    <?php require 'commun/barre_navigation.php'; ?>
    
    <form id="loginConn" action="/?dest=info_conn" method="POST">

        <div class="form_total">

            <div class="descritpion-inscription">
                <h1>Connectez vous à la page de l'association anciens et étudiants de l'EEA avec votre adresse mail</h1>
            </div>
        
            <div class="form_connection">
                <div id="email">
                    <i class="bi bi-envelope"></i>
                    <div class="email-contain">
                        <label for="email"> Email : </label>
                        <input type="email" class="form-control" id="email-fill"  name="email" placeholder="Votre adresse mail" required></br>
                    </div>
                </div>
               
                <div id="mdp">
                    <i class="bi bi-key"></i>
                    <div class="mdp">
                        <label for="Mot de passe"> Mot de passe : </label>
                        <input type="password" class="form-control" id="mdp-fill"  name="password" placeholder="Votre mot de passe" required></br>
                    </div>
                                
                </div>
            </div>

            <div class="button_submit">
                <button class="button_click" id="button_submit" type="submit" disabled>Connectez vous</button>
            </div>



        </div>
    </form>


    <?php require 'commun/footer.php';?>

</body>
</html>