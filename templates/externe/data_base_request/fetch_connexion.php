<?php
/* CHANGE (login security): use the shared secure session and CSRF bootstrap. */
require_once 'commun/init.php';
require_post();
require_csrf($_POST);

$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    json_response(['success' => false, 'message' => 'Erreur de connexion'], 400);
}

try {
    $userDb = EEA_Database::fetc_user_mail($email);
    if (!$userDb || empty($userDb['mot_de_passe']) || !password_verify($password, $userDb['mot_de_passe']) || (int) $userDb['is_validate'] !== 1) {
        json_response(['success' => false, 'message' => 'Erreur de connexion'], 401);
    }

    // CHANGE (session fixation): replace the visitor session after successful login.
    session_regenerate_id(true);
    $_SESSION['user'] = ['id_membre' => $userDb['id_membre']];
    $_SESSION['last_activity'] = time();
    rotate_csrf_token();
    json_response(['success' => true, 'redirect' => '/?dest=acceuil']);
} catch (Throwable $exception) {
    error_log('Login failed: ' . $exception->getMessage());
    json_response(['success' => false, 'message' => 'Erreur serveur'], 500);
}
