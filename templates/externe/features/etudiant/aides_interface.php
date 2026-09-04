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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'aides - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/aide_style.css?v=<?= filemtime('public/css/aide_style.css') ?>">
    <link rel="stylesheet" href="public/css/style_carte.css">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
   
    
    
   <?php
        /* ===============================
        ACCÈS : DEMANDE D’AIDE
        Règle :
        - uniquement étudiant connecté
        =============================== */

        $isStudent = false;

        // ❌ Non connecté → interdit
        if (!$user) {
            header("Location: /?dest=acceuil");
            exit;
        }

        // ✅ Connecté → vérifier rôle
        if (
            empty($user['membre_bureau']) &&
            ($user['membre_assoc'] ?? '') === 'Étudiant/e'
        ) {
            require "commun/barre_conn_etu.php";
            $isStudent = true;
        }
        // ❌ Autres rôles → interdit
        else {
            header("Location: /?dest=acceuil");
            exit;
        }
    ?>
    <!-- ============================
         🎓 PAGE AIDE AUX ÉTUDIANTS
    ============================== -->
    <div class="aide-page">

        <div class="container-title">
            <!-- ===== TITRE / BANDEAU ===== -->
            <div class="title-h1">
                <h1>Aide & Accompagnement Étudiants</h1>
            </div>

            <!-- ===== TEXTE INTRODUCTIF ===== -->
            <div class="aide-title-box">
                <p>
                    🤝 <strong>L’Association EEA est à vos côtés.</strong><br>
                    Ce formulaire a été mis en place pour vous offrir un espace d’écoute et
                    d’accompagnement face aux difficultés que vous pouvez rencontrer au cours
                    de votre parcours universitaire.
                    <br><br>

                    📚 <strong>Aide académique</strong> : difficultés dans certaines matières,
                    compréhension des cours, organisation des révisions.<br>
                    📝 <strong>Aide administrative</strong> : démarches universitaires,
                    dossiers, inscriptions, compréhension des procédures.<br>
                    💶 <strong>Aide financière</strong> : questions liées aux bourses,
                    aides existantes ou situations particulières.<br>
                    🧠 <strong>Soutien et organisation</strong> : gestion du stress,
                    surcharge de travail, besoin d’échanger ou d’être orienté.
                    <br><br>

                    🔒 <strong>Toutes les demandes sont strictement confidentielles.</strong><br>
                    Elles sont consultées uniquement par les membres du Bureau EEA,
                    dans un cadre bienveillant, respectueux et sans jugement.
                    <br><br>

                    💬 N’hésitez pas à vous exprimer librement. Chaque message est lu avec
                    attention et recevra une réponse adaptée.
                </p>
            </div>
        </div>

        <!-- ============================
             📝 FORMULAIRE D’AIDE
        ============================== -->
        <form class="aide-form" method="POST">



            <!-- Nom -->
            <div class="form-group">
                <label for="nom">Nom</label>
                <input
                    type="text"
                    name="nom"
                    id = "nom"
                    placeholder="Votre nom"
                    value="<?= $isStudent ? htmlspecialchars($user['nom'], ENT_QUOTES) : '' ?>"
                    <?= $isStudent ? 'readonly disabled' : 'required' ?>
                >

            </div>
             <!-- Prénom -->
            <div class="form-group">
                <label for="nom">Nom</label>
                <input
                    type="text"
                    name="nom"
                    id="prenom"
                    placeholder="Votre nom"
                    value="<?= $isStudent ? htmlspecialchars($user['prenom'], ENT_QUOTES) : '' ?>"
                    <?= $isStudent ? 'readonly disabled' : 'required' ?>
                >

            </div>
            <!-- Adresse e-mail -->
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Votre adresse e-mail personnelle"
                    value="<?= $isStudent ? htmlspecialchars($user['email'] ?? '', ENT_QUOTES) : '' ?>"
                    <?= $isStudent ? 'readonly disabled' : 'required' ?>
                >

                <?php if (!$isStudent): ?>
                    <p class="email-info" style="margin-top:10px; font-size:15px;">
                        ℹ️ Pour garantir un traitement confidentiel et indépendant,
                        les adresses e-mail se terminant par
                        <strong>@univ-lille.fr</strong> ne sont pas acceptées.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Numéro de téléphone (optionnel) -->
            <!-- Téléphone -->
            <div class="form-group">
                <label for="telephone">
                    Numéro de téléphone <span style="font-weight: normal;">(optionnel)</span>
                </label>
                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    placeholder="Ex : 06 12 34 56 78"
                    value="<?= $isStudent ? htmlspecialchars($user['phone_number'] ?? '', ENT_QUOTES) : '' ?>"
                >

                <small style="color:#666;">
                    📞 Utile si vous souhaitez être recontacté plus rapidement.
                </small>
            </div>

            <!-- Type d’aide -->
            <div class="form-group">
                <label for="type_aide">Type de demande</label>
                <select id="type_aide" name="type_aide" required>
                    <option value="">— Sélectionnez un type —</option>
                    <option value="1">Aide académique</option>
                    <option value="2">Aide administrative</option>
                    <option value="3">Aide financière</option>
                    <option value="4">Soutien & écoute</option>
                    <option value="5">Autre</option>
                </select>
            </div>

            <!-- Sujet -->
            <div class="form-group">
                <label for="sujet">Sujet</label>
                <input
                    type="text"
                    id="sujet"
                    name="sujet"
                    placeholder="Ex : Difficulté dans l'inscription"
                    maxlength="150"
                    required
                >
            </div>

            <!-- Message -->
            <div class="form-group">
                <label for="message">Votre message</label>
                <textarea
                    id="message"
                    name="message"
                    rows="6"
                    maxlength="2500"
                    placeholder="Expliquez votre situation en quelques lignes…"
                    required
                ></textarea>

                <!-- Indicateur de caractères -->
                <div class="char-counter">
                    0 / 2500 caractères
                </div>
            </div>

            <!-- Bouton d’envoi -->
            <div class="form-actions">
                <input type="hidden" id="pikachu" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">
                <button type="submit" disabled>Envoyer ma demande</button>
            </div>

        </form>
    </div>


    <!-- ================= NOTIFICATIONS ================= -->
    <div id="notif-success" class="notif-card success">
        ✅ Votre demande a bien été envoyée.<br>
        Le Bureau EEA vous répondra rapidement.
    </div>

    <div id="notif-error" class="notif-card error">
        ❌ Une erreur est survenue.<br>
        Merci de réessayer.
    </div>
    <script src="public/js/aide_demande_v2.js?v=<?= filemtime('public/js/aide_demande_v2.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>
    <?php require 'commun/footer.php';?>

</body>
</html>
