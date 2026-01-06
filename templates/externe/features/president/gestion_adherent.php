<?php require_once "commun/init.php" ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Changement des membres - Association EEA</title>
    <link rel="stylesheet" href="public/css/barre_navigation_v2.css?v=<?= filemtime('public/css/barre_navigation_v2.css') ?>">
    <link rel="stylesheet" href="public/css/index.css?v=<?= filemtime('public/css/index.css') ?>">
    <link rel="stylesheet" href="public/css/logo_gestion.css">
    <link rel="stylesheet" href="public/css/footer.css?v=<?= filemtime('public/css/footer.css') ?>">
    <link rel="stylesheet" href="public/css/change_statut.css?v=<?= filemtime('public/css/change_statut.css') ?>">
    <link rel="stylesheet" href="public/css/modal.css">
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
        /* ============================================================================
        📌 PAGE RÉSERVÉE AU PRÉSIDENT
        - init.php déjà inclus
        - Politique stricte : tout accès non autorisé = LOGOUT
        ============================================================================ */

        /* ============================================================
        🔐 1) UTILISATEUR OBLIGATOIREMENT CONNECTÉ
        (init.php garantit déjà la structure, on vérifie juste)
        ============================================================ */

        if (!$user) {
            header("Location: /?dest=logout");
            exit;
        }

        /* ============================================================
        🔎 2) VALIDATION EN BASE (source de vérité)
        ============================================================ */

        $found = EEA_Database::fetc_user_id($user['id_membre']);

        if (!$found || !is_array($found)) {
            header("Location: /?dest=logout");
            exit;
        }

        /* ============================================================
        🚫 3) AUTORISATION STRICTE : PRÉSIDENT / WEB ADMIN UNIQUEMENT
        ============================================================ */

        if (
            empty($found['membre_bureau']) ||
            !in_array($found['membre_bureau'], ['Président', 'Web Admin'], true)
        ) {
            // Politique radicale : état interdit = déconnexion
            header("Location: /?dest=logout");
            exit;
        }

        /* ============================================================
        ✅ 4) ACCÈS AUTORISÉ
        ============================================================ */

        // Données utiles
        $nom_prenom = trim($found['prenom'] . ' ' . $found['nom']);

        // Données métier
        $members = EEA_Database::fetchAllMembers();

        // Navigation président
        require_once "commun/barre_navigation_pres.php";   
    ?>


    <!-- Titre de la page -->
    <div class="title-box">
        <h1>Liste des Membres</h1>
    </div>

    
    <!-- Bloc des filtres -->
   <!-- ===== BLOC GLOBAL : FILTRES + ACTION ===== -->
    <div class="box-with-title">
        <span class="box-title">Gestion des Membres</span>

        <!-- ==== PARTIE FILTRE ==== -->
        <div class="filtre-membre">
            <span class="titre-fm">Filtre Membre</span>

            <div class="filtre-membre-grid">

                <!-- Prénom -->
                <div class="filter-item">
                    <label for="search-prenom">Prénom :</label>
                    <input type="text" id="search-prenom" placeholder="Rechercher un prénom...">
                </div>

                <!-- Nom -->
                <div class="filter-item">
                    <label for="search-nom">Nom :</label>
                    <input type="text" id="search-nom" placeholder="Rechercher un nom...">
                </div>

                <!-- Section -->
                <div class="filter-item">
                    <label for="filiere-section">Section :</label>
                    <select id="filiere-section">
                        <option value="">Toutes les sections</option>
                        <option value="Autre">La section n'est pas mentionnée</option>

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
                            <option value="M1-NN">Master 1 Nano-Tech</option>
                            <option value="M2-RT">Master 2 RT</option>
                            <option value="M2-SysCom">Master 2 SysCom</option>
                            <option value="M2-NN">Master 2 Nano-Tech</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Membre Associé -->
                <div class="filter-item">
                    <label for="membre-assoc">Membre Associé :</label>
                    <select id="membre-assoc">
                        <option value="">Tous</option>
                        <option value="Alumni/e">Alumni/e</option>
                        <option value="Étudiant/e">Étudiant/e</option>
                    </select>
                </div>

               

                <!-- Ville -->
                <div class="filter-item">
                    <label for="search-ville">Ville :</label>
                    <input type="text" id="search-ville" placeholder="Rechercher une ville...">
                </div>

              
                <!-- Switch Membre Bureau -->
                <div class="filter-item">
                    <label>Membre du Bureau :</label>
                    <label class="switch">
                        <input type="checkbox" id="bureau-switch">
                        <span class="slider"></span>
                    </label>
                </div>

            </div>
        </div>

        <!-- 🔹 Ligne du bouton -->
        <div class="action-box-single">
            <span class="box-action">Changer le status des M2</span>
            <div class="filter-item" style="display: flex; flex-direction : row; gap : 20px; align-items :center;">
                <label>Filtrer que les M2 pour les mettres anciens :</label>
                <label class="switch">
                    <input type="checkbox" id="m2-switch">
                    <span class="slider"></span>
                </label>
            </div>
        </div>
    </div>

    <!-- Début du tableau -->
    <div class="total_information">

        <table id="table-membres">

            <!-- En-tête du tableau -->
            <thead>
                <tr>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Section</th>
                    <th>Membre Assoc</th>
                    <th>Membre Bureau</th>
                    <th>Mail</th>
                    <th>Téléphone</th>
                    <th>Ville</th>
                    <th>Métier</th>
                    <th>Date d'inscription</th>
                    
                    <!-- Colonne changement (cachée par défaut) -->
                    <th class="col-change">Changement</th>
                    <th class="col-change">Mettre Ancien</th>
                </tr>
            </thead>

            <!-- Corps du tableau -->
            <tbody>

                <!-- Boucle pour afficher chaque membre dans une ligne -->
                <?php foreach ($members as $m): ?>
                    <?php
                        // ===============================
                        // LOGIQUE MÉTIER (CLAIRE)
                        // ===============================
                        $isEtudiant = ($m['membre_assoc'] === 'Étudiant/e');
                        $isM2       = (stripos($m['section'], 'M2') !== false);
                        $canBeAncien = $isEtudiant && $isM2;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($m["prenom"]) ?></td>
                        <td><?= htmlspecialchars($m["nom"]) ?></td>
                        <td><?= htmlspecialchars($m["section"]) ?></td>
                        <td><?= htmlspecialchars($m["membre_assoc"]) ?></td>
                        <td><?= htmlspecialchars($m["membre_bureau"]) ?></td>
                        <td><?= htmlspecialchars($m["email"]) ?></td>
                        <td><?= htmlspecialchars($m["phone_number"]) ?></td>
                        <td><?= htmlspecialchars($m["ville"]) ?></td>
                        <td><?= htmlspecialchars($m["metier"]) ?></td>
                        <td><?= date("d/m/Y", strtotime($m["date_inscription"])) ?></td>

                        <td class="col-change">
                            <button 
                                class="btn-change" 
                                data-id="<?= htmlspecialchars($m["id_membre"]) ?>"
                            >
                                ✏️ Changer
                            </button>
                        </td>

                         <!-- Bouton Mettre Ancien -->
                        <td class="col-change">
                            <?php if ($canBeAncien): ?>
                                <button
                                    class="btn-make-ancien"
                                    data-id="<?= htmlspecialchars($m['id_membre']) ?>"
                                    title="Passer ce membre au statut Ancien"
                                >
                                    🎓 
                                </button>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
        <div id="no-result" class="no-result">
            Aucun résultat trouvé
        </div>


    </div>
   
    <!-- ============================
        🟣 MODAL : MODIFIER UN MEMBRE
    ============================== -->
    <div id="modal-edit" class="modal-overlay">

        <div class="modal-content">

            <!-- Bandeau violet -->
            <div class="modal-header">
                <h2>Modifier le Membre</h2>
            </div>

            <!-- Corps du modal -->
            <div class="modal-body">

                <!-- ID du membre (caché) -->
                <input type="hidden" id="edit-id">

                <!-- Champ : Prénom -->
                <div class="modal-field">
                    <label for="edit-prenom">Prénom :</label>
                    <input type="text" id="edit-prenom">
                </div>

                <!-- Champ : Nom -->
                <div class="modal-field">
                    <label for="edit-nom">Nom :</label>
                    <input type="text" id="edit-nom">
                </div>

                <!-- Champ : Section (SELECT amélioré) -->
                <div class="modal-field">
                    <label for="edit-section">Section :</label>
                    <select id="edit-section">

                        <option value="">Toutes les sections</option>
                        <option value="Autre">La section n'est pas mentionnée</option>

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
                            <option value="M1-NN">Master 1 Nano-Tech</option>
                            <option value="M2-RT">Master 2 RT</option>
                            <option value="M2-SysCom">Master 2 SysCom</option>
                            <option value="M2-NN">Master 2 Nano-Tech</option>
                        </optgroup>

                    </select>
                </div>

                <!-- Champ : Membre Associé -->
                <div class="modal-field">
                    <label>Membre Associé :</label>
                    <select id="edit-assoc"> 
                        <option value="Alumni/e">Alumni/e</option>
                        <option value="Étudiant/e">Étudiant/e</option>
                    </select>
                </div>


                <!-- Champ : Membre du Bureau -->
                <div class="modal-field">
                    <label for="edit-bureau">Membre du Bureau :</label>
                    <input type="text" id="edit-bureau">
                </div>

                <!-- Champ : Email -->
                <div class="modal-field">
                    <label for="edit-email">Email :</label>
                    <input type="email" id="edit-email">
                </div>

                <!-- Champ : Téléphone -->
                <div class="modal-field">
                    <label for="edit-phone">Téléphone :</label>
                    <input type="text" id="edit-phone">
                </div>

                <!-- Champ : Ville -->
                <div class="modal-field">
                    <label for="edit-ville">Ville :</label>
                    <input type="text" id="edit-ville">
                </div>

                <!-- Champ : Métier -->
                <div class="modal-field">
                    <label for="edit-metier">Métier :</label>
                    <input type="text" id="edit-metier">
                </div>

            </div>

            <!-- Pied du modal -->
            <div class="modal-footer">
                <button class="modal-btn-cancel" id="modal-btn-cancel">Annuler</button>
                <button class="modal-btn-save">Valider</button>
            </div>

        </div>
    </div>
    <input type="hidden"  id="pikachu_csrf" value=<?= htmlspecialchars($_SESSION["csrf_token"]) ?> >
    <div id="update-message" class="update-msg"></div>

    
    <?php require 'commun/footer.php';?>
    <script src="public/js/membres.js?v=<?= filemtime('public/js/membres.js') ?>"></script>
    <script src="public/js/gestion_slide_bar_4.js?v=<?= filemtime('public/js/gestion_slide_bar_4.js') ?>"></script>

</body>
</html>