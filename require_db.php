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


        public static function addSubscriber(array $data): bool
        {
            $pdo = self::getInstance();

            $sql_add = "
                INSERT INTO subscribers
                (
                    id_membre,
                    prenom,
                    nom,
                    section,
                    membre_assoc,
                    membre_bureau,
                    email,
                    phone_number,
                    mot_de_passe,
                    date_naissance,
                    date_inscription,
                    pays,
                    ville,
                    metier,
                    genre,
                    confirmation_token,
                    is_validate
                )
                VALUES
                (
                    :id_membre,
                    :prenom,
                    :nom,
                    :section,
                    :membre_assoc,
                    :membre_bureau,
                    :email,
                    :phone_number,
                    :mot_de_passe,
                    :date_naissance,
                    :date_inscription,
                    :pays,
                    :ville,
                    :metier,
                    :genre,
                    :confirmation_token,
                    :is_validated
                )
            ";

            $stmt = $pdo->prepare($sql_add);
            return $stmt->execute($data);
        }


        public static function fetc_user_mail(string $mail): ?array
        {
            $pdo = self::getInstance();
            $sql_fetch = "SELECT * FROM subscribers where email = :email LIMIT 1";

            $stmt = $pdo->prepare($sql_fetch);
            $stmt->execute(['email' => $mail]);

            $result =  $stmt->fetch(PDO::FETCH_ASSOC);
            //Si je trouve l'info par rapport au mail, alors je renvoie le tableau contenant ce mail sinon un tableau vide
            return $result ?: [];
        }

        public static function confirmSubscriber(string $token): bool
        {
            $pdo = self::getInstance();

            $sql = "
                UPDATE subscribers
                    SET is_validate = 1 ,
                    confirmation_token = NULL
                WHERE confirmation_token = :token
                AND is_validate = 0
                AND date_inscription >= NOW() - INTERVAL 24 HOUR
            ";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'token' => $token
            ]);

            return $stmt->rowCount() === 1;
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

        public static function fetchUserJobs(
            ?string $email = null,
            ?int $id_offre = null
        ): array 
        {
            $pdo = self::getInstance();

            /* =========================================================
            🧱 BASE DE LA REQUÊTE
            ========================================================= */
            $sql = "
                SELECT 
                    o.id_offre,
                    o.titre_offre,
                    o.url_linkedin,
                    o.description,
                    o.email_user,
                    o.type_contrat,
                    o.date_creation,
                    GROUP_CONCAT(DISTINCT s.nom_specialite SEPARATOR ', ') AS specialites
                FROM offres o
                JOIN offre_specialite os 
                    ON o.id_offre = os.id_offre
                JOIN specialites s 
                    ON os.id_specialite = s.id_specialite
                WHERE 1=1
            ";

            $params = [];

            /* =========================================================
            🔎 FILTRE EMAIL (OPTIONNEL)
            ========================================================= */
            if (!empty($email)) {
                $sql .= " AND o.email_user = :email";
                $params[':email'] = $email;
            }

            /* =========================================================
            🔎 FILTRE ID OFFRE (OPTIONNEL)
            ========================================================= */
            if (!empty($id_offre)) {
                $sql .= " AND o.id_offre = :id_offre";
                $params[':id_offre'] = $id_offre;
            }

            /* =========================================================
            📊 GROUP & TRI
            ========================================================= */
            $sql .= "
                GROUP BY o.id_offre
                ORDER BY o.date_creation DESC
            ";

            /* =========================================================
            🚀 EXÉCUTION
            ========================================================= */
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }




        public static function updateJob(int $id_offre, array $offreData, array $specialites): bool
        {
            $pdo = self::getInstance();

            // 🔒 Sécuriser toute l'opération dans une transaction
            $pdo->beginTransaction();

            try {
                /* ----------------------------------------------
                * 1️⃣ MISE À JOUR DES INFORMATIONS PRINCIPALES
                * ---------------------------------------------- */

                $sqlUpdateOffre = "
                    UPDATE offres
                    SET 
                        titre_offre   = :titre,
                        url_linkedin  = :linkedin,
                        description   = :description,
                        type_contrat  = :type_contrat
                    WHERE id_offre = :id_offre
                ";

                $stmt = $pdo->prepare($sqlUpdateOffre);

                $stmt->execute([
                    ':titre'        => $offreData['titre_offre'],
                    ':linkedin'     => $offreData['url_linkedin'],
                    ':description'  => $offreData['description'],
                    ':type_contrat' => $offreData['type_contrat'],
                    ':id_offre'     => $id_offre
                ]);

                /* ----------------------------------------------
                * 2️⃣ SUPPRESSION DES ANCIENNES SPÉCIALITÉS
                * ---------------------------------------------- */
                 // 🚫 Si Job Étudiant → ne modifier AUCUNE spécialité
                if ($offreData['type_contrat'] === "Job Étudiant") {
                    $pdo->commit();
                    return true;
                }
                $sqlDeleteSpecs = "DELETE FROM offre_specialite WHERE id_offre = :id";
                $stmtDel = $pdo->prepare($sqlDeleteSpecs);
                $stmtDel->execute([':id' => $id_offre]);
               
                /* ----------------------------------------------
                * 3️⃣ AJOUT DES NOUVELLES SPÉCIALITÉS
                * ---------------------------------------------- */

                if (!empty($specialites)) {
                    $sqlInsertSpec = "
                        INSERT INTO offre_specialite (id_offre, id_specialite)
                        VALUES (:id_offre, :id_specialite)
                    ";

                    $stmtSpec = $pdo->prepare($sqlInsertSpec);

                    foreach ($specialites as $specId) {
                        $stmtSpec->execute([
                            ':id_offre'     => $id_offre,
                            ':id_specialite'=> (int)$specId
                        ]);
                    }
                }

                /* ----------------------------------------------
                * 4️⃣ VALIDATION DE LA TRANSACTION
                * ---------------------------------------------- */
                $pdo->commit();
                return true;

            } catch (Exception $e) {

                // 🔄 Retour à l’état initial si un problème survient
                $pdo->rollBack();

                throw new RuntimeException(
                    "Erreur lors de la mise à jour de l'offre (ID $id_offre) : " . $e->getMessage()
                );
            }
        }


        public static function removeJob(int $id_offre): bool
        {
            $pdo = self::getInstance();
            $pdo->beginTransaction();

            try {

                // 1️⃣ Supprimer les spécialités liées
                $stmt1 = $pdo->prepare("DELETE FROM offre_specialite WHERE id_offre = :id");
                $stmt1->execute([':id' => $id_offre]);

                // 2️⃣ Supprimer l'offre
                $stmt2 = $pdo->prepare("DELETE FROM offres WHERE id_offre = :id");
                $stmt2->execute([':id' => $id_offre]);

                $pdo->commit();
                return true;

            } catch (Exception $e) {
                $pdo->rollBack();
                throw new RuntimeException("Erreur suppression offre : " . $e->getMessage());
            }
        }

        public static function fetch_events(
            ?string $id_membre = null,
            ?int $id_event = null
        ): array 
        {
            $pdo = self::getInstance();

            try {
                /************************************************
                 *  REQUÊTE DE BASE AVEC JOINTURE
                 ************************************************/
                $sql = "
                    SELECT 
                        e.*,
                        m.nom,
                        m.prenom
                    FROM evenements e
                    INNER JOIN subscribers m 
                        ON m.id_membre COLLATE utf8mb4_unicode_ci 
                        = e.id_membre COLLATE utf8mb4_unicode_ci
                    WHERE 1=1
                ";

                $params = [];

                /************************************************
                 *  FILTRE PAR MEMBRE (OPTIONNEL)
                 ************************************************/
                if (!empty($id_membre)) {
                    $sql .= " AND e.id_membre = :id_membre";
                    $params[':id_membre'] = $id_membre;
                }

                /************************************************
                 *  FILTRE PAR ÉVÈNEMENT (OPTIONNEL)
                 ************************************************/
                if (!empty($id_event)) {
                    $sql .= " AND e.id_event = :id_event";
                    $params[':id_event'] = $id_event;
                }

                /************************************************
                 *  TRI
                 ************************************************/
                $sql .= " ORDER BY e.date_event ASC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);

            } catch (Exception $e) {
                throw new RuntimeException(
                    "Erreur lors de la récupération des événements : " . $e->getMessage()
                );
            }
        }


        public static function updateEvent(array $data): bool
        {
            $pdo = self::getInstance();

            $sql = "UPDATE evenements
                    SET nom_event = :nom,
                        date_event = :date_event,
                        desc_event = :desc_event,
                        url_form = :url_form
                    WHERE id_event = :id_event";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':nom'       => $data['nom_event'],
                ':date_event'=> $data['date_event'],
                ':desc_event'=> $data['desc_event'],
                ':url_form'  => $data['url_form'],
                ':id_event'  => $data['id_event']
            ]);
        }

        public static function removeEvent(int $id_event): bool
        {
            $pdo = self::getInstance();

            try {
                $stmt = $pdo->prepare("DELETE FROM evenements WHERE id_event = :id");
                $stmt->execute([':id' => $id_event]);

                return true;

            } catch (Exception $e) {
                throw new RuntimeException("Erreur suppression event : " . $e->getMessage());
            }
        }
        public static function fetch_actualites(?int $actu_id = null): array
        {
            $pdo = self::getInstance();
            try {
                $sql = "
                    SELECT 
                        a.*,
                        m.nom,
                        m.prenom
                    FROM actualites a
                    INNER JOIN subscribers m
                    ON m.id_membre COLLATE utf8mb4_unicode_ci 
                    = a.id_membre COLLATE utf8mb4_unicode_ci 
                    WHERE 1=1
                ";

                $params = [];

                // 🔎 Si un ID est fourni → filtrer sur l’actualité
                if ($actu_id !== null) {
                    $sql .= " AND actu_id = :actu_id";
                    $params[':actu_id'] = $actu_id;
                }

                $sql .= " ORDER BY date_depot DESC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);

            } catch (Exception $e) {
                throw new RuntimeException(
                    'Erreur lors de la récupération des actualités : ' . $e->getMessage()
                );
            }
        }

        public static function addActualite(array $data , string $id_membre): bool
        {
            $pdo = self::getInstance();

            try {
                $sql = "
                    INSERT INTO actualites (
                        titre_actu,
                        lien_actu,
                        desc_actu,
                        id_membre,
                        date_depot
                    ) VALUES (
                        :titre_actu,
                        :lien_actu,
                        :desc_actu,
                        :id_membre,
                        CURDATE()
                    )
                ";

                $stmt = $pdo->prepare($sql);

                return $stmt->execute([
                    ':titre_actu' => $data['titre_actu'],
                    ':lien_actu'  => $data['lien_actu'],
                    ':desc_actu'  => $data['desc_actu'],
                    ':id_membre'  => $id_membre
                ]);

            } catch (Exception $e) {
                throw new RuntimeException(
                    "Erreur lors de l'ajout de l'actualité : " . $e->getMessage()
                );
            }
        }
        public static function updateActualite(array $data): bool
        {
            $pdo = self::getInstance();

            try {
                $sql = "
                    UPDATE actualites
                    SET
                        titre_actu = :titre_actu,
                        lien_actu  = :lien_actu,
                        desc_actu  = :desc_actu
                    WHERE actu_id = :actu_id
                ";

                $stmt = $pdo->prepare($sql);

                return $stmt->execute([
                    ':titre_actu' => $data['titre_actu'],
                    ':lien_actu'  => $data['lien_actu'],
                    ':desc_actu'  => $data['desc_actu'],
                    ':actu_id'    => $data['actu_id']
                ]);

            } catch (Exception $e) {
                throw new RuntimeException(
                    "Erreur mise à jour actualité : " . $e->getMessage()
                );
            }
        }

        public static function removeActualite(int $actu_id): bool
        {
            $pdo = self::getInstance();

            try {
                $sql = "DELETE FROM actualites WHERE actu_id = :actu_id";
                $stmt = $pdo->prepare($sql);

                return $stmt->execute([
                    ':actu_id' => $actu_id
                ]);

            } catch (Exception $e) {
                throw new RuntimeException(
                    "Erreur suppression actualité : " . $e->getMessage()
                );
            }
        }
        
        public static function addAide(?string $id_membre, array $data): bool
        {
            $pdo = self::getInstance();

            $sql = "
                INSERT INTO aide_demandes (
                    nom,
                    prenom,
                    id_membre,
                    email,
                    telephone_num,
                    type_aide_id,
                    sujet,
                    message
                ) VALUES (
                    :nom,
                    :prenom,
                    :id_membre,
                    :email,
                    :telephone_num,
                    :type_aide_id,
                    :sujet,
                    :message
                )
            ";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':nom'           => $data['nom'] ?? null,
                ':prenom'        => $data['prenom'] ?? null,
                ':id_membre'     => $id_membre,              // peut être NULL
                ':email'         => $data['email'],
                ':telephone_num' => $data['telephone_num'] ?? null,
                ':type_aide_id'  => (int)$data['type_aide_id'],
                ':sujet'         => $data['sujet'],
                ':message'       => $data['message']
            ]);
        }

        public static function fetchAides(?int $aide_id = null): array
        {
            $pdo = self::getInstance();

            try {

                $sql = "
                    SELECT
                        a.aide_id,
                        a.nom,
                        a.prenom,
                        a.id_membre,
                        a.email,
                        a.telephone_num,
                        a.type_aide_id,
                        t.libelle AS type_aide,
                        a.sujet,
                        a.message,
                        a.date_demande
                    FROM aide_demandes a
                    LEFT JOIN aide_types t
                        ON a.type_aide_id = t.type_aide_id
                    WHERE 1=1
                ";

                $params = [];

                /* -----------------------------------------
                🔎 Filtre optionnel par aide_id
                ----------------------------------------- */
                if ($aide_id !== null) {
                    $sql .= " AND a.aide_id = :aide_id";
                    $params[':aide_id'] = $aide_id;
                }

                $sql .= " ORDER BY a.date_demande DESC";

                $stmt = $pdo->prepare($sql);
                $stmt->execute($params);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);

            } catch (Throwable $e) {
                throw new RuntimeException(
                    "Erreur lors de la récupération des demandes d’aide : " . $e->getMessage()
                );
            }
        }

        public static function deleteAide(int $aide_id): bool
        {
            $pdo = self::getInstance();

            $sql = "DELETE FROM aide_demandes WHERE aide_id = :aide_id";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':aide_id' => $aide_id
            ]);
        }

        public static function addGoodies(array $data , string $id_membre): bool
        {
            $pdo = self::getInstance();

            $sql = "
                INSERT INTO goodies (
                    nom_goodies,
                    prix,
                    lien,
                    description,
                    id_membre
                ) VALUES (
                    :nom_goodies,
                    :prix,
                    :lien,
                    :description,
                    :id_membre
                )
            ";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':nom_goodies' => $data['nom_goodies'],
                ':prix'        => $data['prix'],
                ':lien'        => $data['lien'] ?? null,
                ':description' => $data['description'],
                ':id_membre'   => $id_membre
            ]);
        }

        public static function updateGoodies(int $id_goodies, array $data): bool
        {
            $pdo = self::getInstance();

            $sql = "
                UPDATE goodies SET
                    nom_goodies = :nom_goodies,
                    prix        = :prix,
                    lien        = :lien,
                    description = :description
                WHERE goodies_id = :id_goodies
            ";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':id_goodies'  => $id_goodies,
                ':nom_goodies' => $data['nom_goodies'],
                ':prix'        => $data['prix'],
                ':lien'        => $data['lien'] ?? null,
                ':description' => $data['description']
            ]);
        }
        public static function deleteGoodies(int $id_goodies): bool
        {
            $pdo = self::getInstance();

            $sql = "DELETE FROM goodies WHERE goodies_id = :id_goodies";

            $stmt = $pdo->prepare($sql);

            return $stmt->execute([
                ':id_goodies' => $id_goodies
            ]);
        }
        public static function fetchGoodies(?int $id_goodies = null , ?string $id_membre = null): array
        {
            $pdo = self::getInstance();

            $sql = "
                SELECT
                    g.*,
                    m.nom,
                    m.prenom
                FROM goodies g
                INNER JOIN subscribers m
                ON m.id_membre COLLATE utf8mb4_unicode_ci 
                = g.id_membre COLLATE utf8mb4_unicode_ci 
                WHERE 1=1
            ";

            $params = [];

            // 🔎 Si un ID est fourni
            if ($id_goodies !== null) {
                $sql .= " AND goodies_id = :id_goodies";
                $params[':id_goodies'] = $id_goodies;
            }

            $sql .= " ORDER BY nom_goodies ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }






    }   





        




