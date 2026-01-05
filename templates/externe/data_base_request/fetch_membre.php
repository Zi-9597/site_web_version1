<?php
    /* ============================================================================
    📌 CONTROLLER – FETCH MEMBER (SÉCURISÉ)
    - Appelé via AJAX (POST JSON)
    - Accès autorisé : Président & Web Admin
    - Rôle vérifié via SESSION uniquement
    ============================================================================ */

    require_once "require_db.php";
    session_start();

    header('Content-Type: application/json');

    try {

        /* ============================================================
        🔐 1) VÉRIFICATION DE LA SESSION
        ============================================================ */
        if (
            empty($_SESSION['user']) ||
            !is_array($_SESSION['user']) ||
            empty($_SESSION['user']['id_membre'])
        ) {
            header("Location: /?dest=logout");
            exit;
        }

        $sessionUser = $_SESSION['user'];

        /* ============================================================
        🔐 2) VÉRIFICATION DES DROITS (SESSION = SOURCE DE VÉRITÉ)
        ============================================================ */
        $roleBureau = $sessionUser['membre_bureau'] ?? '';

        if (!in_array($roleBureau, ['Président', 'Web Admin'], true)) {
            header("Location: /?dest=logout");
            exit;
        }

        /* ============================================================
        📥 3) LECTURE & VALIDATION DU JSON
        ============================================================ */
        $data = json_decode(file_get_contents("php://input"), true);

        if (
            !is_array($data) ||
            empty($data['id_member'])
        ) {
            echo json_encode([
                'success' => false,
                'error'   => 'Identifiant membre manquant'
            ]);
            exit;
        }

        $id_member = trim($data['id_member']);

        /* ============================================================
        📡 4) RÉCUPÉRATION DU MEMBRE DEMANDÉ
        ============================================================ */
        $member = EEA_Database::fetc_user_id($id_member);

        if (!$member) {
            echo json_encode([
                'success' => false,
                'error'   => 'Membre introuvable'
            ]);
            exit;
        }

        /* ============================================================
        ✅ 5) RÉPONSE OK
        ============================================================ */
        echo json_encode([
            'success' => true,
            'data' => [
                'id_membre'        => $member['id_membre'],
                'prenom'           => $member['prenom'],
                'nom'              => $member['nom'],
                'section'          => $member['section'],
                'membre_assoc'     => $member['membre_assoc'],
                'membre_bureau'    => $member['membre_bureau'],
                'email'            => $member['email'],
                'phone_number'     => $member['phone_number'],
                'ville'            => $member['ville'],
                'metier'           => $member['metier'],
                'date_inscription' => $member['date_inscription']
            ]
        ]);

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'error'   => 'Erreur serveur'
        ]);
    }

    exit;

?>
