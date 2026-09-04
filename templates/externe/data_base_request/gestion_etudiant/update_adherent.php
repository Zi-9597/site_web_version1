<?php
    /* ============================================================================
    📌 CONTROLLER – UPDATE MEMBRE (AJAX / JSON)
    - init.php inclus
    - Accès STRICT : Président & Web Admin uniquement
    - Protection CSRF + rotation
    ============================================================================ */

    require_once "commun/init.php";
    header('Content-Type: application/json');

    try {

        /* ============================================================
        🔐 1) UTILISATEUR CONNECTÉ
        ============================================================ */
        $user = require_authenticated_user($user);

        /* ============================================================
        🔐 2) AUTORISATION STRICTE : PRÉSIDENT / WEB ADMIN
        ============================================================ */
        require_admin($user);

        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            json_response(['success' => false, 'error' => 'Méthode non autorisée'], 405);
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

        /* ============================================================
        🛡️ 3️⃣ bis — VÉRIFICATION CSRF
        ============================================================ */
        require_csrf($data);

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

            if ($updated) {
                // 🔁 Rotation CSRF
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }

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
        $allowedAssoc = ['Étudiant/e', 'Alumni/e'];
        $allowedBureau = ['', 'Président', 'Web Admin'];
        if (
            !in_array($payload['assoc'], $allowedAssoc, true) ||
            !in_array($payload['bureau'], $allowedBureau, true) ||
            !filter_var($payload['email'], FILTER_VALIDATE_EMAIL) ||
            mb_strlen($payload['prenom']) > 100 || mb_strlen($payload['nom']) > 100
        ) {
            json_response(['success' => false, 'error' => 'Données membre invalides'], 400);
        }

        /* ============================================================
        💾 8) UPDATE EN BASE + ROTATION CSRF
        ============================================================ */
        $updated = EEA_Database::updateMember($payload);

        if ($updated) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        echo json_encode([
            'success' => (bool) $updated
        ]);
        exit;

    } catch (Throwable $e) {

        echo json_encode([
            'success' => false,
            'error'   => 'Erreur serveur'
        ]);
        exit;
    }
?>
