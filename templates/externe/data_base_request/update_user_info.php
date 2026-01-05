<?php
    /************************************************************
     *  API : Mise à jour des données utilisateur (AJAX)
     *  - Sécurité centralisée via init.php
     *  - Synchronisation session UNIQUEMENT si nécessaire (utile pour la modification de mail)
     ************************************************************/

    require_once "commun/init.php";
    header("Content-Type: application/json");

    /* =========================================================
    1️⃣ CONTRÔLE UTILISATEUR
    ========================================================= */

    // Utilisateur non connecté → refus
    if (!$user) {
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur non authentifié"
        ]);
        exit;
    }

    $id_user = $user['id_membre'];

    /* =========================================================
    2️⃣ LECTURE DES DONNÉES JSON
    ========================================================= */

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($data) ||
        empty($data['field']) ||
        !array_key_exists('value', $data)
    ) {
        echo json_encode([
            "success" => false,
            "message" => "Requête invalide"
        ]);
        exit;
    }

    $field = $data['field'];
    $value = trim($data['value']);

    /* =========================================================
    3️⃣ LISTE BLANCHE DES CHAMPS AUTORISÉS
    ========================================================= */

    $allowed = [
        "mail-input"        => "email",
        "mdp-inp"           => "mot_de_passe",
        "filiere-section"   => "section",
        "phone"             => "phone_number",
        "city-input"        => "ville",
        "profession-input"  => "metier"
    ];

    if (!isset($allowed[$field])) {
        echo json_encode([
            "success" => false,
            "message" => "Champ non autorisé"
        ]);
        exit;
    }

    $db_field = $allowed[$field];

    /* =========================================================
    4️⃣ TRAITEMENT SPÉCIFIQUE : MOT DE PASSE
    ========================================================= */

    if ($db_field === "mot_de_passe") {

        if (strlen($value) < 8) {
            echo json_encode([
                "success" => false,
                "message" => "Mot de passe trop court"
            ]);
            exit;
        }

        // Hash sécurisé avant stockage
        $value = password_hash($value, PASSWORD_DEFAULT);
    }

    /* =========================================================
    5️⃣ MISE À JOUR DE LA BASE DE DONNÉES
    ========================================================= */

    $result = EEA_Database::update_user_info(
        $id_user,
        $db_field,
        $value
    );

    /* =========================================================
    6️⃣ RÉPONSE + SYNCHRO SESSION (MINIMALE)
    ========================================================= */

    if ($result) {

        // 🔁 Synchronisation session UNIQUEMENT si le champ existe en session
        if ($db_field === 'email') {
            $_SESSION['user']['email'] = $value;
        }

        echo json_encode(["success" => true]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "Échec de la mise à jour"
    ]);
    exit;
?>