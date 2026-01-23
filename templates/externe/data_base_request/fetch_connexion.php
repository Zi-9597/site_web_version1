<?php
    header('Content-Type: application/json');

    /* ==================================================
    CONFIGURATION COOKIE DE SESSION (AVANT session_start)
    ================================================== */

    // Accepter la session UNIQUEMENT via cookies (pas URL)
    ini_set('session.use_only_cookies', 1);

    // Empêche JavaScript d'accéder au cookie de session
    ini_set('session.cookie_httponly', 1);

    /* ==================================================
    DÉMARRAGE DE LA SESSION
    ================================================== */

    session_start();

    require_once "require_db.php";

    /* ==================================================
    VÉRIFICATION REQUÊTE
    ================================================== */

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["success" => false, "message" => "Requête invalide"]);
        exit;
    }

    /* ==================================================
    DONNÉES UTILISATEUR
    ================================================== */

    $mail     = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($mail === '' || $password === '') {
        echo json_encode(["success" => false, "message" => "Champs manquants"]);
        exit;
    }

    /* ==================================================
    AUTHENTIFICATION
    ================================================== */

    $userDb = EEA_Database::fetc_user_mail($mail);

    if (
    !$userDb
    || empty($userDb['mot_de_passe'])
    || !password_verify($password, $userDb['mot_de_passe'])
    || (int)$userDb['is_validate'] !== 1
    ) 
    {
        echo json_encode([
            "success" => false,
            "message" => "Erreur de connexion"
        ]);
        exit;
    }


    /* ==================================================
    CONNEXION RÉUSSIE
    ================================================== */

    // Protection contre la session fixation
    session_regenerate_id(true);

    // Création de la session utilisateur
    $_SESSION['user'] = [
        'id_membre'     => $userDb['id_membre'],
        'id'            => $userDb['id'],
        'prenom'        => $userDb['prenom'],
        'nom'           => $userDb['nom'],
        'membre_assoc'  => $userDb['membre_assoc'],
        'membre_bureau' => $userDb['membre_bureau'] ?? null,
        'email'         => $userDb['email'],
        'telephone'     => $userDb['phone_number']
    ];

    $_SESSION['csrf_token'] = bin2hex(random_bytes(24));
    // Création de l'activité
    $_SESSION['last_activity'] = time();

    /* ==================================================
    RÉPONSE AJAX
    ================================================== */

    echo json_encode([
        "success"  => true,
        "redirect" => "/?dest=acceuil"
    ]);
    exit;
?>