<?php
    /************************************************************
     *  CONTROLLER : AJOUT D'UNE INSCRIPTION À UN ÉVÈNEMENT
     *  ➜ Accès réservé aux utilisateurs connectés
     *  ➜ POST JSON
     *  ➜ Protection CSRF
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json');

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
    3️⃣ LECTURE DU JSON
    ========================================================= */

    $input = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($input) ||
        empty($input['id_event']) ||
        empty($input['pikachu_csrf'])
    ) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Données invalides"
        ]);
        exit;
    }

    /* =========================================================
    4️⃣ VÉRIFICATION CSRF
    ========================================================= */

    if (
        empty($_SESSION['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $input['pikachu_csrf'])
    ) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "CSRF invalide"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ VALIDATION ID ÉVÈNEMENT
    ========================================================= */

    $idEvent = $input['id_event'];

    if (!ctype_digit((string)$idEvent)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "ID événement invalide"
        ]);
        exit;
    }

    $idEvent = (int) $idEvent;

    /* =========================================================
    6️⃣ UTILISATEUR (SESSION)
    ========================================================= */

    $emailMembre = $user['email'];

    /* =========================================================
    7️⃣ INSCRIPTION À L’ÉVÈNEMENT
    ========================================================= */
    try 
    {

        // 🔒 Récupération des infos utilisateur depuis la session
        $nomMembre    = $user['nom'];
        $prenomMembre = $user['prenom'];
        $emailMembre  = $user['email'];
        $tel_num      = $user['telephone'];

        // 🔒 Vérifier si déjà inscrit (clé UNIQUE : id_event + email)
        if (EEA_Database::isAlreadyRegistered($idEvent, $emailMembre)) {
            http_response_code(409);
            echo json_encode([
                "success" => false,
                "message" => "Vous êtes déjà inscrit à cet événement"
            ]);
            exit;
        }

        // ➕ Ajouter l’inscription (date_inscription gérée par MySQL)
        $ok = EEA_Database::addInscription_Event([
            'id_event' => $idEvent,
            'nom'      => $nomMembre,
            'prenom'   => $prenomMembre,
            'email'    => $emailMembre,
            'tel_num'  => $tel_num
        ]);

        if ($ok) {
            // 🔁 Rotation CSRF après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool) $ok
        ]);
        exit;

    } 
    catch (Throwable $e) {

        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }