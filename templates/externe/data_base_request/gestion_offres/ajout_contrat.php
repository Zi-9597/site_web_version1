<?php
    /************************************************************
     *  CONTROLLER : AJOUT D’UNE OFFRE D’EMPLOI (AJAX)
     *
     *  Sécurité :
     *  - Utilisateur connecté obligatoire
     *  - Méthode POST uniquement
     *  - Données issues d’un formulaire (FormData)
     *  - Protection CSRF (token généré à la connexion)
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json; charset=utf-8');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    if (!$user) {
        http_response_code(401);
        echo json_encode([
            "success" => false,
            "message" => "Utilisateur non authentifié"
        ]);
        exit;
    }

    /* =========================================================
    2️⃣ MÉTHODE HTTP
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
    3️⃣ PROTECTION CSRF
    =========================================================
    - Le token est généré à la connexion
    - Il est stocké en session
    - Le formulaire DOIT renvoyer le même token
    ========================================================= */

    $csrf_client = $_POST['pikachu_csrf'] ?? '';

    if (
        empty($csrf_client) ||
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
    4️⃣ DONNÉES FORMULAIRE
    ========================================================= */

    $titre        = trim($_POST['titre_offre'] ?? '');
    $linkedin     = trim($_POST['linkedin'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $type_contrat = trim($_POST['types'] ?? 'Job Étudiant');
    $specialites  = !empty($_POST['specialites']) ? (array) $_POST['specialites'] : ['7'];

    /* =========================================================
    5️⃣ VALIDATION MINIMALE
    ========================================================= */

    if ($titre === '' || $description === '') {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Champs obligatoires manquants"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ IDENTITÉ UTILISATEUR
    ========================================================= */

    $id_member = $user['id_membre'];      // issu de la session (fiable)
    $mail      = $user['email'] ?? null;  // jamais depuis le POST

    if (!$mail) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Email utilisateur introuvable"
        ]);
        exit;
    }

    /* =========================================================
    7️⃣ PRÉPARATION DES DONNÉES
    ========================================================= */

    $date_now = (new DateTime())->format('Y-m-d H:i:s');

    $data = [
        'titre_offre'   => $titre,
        'linkedin'      => $linkedin,
        'description'   => $description,
        'email'         => $mail,
        'type_contrat'  => $type_contrat,
        'date_creation' => $date_now
    ];

    /* =========================================================
    8️⃣ INSERTION EN BASE
    ========================================================= */

    try {

        $ok = EEA_Database::addJob($data, $specialites);

        if (!$ok) {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "message" => "Échec de l’insertion"
            ]);
            exit;
        }

        /* =====================================================
        9️⃣ ROTATION DU TOKEN CSRF (BONNE PRATIQUE)
        =====================================================
        - Empêche la réutilisation du token
        - Le nouveau token sera présent au prochain chargement
        ===================================================== */

        $_SESSION['csrf_token'] = bin2hex(random_bytes(24));

        http_response_code(200);
        echo json_encode([
            "success"      => true,
            "dateDepot"    => $date_now,
            "type_contrat" => $type_contrat,
            "email"        => $mail,
            "message"      => ""
        ]);
        exit;

    } catch (Throwable $e) {

        // Sécurité : aucune fuite d’erreur interne
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
}
