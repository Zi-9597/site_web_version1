<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Paramétrage standard du document -->
    <meta charset="UTF-8"> <!-- Encodage UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive -->

    <title>Paramètres</title>

    <!-- Feuilles de style CSS -->
    <link rel="stylesheet" href="public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/parameter_user.css">
    <link rel="stylesheet" href="public/css/switch_inp.css">
    <link rel="stylesheet" href="public/css/footer.css">

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
    <script src="public/js/changement_information.js"></script>
    <script src="public/js/gestion_slidebar_1.js"></script>

    <!-- PHP : récupération des données utilisateur -->
    <?php
        require_once "require_db.php";

        // Récupération de l'identifiant dans l'URL
        $id_comb = $_GET["id_user"];
        list($id_member, $id_num) = explode("_", $id_comb);

        // Recherche utilisateur dans la base
        $found = EEA_Database::fetc_user_id($id_member);

        // Formatage du nom complet
        $nom_prenom = $found["prenom"] . " " . $found["nom"];

        // Inclus la barre de navigation
        include "commun/barre_navigation_conn.php";
    ?>

    <!-- FORMULAIRE D'INSCRIPTION -->
    <form id="loginForm" action="/?dest=add_subscriber" method="POST">

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
                                    placeholder="<?php echo $found['email']; ?>">
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
                                <select id="membre-assoc" name="membre_assoc">
                                    <option value="" selected disabled hidden>Sélectionner</option>
                                    <option value="Professeur/e">Professeur/e</option>
                                    <option value="Alumni/e">Alumni/e</option>
                                    <option value="Étudiant/e">Étudiant/e</option>
                                    <option value="Alternant/e">Alternant/e</option>
                                </select>
                            </div>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="membre-assoc" disabled>Modifier</button>
                        </div>
                    </div>


                    <!-- Champ : Section EEA -->
                    <div class="zone-info">

                        <div class="formulaire-element" id="membre-section">
                            <i class="bi bi-mortarboard-fill"></i>
                            <div id="element-section">

                                <label for="section">Section EEA</label>

                               
                                <select id="filiere-section" name="section">
                                    <option value="" selected disabled hidden>Sélectionner</option>
                                    <option value="Autre" selected>La section n'est pas mentionnée</option>
                                    <!-- Liste des options -->
                                    <optgroup label="Licence">
                                        <option value="L2-EEA">Licence 2 EEA</option>
                                        <option value="L3-EEA">Licence 3 EEA</option>
                                        <option value="L3-LIE">Licence 3 IE</option>
                                    </optgroup>
                                    <optgroup label="Master ASE">
                                        <option value="M1-SE">Master 1 SE</option>
                                        <option value="M1-SA">Master 1 SA</option>
                                        <option value="M2-VIE">Master 2 VIE</option>
                                        <option value="M2-SMaRT">Master 2 SMaRT</option>
                                        <option value="M2-GR2E">Master 2 GR2E</option>
                                        <option value="M2-E2SD">Master 2 E2SD</option>
                                    </optgroup>
                                    <optgroup label="Master Génie Industrie">
                                        <option value="M1-GI">Master 1 GI</option>
                                        <option value="M2-GI">Master 2 GI</option>
                                    </optgroup>
                                    <optgroup label="Réseaux et Télécoms">
                                        <option value="M1-RT">Master 1 RT</option>
                                        <option value="M1-SysCom">Master 1 SysCom</option>
                                        <option value="M1-NN">Master 1 Nano-Technologie</option>
                                        <option value="M2-RT">Master 2 RT</option>
                                        <option value="M2-SysCom">Master 2 SysCom</option>
                                        <option value="M2-NN">Master 2 Nano-Technologie</option>
                                    </optgroup>
                                    <!-- Ajoutez ici les autres sections -->
                                    

                                    <!-- Toutes tes options... -->
                                </select>

                                <input type="text" id="autre-filiere" name="autre_fil"
                                    placeholder="Mettre votre ancienne filière" disabled>

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
                                    placeholder="<?php echo $found['metier']; ?>">
                            </div>
                        </div>

                        <!-- Nouveau bouton -->
                        <div class="zone-button">
                            <button type="button" class="change-btn" data-target="profession-input" disabled>Modifier</button>
                        </div>
                    </div>

                

            </div> <!-- Fin formulaire-inscription -->

        </div>

    </form>
    <script src="public/js/switch_control.js"></script>

    <!-- Pied de page -->
    <?php require 'commun/footer.php'; ?>

</body>
</html>
