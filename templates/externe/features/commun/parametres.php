<?php require_once "commun/init.php" ?>
<!--
    Initialisation globale de l’application :
    - démarrage de la session
    - connexion à la base de données
    - récupération de l’utilisateur connecté dont le nom et prénom
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Paramétrage standard du document -->
    <meta charset="UTF-8"> <!-- Encodage UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->

    <title>Paramètres - Association EEA</title>

    <!-- Feuilles de style CSS -->
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/parameter_user.css">
    <link rel="stylesheet" href="public/css/switch_inp.css">
    <link rel="shortcut icon" href="public/pictures/logo_v8.jpeg">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">

    <!-- Polices Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Scripts externes - jQuery & Bootstrap -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>

    <!-- Scripts internes -->
   

    <!-- PHP : récupération des données utilisateur -->
    <?php
        /************************************************************
         *  RÉCUPÉRATION DES DONNÉES UTILISATEUR
         *  - init.php est déjà inclus
         *  - $user est valide (session)
         ************************************************************/

        // ID utilisateur depuis la session
        $id_member = $user['id_membre'];

        // Chargement depuis la base (données réelles)
        $found = EEA_Database::fetc_user_id($id_member);

        // Sécurité défensive
        if (!$found || !is_array($found)) {
            header("Location: /?dest=logout");
            exit;
        }

        /************************************************************
         *  DONNÉES UTILISATEUR (AFFICHAGE)
         ************************************************************/

        $nom_prenom = trim($found['prenom'] . ' ' . $found['nom']);

        /************************************************************
         *  RÉFÉRENTIEL DES SECTIONS
         ************************************************************/

        $sections_connues = [
            'L2-EEA', 'L3-EEA', 'L3-LIE',
            'M1-SE', 'M1-SA', 'M2-VIE', 'M2-SMaRT', 'M2-GR2E', 'M2-E2SD',
            'M1-RT', 'M1-SysCom', 'M1-NN',
            'M2-RT', 'M2-SysCom', 'M2-NN',
            'M1-GI', 'M2-GI'
        ];

        $section_db = trim($found['section'] ?? '');
        $is_autre   = !in_array($section_db, $sections_connues, true);

        /************************************************************
         *  CHOIX DE LA BARRE DE NAVIGATION
         *  ➜ basé sur la SESSION ($user)
         ************************************************************/

        // 🔵 Membre du bureau
        if (!empty($user['membre_bureau'])) {

            if (
                $user['membre_bureau'] === "Président" ||
                $user['membre_bureau'] === "Web Admin"
            ) {
                require "commun/barre_navigation_pres.php";
            } else {
                require "commun/barre_navigation_conn.php";
            }

        }
        // 🟢 Membre de l'association
        else {

            if ($user['membre_assoc'] === "Étudiant/e") {
                require "commun/barre_conn_etu.php";
            }
            elseif ($user['membre_assoc'] === "Alumni/e") {
                require "commun/barre_conn_ancien.php";
            }
            else {
                // État incohérent (sécurité défensive)
                header("Location: /?dest=logout");
                exit;
            }
        }
    ?>

    <!-- FORMULAIRE D'INSCRIPTION -->
    <form id="loginForm">
        
    

        <div class="container-formulaire" id="container-formulaire-id">

            <!-- SECTION INTRODUCTIVE -->
            <div class="descritpion-inscription">

                <div class="titre_h1">
                    <h1>📝 Modifiez vos informations</h1>
                </div>

                <div class="descirption-courte">
                    <p>Mettez simplement à jour vos informations personnelles afin de garder votre profil exact et à jour.</p>
                </div>

            </div>

            <!-- CONTENU DU FORMULAIRE -->
            <div class="formulaire-inscription" id="formulaire-id-inscription">


                <!-- Champ : Email -->
                <div class="zone-info">

                    <div class="formulaire-element" id="email-form">
                        <i class="bi bi-envelope"></i>

                        <div id="element-mail">
                            <div class="elem_mail_sub">
                                <label for="email">Votre mail personnel (pas celui de l'université de Lille)</label>

                                <input type="text" 
                                    class="email-input" 
                                    id="mail-input" 
                                    name="email"
                                    placeholder="<?= htmlspecialchars($found['email']) ?>">
                                <p id="p_mail"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Nouveau bouton -->
                    <div class="zone-button">
                        <button type="button" class="change-btn" data-target="mail-input" disabled>Modifier</button>
                    </div>
                </div>


                    <!-- Champ : Mot de passe -->
                    <div class="zone-info">

                        <div class="formulaire-element" id="password-form">
                            <div id="container_mdp">
                                <i class="bi bi-key"></i>

                                <div id="element-mdp">
                                    <label for="mot_de_passe">Mot de passe</label>
                                    <input type="password" class="mdp-input" id="mdp-inp" name="password"
                                        placeholder="Votre mot de passe">
                                </div>
                            </div>

                            <div id="det_mdp">
                                <p>Votre mot de passe doit contenir :</p>
                                <ul>
                                    <li id="rule_1">Au minimum 8 caractères</li>
                                    <li id="rule_2">Des chiffres</li>
                                    <li id="rule_3">Des lettres (minuscules et majuscules)</li>
                                    <li id="rule_4">Des caractères spéciaux (+,!,_,-,@) seulement</li>
                                </ul>
                            </div>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="mdp-inp" disabled>Modifier</button>
                        </div>
                    </div>


                    <!-- Champ : Membre de l'association -->
                    <div class="zone-info">
                        <div class="formulaire-element" id="membre-association">
                            <i class="bi bi-people-fill"></i>
                            <div id="membre-choice">
                                <label>Membre de l'association</label>
                                <select id="membre-assoc" name="membre_assoc" disabled>
                                    <option value="" disabled hidden>Sélectionner</option>


                                    <option value="Alumni/e" <?= ($found['membre_assoc'] === 'Alumni/e') ? 'selected' : '' ?>>
                                        Alumni/e
                                    </option>

                                    <option value="Étudiant/e" <?= ($found['membre_assoc'] === 'Étudiant/e') ? 'selected' : '' ?>>
                                        Étudiant/e
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>


                    <!-- Champ : Section EEA -->
                    <div class="zone-info">

                        <div class="formulaire-element" id="membre-section">
                            <i class="bi bi-mortarboard-fill"></i>
                            <div id="element-section">

                                <label for="section">Section EEA</label>

                               
                                <select id="filiere-section" name="section">
                                    <option value="" disabled hidden>Sélectionner</option>

                                    <option value="Autre" <?= $is_autre ? 'selected' : '' ?>>
                                        La section n'est pas mentionnée
                                    </option>

                                    <optgroup label="Licence">
                                        <option value="L2-EEA" <?= ($section_db === 'L2-EEA') ? 'selected' : '' ?>>Licence 2 EEA</option>
                                        <option value="L3-EEA" <?= ($section_db === 'L3-EEA') ? 'selected' : '' ?>>Licence 3 EEA</option>
                                        <option value="L3-LIE" <?= ($section_db === 'L3-LIE') ? 'selected' : '' ?>>Licence 3 IE</option>
                                    </optgroup>

                                    <optgroup label="Master ASE">
                                        <option value="M1-SE" <?= ($section_db === 'M1-SE') ? 'selected' : '' ?>>Master 1 SE</option>
                                        <option value="M1-SA" <?= ($section_db === 'M1-SA') ? 'selected' : '' ?>>Master 1 SA</option>
                                        <option value="M2-VIE" <?= ($section_db === 'M2-VIE') ? 'selected' : '' ?>>Master 2 VIE</option>
                                        <option value="M2-SMaRT" <?= ($section_db === 'M2-SMaRT') ? 'selected' : '' ?>>Master 2 SMaRT</option>
                                        <option value="M2-GR2E" <?= ($section_db === 'M2-GR2E') ? 'selected' : '' ?>>Master 2 GR2E</option>
                                        <option value="M2-E2SD" <?= ($section_db === 'M2-E2SD') ? 'selected' : '' ?>>Master 2 E2SD</option>
                                    </optgroup>

                                    <optgroup label="Réseaux et Télécoms">
                                        <option value="M1-RT" <?= ($section_db === 'M1-RT') ? 'selected' : '' ?>>Master 1 RT</option>
                                        <option value="M1-SysCom" <?= ($section_db === 'M1-SysCom') ? 'selected' : '' ?>>Master 1 SysCom</option>
                                        <option value="M1-NN" <?= ($section_db === 'M1-NN') ? 'selected' : '' ?>>Master 1 Nano-Technologie</option>
                                        <option value="M2-RT" <?= ($section_db === 'M2-RT') ? 'selected' : '' ?>>Master 2 RT</option>
                                        <option value="M2-SysCom" <?= ($section_db === 'M2-SysCom') ? 'selected' : '' ?>>Master 2 SysCom</option>
                                        <option value="M2-NN" <?= ($section_db === 'M2-NN') ? 'selected' : '' ?>>Master 2 Nano-Technologie</option>
                                    </optgroup>
                                </select>

                                <input type="text" id="autre-filiere" name="autre_fil"
                                    placeholder="Mettre votre ancienne filière" 
                                    value="<?= htmlspecialchars($is_autre ? $section_db : '')  ?>"
                                    <?=  !$is_autre ? 'disabled' : '' ?> >

                            </div>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="filiere-section" disabled>Modifier</button>
                        </div>
                    </div>


                    <!-- Champ : Numéro de téléphone -->
                    <div class="zone-info">

                        <div class="formulaire-element" id="tel-form">
                            <div id="container-phone">
                                <i class="bi bi-telephone"></i>
                                <div id="element-tel">
                                    <label for="tel">Numéro de téléphone : +33</label>

                                    <input id="phone" type="tel" class="form-control" name="phone"
                                        placeholder="<?php echo $found['phone_number']; ?>">
                                </div>
                            </div>

                        

                            <p id="bon_num">Vous n'avez pas mis le bon numéro</p>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="phone" disabled>Modifier</button>
                        </div>
                    </div>


                    <!-- Champ : Ville -->
                    <div class="zone-info">

                        <div class="formulaire-element" id="ville-form">
                            <i class="bi bi-building"></i>
                            <div id="element-ville">
                                <label>Ville (France)</label>
                                <input class="ville-input" id="city-input" name="country"
                                    placeholder="<?php echo $found['ville']; ?>">
                            </div>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="city-input" disabled>Modifier</button>
                        </div>
                    </div>


                    <!-- Champ : Profession -->
                    <div class="zone-info">

                        <div class="formulaire-element" id="profession-form">
                            <i class="bi bi-briefcase"></i>
                            <div id="element-profession">
                                <label>Profession</label>
                                <input class="prof-input" id="profession-input" name="profession"
                                    placeholder="<?php echo !empty($found['metier'])? $found['metier'] :  "Étudiant/e" ?>"
                                    <?= !empty($found['metier']) ? '' : 'disabled' ?>>
                            </div>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="profession-input" disabled>Modifier</button>
                        </div>
                    </div>

                

            </div> <!-- Fin formulaire-inscription -->

        </div>
        <input type="hidden" id="pikachu" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES) ?>">

    </form>
    <script src="public/js/switch_control.js?v=<?= filemtime('public/js/switch_control.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>
 


    <!-- Pied de page -->
    <?php require 'commun/footer.php'; ?>

</body>
</html>
