<?php require_once "commun/init.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter Évenements - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/evenement_add.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
   
   
    <?php
        /************************************************************
         *  PAGE : DÉPÔT D’ÉVÉNEMENT
         *  - init.php est déjà inclus
         *  - Accès réservé aux membres du bureau
         ************************************************************/

        /* =========================================================
        1️⃣ UTILISATEUR CONNECTÉ
        ========================================================= */

        if (!$user) {
            header("Location: /?dest=logout");
            exit;
        }

        /* =========================================================
        2️⃣ AUTORISATION : MEMBRE DU BUREAU UNIQUEMENT
        ========================================================= */

        if (empty($user['membre_bureau'])) {
            header("Location: /?dest=acceuil");
            exit;
        }

        /* =========================================================
        3️⃣ CHOIX DE LA BARRE DE NAVIGATION
        ========================================================= */

        if (
            $user['membre_bureau'] === "Président" ||
            $user['membre_bureau'] === "Web Admin"
        ) {
            require "commun/barre_navigation_pres.php";
        } else {
            require "commun/barre_navigation_conn.php";
        }
    ?>

    
    <div class="container-formulaire">
      
      <!-- Bandeau titre + description -->
      <div class="descritpion-evenement">
          <div class="titre_h1">
              <h1>Ajouter un événement</h1>
          </div>
          <div class="descirption-courte">
              <p>Ce formulaire vous permet d’ajouter un nouvel événement au sein de l’association.</p>
              <p>Veuillez renseigner le <strong>nom</strong>, la <strong>date</strong>, le <strong>lieu</strong>, ainsi qu’une <strong>description</strong> détaillée.</p>
              <p>Vous pouvez aussi choisir une <strong>catégorie</strong> (sport, culture, formation, etc.) pour mieux organiser les activités.</p>
          </div>
      </div>

      <!-- Formulaire -->
      <form id="formulaire-evenement">
        
          <!-- Nom de l'événement -->
          <div class="formulaire-element">
              <i class="fa fa-calendar"></i>
              <div>
                  <label for="nom-event">Nom de l'événement</label>
                  <input type="text" id="nom-event" name="nom_event" placeholder="Ex: Tournoi de football" required>
              </div>
          </div>

          <!-- Date de l'événement -->
            <div class="formulaire-element" id="event-date">
                <div class="form-element" id="naissance">
                    <i class="fa fa-clock"></i> <!-- Icône horloge -->
                    <div id="element-date">
                        <label for="date_event">Date de l'événement</label>
                        <input type="text" id="evenmt_date" placeholder="Date de l'événement" name="date" required>
                    </div>
                </div>
                <p class="form_annee" id="forme_id_anniv">Format de l'année de naissance : jj/mm/aaaa</p>
            </div>
        

          <!-- URL -->
          <div class="formulaire-element" id="formulaire-url">
             <i class="bi bi-browser-chrome"></i>
              <div>
                  <label for="url_form">Lien d'accès à l'évenement</label>
                  <input type="text" id="url-form" name="url_form" placeholder="Lien d'accès à l'évènement">
              </div>
          </div>

          <!-- Description -->
           <div class="formulaire-element" id="desc-evenmt">
                <div class="form-element" id="description">
                    <i class="fa fa-align-left"></i>
                    <div id="element-desc">
                        <label for="desc-event">Description de l'événement</label>
                        <textarea id="desc-event" name="desc_event" rows="3" placeholder="Décrivez brièvement l'événement..." maxlength="500" required></textarea>
                    </div>
                </div>
                <p id="char-count">0 / 500 caractères</p>
            </div>



          <!-- Bouton -->
          <div class="button_submit">
              <button type="submit" id="button_submit" disabled>Ajouter l'événement</button>
          </div>
          <div id="box_present"></div>
        
      </form>
      <div id="block_valid"></div>
      
    </div>


    <script src="public/js/update_add_event.js?v=<?= filemtime('public/js/update_add_event.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

    <?php require 'commun/footer.php';?>

</body>
</html>