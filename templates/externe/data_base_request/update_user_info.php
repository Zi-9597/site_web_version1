<?php
/************************************************************
 *  API : Mise à jour des données utilisateur (AJAX)
 *
 *  Sécurité :
 *  - Utilisateur obligatoirement connecté
 *  - Méthode POST uniquement
 *  - Données JSON uniquement
 *  IMPORTANT :
 *  Le token CSRF n’est PAS généré ici.
 *  Il est généré à la connexion utilisateur.
 ************************************************************/

    require_once "commun/init.php";
    header("Content-Type: application/json");

    /* =========================================================
    1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
    ========================================================= */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    /* =========================================================
    2️⃣ CONTRÔLE DE LA SESSION UTILISATEUR
    ========================================================= */

    // L'utilisateur doit être connecté
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur non authentifié"
        ]);
        exit;
    }

    $id_user = $user['id_membre'];

    /* =========================================================
    3️⃣ RÉCUPÉRATION DES DONNÉES JSON
    ========================================================= */

    $data = json_decode(file_get_contents("php://input"), true);

    // Vérification structure minimale
    if (
        !is_array($data) ||
        empty($data['field']) ||
        !array_key_exists('value', $data) ||
        empty($data['pikachu_csrf'])
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Données invalides"
        ]);
        exit;
    }

    $field        = $data['field'];
    $value        = trim($data['value']);
    $csrf_client  = $data['pikachu_csrf'];

    /* =========================================================
    4️⃣ VÉRIFICATION CSRF
    =========================================================
    - Le token a été généré à la connexion
    - Il est stocké en session
    - Le client DOIT renvoyer le même token
    ========================================================= */

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $csrf_client)
    ) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "CSRF invalide"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ LISTE BLANCHE DES CHAMPS AUTORISÉS
    =========================================================
    IMPORTANT :
    - Le client envoie un ID HTML
    - On mappe vers un champ DB autorisé
    - Toute tentative hors whitelist est bloquée
    ========================================================= */

    $allowed_fields = [
        "mail-input"        => "email",
        "mdp-inp"           => "mot_de_passe",
        "filiere-section"   => "section",
        "phone"             => "phone_number",
        "city-input"        => "ville",
        "profession-input"  => "metier"
    ];

    if (!isset($allowed_fields[$field])) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Champ non autorisé"
        ]);
        exit;
    }

    $db_field = $allowed_fields[$field];

    /* =========================================================
    6️⃣ VALIDATIONS SPÉCIFIQUES PAR CHAMP
    ========================================================= */

    if ($db_field === "email") {

        // Email valide
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                "success" => false,
                "message" => "Email invalide"
            ]);
            exit;
        }

        // Interdiction email institutionnel
        if (str_ends_with(strtolower($value), "@univ-lille.fr")) {
            echo json_encode([
                "success" => false,
                "message" => "Email non autorisé"
            ]);
            exit;
        }
    }

    if ($db_field === "mot_de_passe") {

        // Longueur minimale
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
    7️⃣ MISE À JOUR EN BASE DE DONNÉES
    ========================================================= */

    try {

        $result = EEA_Database::update_user_info(
            $id_user,
            $db_field,
            $value
        );

        if (!$result) {
            echo json_encode([
                "success" => false,
                "message" => "Échec de la mise à jour"
            ]);
            exit;
        }

        /* =====================================================
        8️⃣ SYNCHRONISATION SESSION (MINIMALE)
        =====================================================
        On met à jour la session UNIQUEMENT
        si le champ modifié est utilisé en session
        ===================================================== */

        if ($db_field === "email") {
            $_SESSION['user']['email'] = $value;
        }

        /* =====================================================
        9️⃣ ROTATION DU TOKEN CSRF
        =====================================================
        Bonne pratique :
        - Empêche la réutilisation d’un token intercepté
        - Le nouveau token sera disponible après reload
        ===================================================== */

        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));

        echo json_encode(["success" => true]);
        exit;

    } catch (Throwable $e) {

        // Ne jamais exposer les erreurs internes
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>