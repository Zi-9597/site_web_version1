<?php
    /************************************************************
     * CONTROLLER : UPDATE OFFRE (AJAX / JSON)
     * - Sécurité centralisée via init.php
     * - L’utilisateur doit être connecté
     * - L’offre doit appartenir à l’utilisateur
     * - Protection CSRF + rotation du token
     ************************************************************/

    require_once "commun/init.php";
    header('Content-Type: application/json');

    /* =========================================================
    1️⃣ UTILISATEUR CONNECTÉ
    ========================================================= */

    $user = require_authenticated_user($user);

    /* =========================================================
    2️⃣ MÉTHODE HTTP + JSON
    ========================================================= */

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "message" => "Méthode non autorisée"
        ]);
        exit;
    }

    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "JSON invalide"
        ]);
        exit;
    }

    /* =========================================================
    🛡️ 2️⃣ bis — VÉRIFICATION CSRF
    ========================================================= */

    require_csrf($input);

    /* =========================================================
    3️⃣ VALIDATION DES DONNÉES
    ========================================================= */

    $idOffre = $input['id_offre'] ?? null;
    if (!ctype_digit((string)$idOffre) || (int)$idOffre <= 0) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "id_offre invalide"
        ]);
        exit;
    }
    $idOffre = (int) $idOffre;

    $titre       = trim($input['titre_offre'] ?? '');
    $urlLinkedin = trim($input['url_linkedin'] ?? '');
    $description = trim($input['description'] ?? '');
    $typeContrat = trim($input['type_contrat'] ?? '');
    $specialites = $input['specialites'] ?? [];

    if ($titre === '' || $description === '' || $typeContrat === '') {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Champs requis manquants"
        ]);
        exit;
    }
    if (mb_strlen($titre) > 255 || mb_strlen($description) > 3000 || ($urlLinkedin !== '' && !filter_var($urlLinkedin, FILTER_VALIDATE_URL))) {
        json_response(['success' => false, 'message' => 'Données offre invalides'], 400);
    }

    if (!is_array($specialites)) {
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "specialites doit être un tableau"
        ]);
        exit;
    }

    /* =========================================================
    4️⃣ AUTORISATION : L’OFFRE APPARTIENT À L’UTILISATEUR
    ========================================================= */

    $pdo = EEA_Database::getInstance();

    $stmt = $pdo->prepare("
        SELECT email_user 
        FROM offres 
        WHERE id_offre = :id 
        LIMIT 1
    ");
    $stmt->execute([':id' => $idOffre]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        http_response_code(404);
        echo json_encode([
            "success" => false,
            "message" => "Offre introuvable"
        ]);
        exit;
    }

    if (!hash_equals($row['email_user'], $user['email'])) {
        http_response_code(403);
        echo json_encode([
            "success" => false,
            "message" => "Accès interdit"
        ]);
        exit;
    }

    /* =========================================================
    5️⃣ NETTOYAGE DES DONNÉES
    ========================================================= */

    $offreData = [
        "titre_offre"  => $titre,
        "url_linkedin" => $urlLinkedin,
        "description"  => $description,
        "type_contrat" => $typeContrat
    ];

    $specialites = array_values(
        array_unique(
            array_map(
                'intval',
                array_filter($specialites, fn($v) => ctype_digit((string)$v))
            )
        )
    );

    /* =========================================================
    6️⃣ UPDATE EN BASE + ROTATION CSRF
    ========================================================= */

    try {

        $ok = EEA_Database::updateJob($idOffre, $offreData, $specialites);

        if ($ok) {
            // 🔁 Rotation du token CSRF après action sensible
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        http_response_code($ok ? 200 : 500);
        echo json_encode([
            "success" => (bool) $ok
        ]);
        exit;

    } catch (Throwable $e) {

        http_response_code(500);
        echo json_encode([
            "success" => false,
            "message" => "Erreur serveur"
        ]);
        exit;
    }

?>
