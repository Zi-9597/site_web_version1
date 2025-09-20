<?php

    class EEA_Database
    {

        private static $instance = null;
        private $pdo; 



        private function __construct()
        {
            $env = parse_ini_file(".env"); 


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


        public static function addJob(array $data , array $specialitie):bool
        {
            $pdo = self::getInstance();

            $pdo->beginTransaction();

            try
            {
                $sql_add_offre = "INSERT INTO offres (titre_offre, url_linkedin, description, email_user , lieu , departement ,  date_creation) 
                          VALUES (:titre_offre, :url_linkedin, :description, :email_user , :lieu , :departement , :date_creation)";

                $stmt = $pdo->prepare($sql_add_offre);
              
                $stmt->execute([
                    'titre_offre'  => $data['titre_offre'],
                    'url_linkedin'     => $data['linkedin'],
                    'description'  => $data['description'],
                    'email_user'   => $data['email'],
                    'lieu' => $data['lieu'],
                    'departement' => $data['departement'],
                    'date_creation' => $data['date_creation']
                ]);

                
                // Récupérer l'id de l'offre insérée
                $id_offre = $pdo->lastInsertId();

                $sql_add_specialite = "INSERT INTO offre_specialite (id_offre , id_specialite) 
                          VALUES (:id_offre, :id_specialite)";
                
                $stmSpec = $pdo->prepare($sql_add_specialite);

                foreach ($specialitie as $id_specialite)
                {
                    $stmSpec->execute([
                        'id_offre' => $id_offre,
                        'id_specialite'=>(int)$id_specialite
                    ]);
                }


                

                $pdo->commit();
                return true;
                
            }
            catch (Exception $e) 
            {
                $pdo->rollBack();
                throw new RuntimeException("Erreur lors de l'insertion de l'offre : " . $e->getMessage());

            }

        
        }

        public static function getLocalisationByCP(string $code_postal): ?array
        {
            $pdo = self::getInstance();

            $sql = "
                SELECT nom_commune , nom_departement, nom_region
                FROM dep_reg_com
                WHERE Code_Postal = :cp
                LIMIT 1
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['cp' => $code_postal]);
            $row = $stmt->fetch();

            return $row ?: null;
        }


        public static function searchJobs(array $filters): array
        {
            $pdo = self::getInstance();

            $sql = "
                SELECT 
                    o.id_offre,
                    o.titre_offre,
                    o.url_linkedin,
                    o.description,
                    o.email_user,
                    o.departement,
                    o.date_creation,
                    d.nom_commune,
                    d.nom_departement,
                    d.nom_region,
                    GROUP_CONCAT(s.nom_specialite SEPARATOR ', ') AS specialites
                FROM offres o
                LEFT JOIN dep_reg_com d 
                    ON o.departement = d.code_postal
                LEFT JOIN offre_specialite os 
                    ON o.id_offre = os.id_offre
                LEFT JOIN specialites s 
                    ON os.id_specialite = s.id_specialite
                WHERE 1=1
            ";

            $params = [];

            // 🔎 Filtres dynamiques
            if (!empty($filters['titre_offre'])) {
                $sql .= " AND o.titre_offre LIKE :titre_offre";
                $params['titre_offre'] = "%" . $filters['titre_offre'] . "%";
            }

            if (!empty($filters['departement'])) {
                $sql .= " AND o.departement = :departement";
                $params['departement'] = $filters['departement'];
            }

            if (!empty($filters['specialites'])) {
                $sql .= " AND s.id_specialite IN (" . implode(",", array_map("intval", $filters['specialites'])) . ")";
            }

            $sql .= "
                GROUP BY 
                    o.id_offre, o.titre_offre, o.url_linkedin, o.description, 
                    o.email_user, o.departement, o.date_creation,
                    d.nom_commune, d.nom_departement, d.nom_region
                ORDER BY o.date_creation DESC
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }






    }





        




