<?php
    /* ============================================================================
    📌 CONTROLLER – CONSULTATION D’UNE AIDE (SÉCURISÉ)
    - Appelé via AJAX (JSON)
    - Accès réservé : Président & Membres du bureau
    - Reçoit : aide_id (GET)
    - Retourne : données complètes de l’aide
    ============================================================================ */

    require_once "require_db.php";
    session_start();

    header('Content-Type: application/json');

    /* ============================================================
    🔐 1) VÉRIFICATION DE LA SESSION
    ============================================================ */
    if (
        empty($_SESSION['user']) ||
        !is_array($_SESSION['user']) ||
        empty($_SESSION['user']['id_membre'])
    ) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Utilisateur non authentifié'
        ]);
        exit;
    }

    $user = $_SESSION['user'];

    /* ============================================================
    🔐 2) VÉRIFICATION DES DROITS
    ➜ Autorisés : Président & Membres du bureau
    ============================================================ */
    if (empty($user['membre_bureau'])) {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Accès interdit'
        ]);
        exit;
    }

    /* ============================================================
    🔎 3) VALIDATION DE L’IDENTIFIANT AIDE (GET)
    ============================================================ */
    if (empty($_GET['aide_id']) || !ctype_digit($_GET['aide_id'])) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Identifiant aide invalide'
        ]);
        exit;
    }

    $aide_id = (int) $_GET['aide_id'];

    /* ============================================================
    📥 4) RÉCUPÉRATION EN BASE
    ============================================================ */
    try {
        $result = EEA_Database::fetchAides($aide_id);

        if (empty($result)) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Aide introuvable'
            ]);
            exit;
        }

        // fetchAides retourne un tableau → on prend la première ligne
        $aide = $result[0];

        /* ============================================================
        ✅ 5) RÉPONSE OK
        ============================================================ */
        echo json_encode([
            'success'         => true,
            'aide_id'         => $aide['aide_id'],
            'nom'             => $aide['nom'],
            'prenom'          => $aide['prenom'],
            'email'           => $aide['email'],
            'telephone_num'   => $aide['telephone_num'],
            'id_membre'       => $aide['id_membre'],
            'type_aide_id'    => $aide['type_aide_id'],
            'type_libelle'    => $aide['type_aide'],   // jointure type_aide
            'sujet'           => $aide['sujet'],
            'message'         => $aide['message'],
            'date_demande'    => $aide['date_demande']
        ]);

    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur serveur'
        ]);
    }

    exit;
?>