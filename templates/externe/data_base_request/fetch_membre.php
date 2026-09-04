<?php
/* CHANGE (authorization): use the shared bootstrap, not stale session role data. */
require_once 'commun/init.php';
require_post();
require_admin($user);

$data = json_decode(file_get_contents('php://input'), true);
$idMember = is_array($data) ? trim($data['id_member'] ?? '') : '';
if ($idMember === '') {
    json_response(['success' => false, 'error' => 'Identifiant membre manquant'], 400);
}

try {
    $member = EEA_Database::fetc_user_id($idMember);
    if (!$member) {
        json_response(['success' => false, 'error' => 'Membre introuvable'], 404);
    }

    // CHANGE (privacy): return only fields required by the administration modal.
    json_response(['success' => true, 'data' => [
        'id_membre' => $member['id_membre'],
        'prenom' => $member['prenom'],
        'nom' => $member['nom'],
        'section' => $member['section'],
        'membre_assoc' => $member['membre_assoc'],
        'membre_bureau' => $member['membre_bureau'],
        'email' => $member['email'],
        'phone_number' => $member['phone_number'],
        'ville' => $member['ville'],
        'metier' => $member['metier'],
        'date_inscription' => $member['date_inscription'],
    ]);
} catch (Throwable $exception) {
    error_log('Member fetch failed: ' . $exception->getMessage());
    json_response(['success' => false, 'error' => 'Erreur serveur'], 500);
}
