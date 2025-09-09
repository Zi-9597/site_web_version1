<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Définition du type de document et langage principal -->
    <meta charset="UTF-8"> <!-- Encodage des caractères en UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Permet de rendre le site responsive -->
    <title>Inscription - Association EEA</title> <!-- Titre de la page affiché dans l'onglet du navigateur -->

    <!-- Inclusion des feuilles de style pour la mise en forme -->
    <link rel="stylesheet" href="public/css/barre_navigation_1.css">
    <link rel="stylesheet" href="public/css/index.css">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/inscription_st_v2.css">
    <link rel="stylesheet" href="public/css/footer.css">

    <!-- Importation des polices depuis Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Inclusion de bibliothèques JavaScript (jQuery et Bootstrap) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

    <!-- Icônes Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <!-- Inclusion des scripts pour des fonctionnalités spécifiques -->
    <script src="public/js/gestion_slidebar_1.js"></script>
    <script src="public/js/inscription_v2.js"></script>

    <!-- Barre de navigation incluse depuis un fichier PHP -->
    <?php include_once 'commun/barre_navigation.php'; ?>

    <!-- Début du formulaire d'inscription -->
    <form id="loginForm" action="/?dest=add_subscriber" method="POST">
        <div class="container-formulaire" id="container-formulaire-id">
            <!-- Section de description -->
            <div class="descritpion-inscription">
                <div class="titre_h1">
                    <h1>Rejoignez l’association Étudiant-Anciens EEA et créez votre espace adhérent</h1>
                </div>
                <div class='descirption-courte'>
                    <p>En ouvrant votre compte <strong>Étudiant-Anciens EEA</strong>, vous accédez à de nombreux avantages :</p>
                    <ul>
                        <li>Accédez à des offres exclusives de stages, d’alternance, d’emplois et de jobs étudiants</li>
                        <li>Découvrez et participez aux événements organisés (activités, sorties, rencontres professionnelles...)</li>
                        <li>Bénéficiez de séances de révision collaboratives pour progresser plus efficacement</li>
                        <li>Revivez les moments forts de l’association à travers nos archives et galeries</li>
                    </ul>
                </div>
            </div>

            <!-- Début des champs du formulaire -->
            <div class="formulaire-inscription" id="formulaire-id-inscription">
                <!-- Sélection de la civilité -->
                <div class="formulaire-element" id="choix-genre">
                     <i class="bi bi-person"></i> <!-- Icône de civilité -->
                    <div id="element-genre">
                        <label for="genre">Civilité </label>
                        <select id="civilite-select" name="civil" required>
                            <option value="" selected disabled hidden>Sélectionner</option>
                            <option value="Madame">Madame</option>
                            <option value="Monsieur">Monsieur</option>
                        </select>
                    </div>
                </div>

                <!-- Champs pour le nom et le prénom -->
                <div class="formulaire-element" id="nom-prenom">
                    <i class="bi-emoji-smile-fill"></i> <!-- Icône sourire -->
                    <div class="form-element" id="nom">
                        <label for="nom">Votre nom</label>
                        <input type="text" id="last_name" name="nom" placeholder="Votre nom" required>
                    </div>
                    <div class="form-element" id="prenom">
                        <label for="prenom">Votre prénom</label>
                        <input type="text" id="name" placeholder="Votre prénom" name="prenom" required>
                    </div>
                </div>

                <!-- Champ pour la date de naissance -->
                <div class="formulaire-element" id="naissance-date">
                    <div class="form-element" id="naissance">
                        <i class="bi bi-cake2-fill"></i> <!-- Icône gâteau -->
                        <div id="element-naissance">
                            <label for="naissance">Année de Naissance</label>
                            <input type="text" id="birthday" placeholder="Votre année de naissance" name="date" required>
                        </div>
                    </div>
                    <p class="form_annee" id="forme_id_anniv">Format de l'année de naissance : jj/mm/aaaa</p>
                </div>

                <!-- Champ pour l'email -->
                <div class="formulaire-element" id="email-form">
                    <i class="bi bi-envelope"></i> <!-- Icône email -->
                    <div id="element-mail">
                        <label for="email">Votre mail personnel (pas celui de l'université de Lille)</label>
                        <input type="text" class="email-input" id="mail-input" name="email" placeholder="Votre mail personnel">
                        <p id="p_mail"></p> <!-- Message d'erreur affiché via JavaScript -->
                    </div>
                </div>

                <!-- Champ pour le mot de passe -->
                <div class="formulaire-element" id="password-form">
                    <div id="container_mdp">
                        <i class="bi bi-key"></i> <!-- Icône clé -->
                        <div id="element-mdp">
                            <label for="mot_de_passe">Mot de passe</label>
                            <input type="password" class="mdp-input" id="mdp-inp" name="password" placeholder="Votre mot de passe">
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

                <!-- Section pour le type de membre -->
                <div class="formulaire-element" id="membre-association">
                    <i class="bi bi-people-fill"></i> <!-- Icône groupe -->
                    <div id="membre-choice">
                        <label for="membre_association">Membre de l'association </label>
                        <select id="membre-assoc" name="membre_assoc">
                            <option value="" selected disabled hidden>Sélectionner</option>
                            <option value="Professeur/e">Professeur/e</option>
                            <option value="Alumni/e">Alumni/e</option>
                            <option value="Étudiant/e">Étudiant/e</option>
                            <option value="Alternant/e">Alternant/e</option>
                        </select>
                    </div>
                </div>

                <!-- Champ pour la section EEA -->
                <div class="formulaire-element" id="membre-section">
                    <i class="bi-mortarboard-fill" aria-hidden="true"></i> <!-- Icône chapeau de diplôme -->
                    <div id="element-section">
                        <label for="date-naissance">Section EEA</label>
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
                        </select>
                        <input type="text" id="autre-filiere" placeholder="Mettre votre ancienne filière" name="autre_fil" disabled>
                    </div>
                </div>

                <!-- Champ pour le numéro de téléphone -->
                <div class="formulaire-element" id="tel-form">
                    <div id="container-phone">
                        <i class="bi bi-telephone"></i> <!-- Icône téléphone -->
                        <div id="element-tel">
                            <label for="tel">Numéro de téléphone : +33</label>
                            <input id="phone" type="tel" class="form-control" name="phone" placeholder="Numéro de téléphone (France)">
                        </div>
                    </div>
                    <div id="check-phone">
                        <p>Si vous n'avez pas de numéro français</p>
                        <input type="checkbox" id="tel-available">
                    </div>
                    <p id="bon_num">Vous n'avez pas mis le bon numéro</p>
                </div>

                <!-- Champs pour le pays, la ville et la profession -->
                <div class="formulaire-element" id="pays-form">
                    <i class="bi bi-globe"></i>
                    <div id="element-tel">
                        <label for="pays">Pays d'origine </label>
                        <input class="country-input" id="country-born-input" name="city" placeholder="Votre pays d'origine">
                    </div>
                </div>
                <div class="formulaire-element" id="ville-form">
                    <i class="bi bi-building"></i>
                    <div id="element-ville">
                        <label for="ville">Ville (France) </label>
                        <input class="ville-input" id="city-input" name="country" placeholder="Ville d'habitation">
                    </div>
                </div>
                <div class="formulaire-element" id="profession-form">
                    <i class="bi bi-briefcase"></i>
                    <div id="element-profession">
                        <label for="profession">Profession</label>
                        <input class="prof-input" id="profession-input" name="profession" placeholder="Votre profession">
                    </div>
                </div>
                <input type="hidden" name="id_insc" value="<?php echo $_GET['id_insc']; ?>">
            </div>

            <!-- Bouton de soumission du formulaire -->
            <div class="button_submit">
                <button class="button_click" id="button_submit" type="submit" disabled>Validez votre inscription</button>
            </div>
        </div>
    </form>

    <!-- Inclusion du pied de page -->
    <?php require 'commun/footer.php'; ?>
</body>
</html>
