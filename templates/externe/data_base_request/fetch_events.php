<?php
    /* ==========================================================
    CONTROLLER AJAX : FETCH EVENT
    - Accès autorisé à TOUS les utilisateurs connectés
    - Sécurité et session via init.php
    ========================================================== */

    // Initialisation globale (session, sécurité, DB, $user)
    require_once "commun/init.php";

    // Réponse JSON
    header('Content-Type: application/json');

    /* ==========================================================
    1️⃣ MÉTHODE HTTP
    ========================================================== */
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'message' => 'Méthode non autorisée'
        ]);
        exit;
    }

    /* ==========================================================
    2️⃣ CONTRÔLE DE LA SESSION
    ========================================================== */

    // Utilisateur non connecté → refus
    if (!$user) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Connexion requise'
        ]);
        exit;
    }

    /* ==========================================================
    3️⃣ VALIDATION DE L’ID ÉVÉNEMENT
    ========================================================== */

    if (
        !isset($_GET['id_event']) ||
        !ctype_digit($_GET['id_event'])
    ) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'ID événement invalide'
        ]);
        exit;
    }

    $id_event = (int) $_GET['id_event'];

    /* ==========================================================
    4️⃣ RÉCUPÉRATION DE L’ÉVÉNEMENT EN BASE
    ========================================================== */

    try {

        // id_event est UNIQUE → un seul résultat attendu
        $result = EEA_Database::fetch_events(null, $id_event);

        if (empty($result)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Événement introuvable'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'data'    => $result[0]
        ]);
        exit;

    } catch (Throwable $e) {

        // Ne jamais exposer l’erreur interne
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
        exit;
    }
?>