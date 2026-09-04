<?php

    require_once "mail_class.php";

    $mailer = EEA_Mailer::getInstance(); 
   
    var_dump($mailer->sendTest("ziyadhoussaini95@gmail.com"));
    
?>