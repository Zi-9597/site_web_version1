<?php

    class EEA_Database
    {

        private static $instance = null;
        private $pdo; 



        private function __construct()
        {
            $env = parse_ini_file(".env1"); 


            $host = $env["databaseHost"]; 
            $user = $env["databaseUsername"]; 
            $password =  $env["databasePassword"]; 
            $dbname = $env["databaseName"];
            $charset = "utf8mb4"; 

            $dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
            
            $option = [

                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, 
                PDO::ATTR_EMULATE_PREPARES => false

            ]; 

            try{

                $this->pdo = new PDO($dsn , $user , $password , $option);
           
            
            }catch(PDOException $e)
            {
                throw new RuntimeException("Problem to connet to the database ... ".$e->getMessage());
            }

            
            

        }


        public static function getInstance():PDO
        {


            if(self::$instance === null)
            {
                self::$instance = new self(); 
            }

            return self::$instance->pdo;
        }


        public static function addSubscriber(array $data):bool
        {

            $pdo = self::getInstance();
            $sql_add = "INSERT INTO subscribers
            (id_membre, prenom, nom, section, membre_assoc, membre_bureau, email, phone_number, mot_de_passe, date_naissance, date_inscription, pays, ville, metier, genre) 
            VALUES 
            (:id_membre, :prenom, :nom, :section, :membre_assoc, :membre_bureau, :email, :phone_number, :mot_de_passe, :date_naissance, :date_inscription, :pays, :ville, :metier, :genre)";

            $stmt = $pdo->prepare($sql_add); 
            return $stmt->execute($data);
        }


        public static function fetc_user_mail(string $mail): ?array
        {
            $pdo = self::getInstance();
            $sql_fetch = "SELECT * FROM subscribers where email = :email LIMIT 1";

            $stmt = $pdo->prepare($sql_fetch);
            $stmt->execute(['email' => $mail]);

            return $stmt->fetch();
        }

        public static function fetc_user_id(string $id_member): ?array
        {
            $pdo = self::getInstance();
            $sql_fetch = "SELECT * FROM subscribers where id_membre = :id_member LIMIT 1";

            $stmt = $pdo->prepare($sql_fetch);
            $stmt->execute(['id_member' => $id_member]);

    
            return $stmt->fetch();
        }

        
        public static function addEvent(array $data): bool
        {
            $pdo = self::getInstance();
            $sql_add = "INSERT INTO evenements 
            (nom_event, date_event, lieu_event, desc_event, categorie, id_membre, url_form , date_creation)
            VALUES 
            (:nom_event, :date_event, :lieu_event, :desc_event, :categorie, :id_membre, :url_form , :date_creation)";


            $stmt = $pdo->prepare($sql_add); 
            
            return $stmt->execute($data);
            
        }

        public static function fetc_event(string $my_sql , array $data): ?array
        {
            $pdo = self::getInstance();
            

            $stmt = $pdo->prepare($my_sql);
            $stmt->execute($data);

    
            return $stmt->fetchAll();
        }





        



     

    }

