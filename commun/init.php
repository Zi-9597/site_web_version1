<?php
/*
 * CHANGE (security bootstrap): this is now the single place for session,
 * browser-header, JSON-response, CSRF, and authenticated-user rules.
 * Existing `?dest=` routes continue to include this file unchanged.
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    // CHANGE (session security): reject unknown session IDs and disable URL IDs.
    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', '1');
    ini_set('session.cookie_samesite', 'Strict');
    session_start();
}

if (!headers_sent()) {
    // CHANGE (browser security): protect MIME sniffing and clickjacking too.
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header('Content-Security-Policy: default-src \'self\'; base-uri \'self\'; object-src \'none\'; frame-ancestors \'none\'; form-action \'self\'; img-src \'self\' data:; connect-src \'self\'; script-src \'self\' \'unsafe-inline\' https://cdn.jsdelivr.net https://ajax.googleapis.com https://maxcdn.bootstrapcdn.com; style-src \'self\' \'unsafe-inline\' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://maxcdn.bootstrapcdn.com; font-src \'self\' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://maxcdn.bootstrapcdn.com; upgrade-insecure-requests');
}

require_once __DIR__ . '/../require_db.php';

/** CHANGE (XSS prevention): use for every value inserted into HTML. */
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** CHANGE (consistent JSON): endpoints can return safe errors consistently. */
function json_response(array $data, int $status = 200): void
{
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function require_post(): void
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        json_response(['success' => false, 'message' => 'Méthode non autorisée'], 405);
    }
}

function require_authenticated_user(?array $user): array
{
    if ($user === null) {
        json_response(['success' => false, 'message' => 'Utilisateur non authentifié'], 401);
    }

    return $user;
}

function is_bureau_member(array $user): bool
{
    // Existing deployments may use several bureau titles, so do not hard-code two titles here.
    return isset($user['membre_bureau']) && trim((string) $user['membre_bureau']) !== '';
}

function require_bureau_member(?array $user): array
{
    $user = require_authenticated_user($user);
    if (!is_bureau_member($user)) {
        json_response(['success' => false, 'message' => 'Accès interdit'], 403);
    }

    return $user;
}

function require_admin(?array $user): array
{
    $user = require_authenticated_user($user);
    if (!in_array($user['membre_bureau'] ?? '', ['Président', 'Web Admin'], true)) {
        json_response(['success' => false, 'message' => 'Accès interdit'], 403);
    }

    return $user;
}

/**
 * CHANGE (CSRF compatibility): old clients use pikachu, pikachu_csfr, and
 * pikachu_csrf. All are accepted temporarily, then checked by one function.
 */
function require_csrf(array $input): void
{
    $token = $input['pikachu_csrf'] ?? $input['pikachu_csfr'] ?? $input['pikachu'] ?? '';
    if (!is_string($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
        json_response(['success' => false, 'message' => 'CSRF invalide'], 403);
    }
}

function rotate_csrf_token(): string
{
    // CHANGE (CSRF): one token generator prevents inconsistent token sizes.
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

$user = null;
$sessionUser = $_SESSION['user'] ?? null;
if (empty($_SESSION['csrf_token'])) {
    // CHANGE (CSRF): public forms such as login and registration need a token too.
    rotate_csrf_token();
}
if (is_array($sessionUser) && !empty($sessionUser['id_membre'])) {
    /*
     * CHANGE (authorization): refresh the account from the database so deleted
     * accounts and changed roles cannot keep using stale session permissions.
     */
    $foundUser = EEA_Database::fetc_user_id((string) $sessionUser['id_membre']);
    if (!$foundUser || !is_array($foundUser) || empty($foundUser['id_membre'])) {
        $_SESSION = [];
        session_destroy();
        header('Location: /?dest=logout');
        exit;
    }

    $user = [
        'id_membre' => $foundUser['id_membre'],
        'prenom' => $foundUser['prenom'],
        'nom' => $foundUser['nom'],
        'membre_assoc' => $foundUser['membre_assoc'],
        'membre_bureau' => $foundUser['membre_bureau'] ?? '',
        'email' => $foundUser['email'],
        'telephone' => $foundUser['phone_number'] ?? '',
        'phone_number' => $foundUser['phone_number'] ?? '',
    ];
    $_SESSION['user'] = $user;

    $timeout = 1800;
    if (isset($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > $timeout) {
        $_SESSION = [];
        session_destroy();
        header('Location: /?dest=logout');
        exit;
    }
    $_SESSION['last_activity'] = time();

}

$nom_prenom = $user ? trim($user['prenom'] . ' ' . $user['nom']) : '';
