<?php
/* CHANGE (registration security): validate all server-side values before storage. */
require_once 'commun/init.php';
require_once __DIR__ . '/../../../commun/uuid_v4.php';
require_once __DIR__ . '/../../../mail_class.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Location: /?dest=inscription');
    exit;
}

require_csrf($_POST);

$civil = trim($_POST['civil'] ?? '');
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$date = trim($_POST['date'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$password = $_POST['password'] ?? '';
$membre = trim($_POST['membre_assoc'] ?? '');
$section = trim($_POST['section'] ?? '');
$autreFiliere = trim($_POST['autre_fil'] ?? '');
$phone = trim($_POST['phone_e164'] ?? '');
$pays = trim($_POST['city'] ?? '');
$ville = trim($_POST['country'] ?? '');
$profession = trim($_POST['profession'] ?? '');

if ($section === 'Autre') {
    $section = $autreFiliere;
}

$birthDate = DateTime::createFromFormat('!d/m/Y', $date);
$dateErrors = DateTime::getLastErrors();
$invalidDate = !$birthDate || ($dateErrors !== false && ($dateErrors['warning_count'] > 0 || $dateErrors['error_count'] > 0)) || $birthDate->format('d/m/Y') !== $date;
$validCivil = ['Madame', 'Monsieur'];
$validMemberTypes = ['Étudiant/e', 'Alumni/e'];

/* CHANGE (input validation): client-side checks are not a security boundary. */
if (
    !in_array($civil, $validCivil, true) ||
    !in_array($membre, $validMemberTypes, true) ||
    $nom === '' || mb_strlen($nom) > 100 ||
    $prenom === '' || mb_strlen($prenom) > 100 ||
    $section === '' || mb_strlen($section) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255 ||
    /* CHANGE: no composition or minimum-length rule; retain non-empty and storage-safe maximum validation. */
    $password === '' || strlen($password) > 1024 ||
    $invalidDate
) {
    header('Location: /?dest=erreur_inscription');
    exit;
}

try {
    if (!empty(EEA_Database::fetc_user_mail($email))) {
        header('Location: /?dest=erreur_inscription');
        exit;
    }

    $confirmationToken = bin2hex(random_bytes(32));
    $saved = EEA_Database::addSubscriber([
        'id_membre' => uuid7(),
        'prenom' => $prenom,
        'nom' => $nom,
        'section' => $section,
        'membre_assoc' => $membre,
        'membre_bureau' => false,
        'email' => $email,
        'phone_number' => mb_substr($phone, 0, 30),
        'mot_de_passe' => password_hash($password, PASSWORD_DEFAULT),
        'date_naissance' => $birthDate->format('Y-m-d'),
        'date_inscription' => (new DateTime())->format('Y-m-d H:i:s'),
        'pays' => mb_substr($pays, 0, 100),
        'ville' => mb_substr($ville, 0, 100),
        'metier' => mb_substr($profession, 0, 150),
        'genre' => $civil,
        'confirmation_token' => $confirmationToken,
        'is_validated' => 0,
    ]);

    if (!$saved || !EEA_Mailer::getInstance()->sendWelcome($email, $prenom, $nom, $confirmationToken)) {
        // Avoid declaring success when the new member cannot receive confirmation.
        header('Location: /?dest=erreur_inscription');
        exit;
    }
} catch (Throwable $exception) {
    error_log('Registration failed: ' . $exception->getMessage());
    header('Location: /?dest=erreur_inscription');
    exit;
}

header('Location: /?dest=success');
exit;
