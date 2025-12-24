<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'aides - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css">
    <link rel="stylesheet" href="public/css/aide_style.css">
    <link rel="stylesheet" href="public/css/style_carte.css">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    
   
    
    <?php
    // Simple test to display "ancien" on the page
        require_once "require_db.php";


        $id_comb = $_GET["id_user"]; 
        list($id_member , $id_num ) = explode("_" , $id_comb ); 
    

        $found = EEA_Database::fetc_user_id($id_member);

    
        $nom_prenom = $found["prenom"]." ".$found["nom"];

        $mail_user = $found["email"];
        $phone_num = $found["phone_number"];
        include "commun/barre_conn_etu.php";
       

        
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
                    type="input"
                    id="nom"
                    name="nom"
                    placeholder="Votre nom"
                    required
                    value="<?= htmlspecialchars($found["nom"] ?? '', ENT_QUOTES) ?>"
                    disabled
                    readonly
                >
            </div>
             <!-- Prénom -->
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input
                    type="input"
                    id="prenom"
                    name="prenom"
                    placeholder="Votre prénom"
                    value="<?= htmlspecialchars($found["prenom"] ?? '', ENT_QUOTES) ?>"
                    disabled
                    readonly
                >
            </div>
            <!-- Adresse e-mail -->
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Ton adresse mail personnelle"
                    value="<?= htmlspecialchars($mail_user ?? '', ENT_QUOTES) ?>"
                    disabled
                    readonly
                >

                <p class="email-info" style="margin-top:10px; font-size:15px;">
                    ℹ️ Pour garantir un traitement confidentiel et indépendant,
                    les adresses e-mail se terminant par
                    <strong>@univ-lille.fr</strong> ne sont pas acceptées.
                </p>
            </div>

            <!-- Numéro de téléphone (optionnel) -->
            <div class="form-group">
                <label for="telephone">
                    Numéro de téléphone <span style="font-weight: normal;">(optionnel)</span>
                </label>
                <input
                    type="tel"
                    id="telephone"
                    name="telephone"
                    value="<?= htmlspecialchars($phone_num ?? '', ENT_QUOTES) ?>"
                    disabled
                    readonly
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
    <script src="public/js/aide_demande.js"></script>
    <script src="public/js/gestion_slide_bar_4.js"></script>
    <?php require 'commun/footer.php';?>

</body>
</html>