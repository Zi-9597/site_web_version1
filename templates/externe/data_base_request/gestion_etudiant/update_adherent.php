<?php
    /* ============================================================================
    📌 CONTROLLER – UPDATE MEMBRE (AJAX / JSON)
    - init.php inclus
    - Accès STRICT : Président & Web Admin uniquement
    - Deux modes :
        1) action = "make_ancien"
        2) update normal
    ============================================================================ */

    require_once "commun/init.php";
    header('Content-Type: application/json');

    try {

        /* ============================================================
        🔐 1) UTILISATEUR CONNECTÉ (garanti par init.php)
        ============================================================ */
        if (!$user) {
            header("Location: /?dest=logout");
            exit;
        }

        /* ============================================================
        🔐 2) AUTORISATION STRICTE : PRÉSIDENT / WEB ADMIN
        ============================================================ */
        if (
            empty($user['membre_bureau']) ||
            !in_array($user['membre_bureau'], ['Président', 'Web Admin'], true)
        ) {
            header("Location: /?dest=logout");
            exit;
        }

        /* ============================================================
        📥 3) LECTURE & VALIDATION DU JSON
        ============================================================ */
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || empty($data['id_member'])) {
            echo json_encode([
                'success' => false,
                'error'   => 'Identifiant membre manquant'
            ]);
            exit;
        }

        $id_member = trim($data['id_member']);
        $action    = trim($data['action'] ?? '');

        /* ============================================================
        🔎 4) SOURCE DE VÉRITÉ : MEMBRE EN BASE
        ============================================================ */
        $member = EEA_Database::fetc_user_id($id_member);

        if (!$member || !is_array($member)) {
            echo json_encode([
                'success' => false,
                'error'   => 'Membre introuvable'
            ]);
            exit;
        }

        /* ============================================================
        🟠 5) MODE ACTION : PASSER EN ANCIEN
        ============================================================ */
        if ($action === 'make_ancien') {

            $payload = [
                'id'      => $member['id_membre'],
                'prenom'  => $member['prenom'],
                'nom'     => $member['nom'],
                'section' => $member['section'],
                'assoc'   => 'Alumni/e',
                'bureau'  => '',
                'email'   => $member['email'],
                'phone'   => $member['phone_number'],
                'ville'   => $member['ville'],
                'metier'  => $member['metier']
            ];

            $updated = EEA_Database::updateMember($payload);

            echo json_encode([
                'success' => (bool) $updated
            ]);
            exit;
        }

        /* ============================================================
        🧼 6) MODE NORMAL : UPDATE CLASSIQUE
        ============================================================ */
        $payload = [
            'id'      => $member['id_membre'],
            'prenom'  => trim($data['prenom'] ?? ''),
            'nom'     => trim($data['nom'] ?? ''),
            'section' => trim($data['section'] ?? ''),
            'assoc'   => trim($data['membre_assoc'] ?? ''),
            'bureau'  => trim($data['membre_bureau'] ?? ''),
            'email'   => trim($data['email'] ?? ''),
            'phone'   => trim($data['phone'] ?? ''),
            'ville'   => trim($data['ville'] ?? ''),
            'metier'  => trim($data['metier'] ?? '')
        ];

        /* ============================================================
        🧪 7) VALIDATION MÉTIER MINIMALE
        ============================================================ */
        if ($payload['prenom'] === '' || $payload['nom'] === '') {
            echo json_encode([
                'success' => false,
                'error'   => 'Prénom et nom obligatoires'
            ]);
            exit;
        }

        /* ============================================================
        💾 8) UPDATE EN BASE
        ============================================================ */
        $updated = EEA_Database::updateMember($payload);

        echo json_encode([
            'success' => (bool) $updated
        ]);
        exit;

    } catch (Throwable $e) {

        // ⚠️ log serveur en prod
        echo json_encode([
            'success' => false,
            'error'   => 'Erreur serveur'
        ]);
        exit;
    }
?>