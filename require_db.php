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
            (nom_event, date_event,  desc_event, id_membre, url_form , date_creation)
            VALUES 
            (:nom_event, :date_event,  :desc_event, :id_membre, :url_form , :date_creation)";


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
                $sql_add_offre = "INSERT INTO offres (titre_offre, url_linkedin, description, email_user , type_contrat ,  date_creation) 
                          VALUES (:titre_offre, :url_linkedin, :description, :email_user , :type_contrat , :date_creation)";

                $stmt = $pdo->prepare($sql_add_offre);
              
                $stmt->execute([
                    'titre_offre'  => $data['titre_offre'],
                    'url_linkedin'     => $data['linkedin'],
                    'description'  => $data['description'],
                    'email_user'   => $data['email'],
                    'type_contrat' => $data['type_contrat'],
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
                    o.type_contrat,
                    o.date_creation,
                    GROUP_CONCAT(s.nom_specialite SEPARATOR ', ') AS specialites
                FROM offres o
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


            if (!empty($filters['specialites'])) {
                $sql .= " AND s.id_specialite IN (" . implode(",", array_map("intval", $filters['specialites'])) . ")";
            }
            else
            {
                // Cas 2 : aucune spécialité → on force les spécialités 1 à 6
                $defaultSpecialites = ['1', '2', '3', '4', '5', '6'];

                $sql .= " AND s.id_specialite IN (" . implode(",", array_map("intval", $defaultSpecialites)) . ")";
            }

            if (!empty($filters['types'])) {
                $placeholders = implode(',', array_fill(0, count($filters['types']), '?'));
                $sql .= " AND o.type_contrat IN ($placeholders)";
                $params = array_merge($params, $filters['types']);
            }
            $sql .= "
                GROUP BY 
                    o.id_offre, o.titre_offre, o.url_linkedin, o.description, 
                    o.email_user, o.type_contrat , o.date_creation
                ORDER BY o.date_creation DESC
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        public static function update_user_info($id, $field, $value) {
            $pdo = self::getInstance();
            $sql = "UPDATE subscribers SET $field = :value WHERE id_membre = :id";
            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ":value" => $value,
                ":id" => $id
        ]);
        }

        public static function fetchAllMembers(): array
        {
            $pdo = self::getInstance();
            $sql = "SELECT 
                        id_membre, 
                        prenom, 
                        nom, 
                        section, 
                        membre_assoc, 
                        membre_bureau, 
                        ville, 
                        phone_number,
                        email,
                        metier,
                        date_inscription
                    FROM subscribers 
                    ORDER BY nom ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll();
        }

        public static function updateMember(array $data): bool
        {
            $pdo = self::getInstance();

            $sql = "UPDATE subscribers SET
                        prenom         = :prenom,
                        nom            = :nom,
                        section        = :section,
                        membre_assoc   = :membre_assoc,
                        membre_bureau  = :membre_bureau,
                        email          = :email,
                        phone_number   = :phone_number,
                        ville          = :ville,
                        metier         = :metier
                    WHERE id_membre = :id";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ":prenom"        => $data["prenom"],
                ":nom"           => $data["nom"],
                ":section"       => $data["section"],
                ":membre_assoc"  => $data["assoc"],
                ":membre_bureau" => $data["bureau"],
                ":email"         => $data["email"],
                ":phone_number"  => $data["phone"],
                ":ville"         => $data["ville"],
                ":metier"        => $data["metier"],
                ":id"            => $data["id"]
            ]);
        }
    }   





        




