<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;



    require "commun/PHPMailer/src/Exception.php";
    require "commun/PHPMailer/src/PHPMailer.php";
    require "commun/PHPMailer/src/SMTP.php"; 

    class EEA_Mailer
    {

        private static $instance = null;
        
        private $mailer ; 


        // Configuration des données du mail de l'associatiion EEEA
        private function __construct()
        {
            $env = parse_ini_file(".env"); 


            $smpt = $env["smtp_univ"]; 
            $mail = $env["mail_eea"]; 
            $password =  $env["password_mail"]; 


            $this->mailer = new PHPMailer(true); 
            $this->mailer->isSMTP();
            $this->mailer->Host = $smpt;

            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $mail;
            $this->mailer->Password = $password;
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = 587;
            $this->mailer->CharSet = 'UTF-8';
            // Expéditeur par défaut
            $this->mailer->setFrom($env["mail_eea"], "Association Ancien et Étudiant EEEA");
        }

        public static function getInstance()
        {
            if(self::$instance === null)
            {
                self::$instance = new EEA_Mailer();
            }
            return self::$instance;
        }

       

        public function sendWelcome(string $to, string $name, string $last_name , string $token): bool
        {
            try 
            {
                // Nettoie les destinataires précédemment ajoutés
                // (important si la même instance envoie plusieurs mails)
                $this->mailer->clearAddresses();

                // Ajoute le destinataire du mail
                $this->mailer->addAddress($to);

                // Sujet du mail de bienvenue
                $this->mailer->Subject =
                    "Bienvenue à l'association des anciens & étudiants EEEA – Université de Lille";

                // Chargement du template HTML du mail
                $html = file_get_contents("templates/externe/mailer/mail_welcome.php");

                // Remplacement des variables dynamiques dans le template
                $html = str_replace(
                    ['{{NAME}}', '{{LAST_NAME}}' , '{{TOKEN}}'],
                    [$name, $last_name , $token],
                    $html
                );

                // Indique que le mail est en HTML
                $this->mailer->isHTML(true);

                // Corps HTML du mail
                $this->mailer->Body = $html;

                // Version texte du mail (fallback pour les clients sans HTML)
                $this->mailer->AltBody =
                    "Bonjour {$name} {$last_name},\n\n"
                    . "Merci d’avoir rejoint l’Association des Étudiants & Anciens (EEA) de l’Université de Lille.\n"
                    . "Nous sommes ravis de vous compter parmi nos membres.\n\n"
                    . "En tant que membre, vous bénéficiez notamment de :\n"
                    . "- Un réseau actif d’étudiants et diplômés\n"
                    . "- Des événements exclusifs : conférences, afterworks, visites d’entreprises\n"
                    . "- Un accompagnement carrière : stages, alternances, premier emploi\n"
                    . "- Des séances de révisions collectives pour préparer vos examens\n"
                    . "- Des réductions et avantages sur certaines activités étudiantes\n\n"
                    . "Nous vous tiendrons informé(e) des prochaines activités et événements de l’association.\n\n"
                    . "À très bientôt,\n"
                    . "L’équipe EEA Lille";

                // Envoi du mail
                $this->mailer->send();

                // Si tout s’est bien passé, on retourne true
                return true;
            } 
            catch (Exception $e) 
            {
                // En cas d’erreur, on journalise le message sans bloquer l’application
                error_log($this->mailer->ErrorInfo);

                // Échec de l’envoi
                return false;
            }
        }

    }
?>