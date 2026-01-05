<?php
    /* ==========================================================
    AJOUT D’UNE DEMANDE D’AIDE (AJAX)
    - Accès réservé aux étudiants connectés
    - Initialisation et sécurité via init.php
    ========================================================== */

    // Initialisation globale : session, sécurité, DB, $user
    require_once "commun/init.php";

    // Réponse JSON uniquement
    header('Content-Type: application/json');

    /* ==========================================================
    1️⃣ VÉRIFICATION DE LA MÉTHODE HTTP
    ========================================================== */
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            'success' => false,
            'error'   => 'Méthode non autorisée'
        ]);
        exit;
    }

    /* ==========================================================
    2️⃣ RÉCUPÉRATION DES DONNÉES JSON
    ========================================================== */
    $data = json_decode(file_get_contents("php://input"), true);

    // Les données doivent être un tableau
    if (!is_array($data)) {
        echo json_encode([
            'success' => false,
            'error'   => 'Données invalides'
        ]);
        exit;
    }

    /* ==========================================================
    3️⃣ CONTRÔLE DE LA SESSION (OBLIGATOIRE)
    ========================================================== */

    // L’utilisateur doit être connecté
    if (!$user) {
        echo json_encode([
            'success' => false,
            'error'   => 'Connexion requise'
        ]);
        exit;
    }

    // Accès réservé aux étudiants uniquement
    if (
        !empty($user['membre_bureau']) ||
        ($user['membre_assoc'] ?? '') !== 'Étudiant/e'
    ) {
        echo json_encode([
            'success' => false,
            'error'   => 'Accès non autorisé'
        ]);
        exit;
    }

    /* ==========================================================
    4️⃣ DONNÉES FORCÉES DEPUIS LA SESSION
    (sécurité : pas modifiables côté client)
    ========================================================== */

    $id_membre        = $user['id_membre'];
    $data['nom']      = $user['nom'];
    $data['prenom']   = $user['prenom'];
    $data['email']    = $user['email'];

    /* ==========================================================
    5️⃣ VALIDATION DES DONNÉES
    ========================================================== */

    if (
        empty($data['type_aide_id']) ||
        empty($data['sujet']) ||
        empty($data['message'])
    ) {
        echo json_encode([
            'success' => false,
            'error'   => 'Champs manquants'
        ]);
        exit;
    }

    // Vérification email (sécurité supplémentaire)
    if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'error'   => 'Email invalide'
        ]);
        exit;
    }

    // Interdiction des emails institutionnels
    if (str_ends_with(strtolower($data['email']), '@univ-lille.fr')) {
        echo json_encode([
            'success' => false,
            'error'   => 'Email non autorisé'
        ]);
        exit;
    }

    // type_aide_id doit être numérique
    if (!ctype_digit((string)$data['type_aide_id'])) {
        echo json_encode([
            'success' => false,
            'error'   => 'Type d’aide invalide'
        ]);
        exit;
    }

    // Limite de taille du message (anti-spam basique)
    if (mb_strlen($data['message']) > 3000) {
        echo json_encode([
            'success' => false,
            'error'   => 'Message trop long'
        ]);
        exit;
    }

    /* ==========================================================
    6️⃣ INSERTION EN BASE DE DONNÉES
    ========================================================== */

    try {

        $ok = EEA_Database::addAide(
            $id_membre,
            [
                'nom'           => trim($data['nom']),
                'prenom'        => trim($data['prenom']),
                'email'         => trim($data['email']),
                'telephone_num' => $data['telephone'] ?? null,
                'type_aide_id'  => (int)$data['type_aide_id'],
                'sujet'         => trim($data['sujet']),
                'message'       => trim($data['message'])
            ]
        );

        echo json_encode([
            'success' => (bool)$ok
        ]);

    } catch (Throwable $e) {

        // Ne jamais exposer les erreurs internes en production
        echo json_encode([
            'success' => false,
            'error'   => 'Erreur serveur'
        ]);
    }

    exit;
?>