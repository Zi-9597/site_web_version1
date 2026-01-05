<?php
    /************************************************************
     *  CONTROLLER : AJOUT D’UNE OFFRE D’EMPLOI (AJAX)
     *  - Sécurité centralisée via init.php
     *  - Accès réservé aux utilisateurs connectés
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
    3️⃣ DONNÉES FORMULAIRE
    ========================================================= */

    $titre        = trim($_POST['titre_offre'] ?? '');
    $linkedin     = trim($_POST['linkedin'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $type_contrat = trim($_POST['types'] ?? 'Job Étudiant');
    $specialites  = !empty($_POST['specialites']) ? (array) $_POST['specialites'] : ['7'];

    /* =========================================================
    4️⃣ VALIDATION MINIMALE
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
    5️⃣ IDENTITÉ UTILISATEUR
    ========================================================= */

    // ID issu de la session (fiable)
    $id_member = $user['id_membre'];

    // Email issu de la session (pas du POST)
    $mail = $user['email'] ?? null;

    if (!$mail) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Email utilisateur introuvable"
        ]);
        exit;
    }

    /* =========================================================
    6️⃣ PRÉPARATION DONNÉES
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
    7️⃣ INSERTION BASE
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

        http_response_code(200);
        echo json_encode([
            "success"       => true,
            "dateDepot"     => $date_now,
            "type_contrat"  => $type_contrat,
            "email"         => $mail, 
            "message"       => ""
        ]);
        exit;

    } catch (Throwable $e) {

        // Sécurité : pas de fuite d’erreur interne
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }
?>