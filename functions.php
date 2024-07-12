<?php
/**
 * Classe Database pour établir une connexion à la base de données
 */
class Database {
    private $hôte = 'localhost';
    private $nom_base_de_donnée = 'zoo_arcadia';
    private $identifiant = 'Abdurahman';
    private $mot_de_passe = 'Abdufufu2525+';
    private $connexion;

    public function connect() {

        $this->connexion = null;
    
        try {
            $this->connexion = new PDO(
                "mysql:host=" . $this->hôte . ";dbname=" . $this->nom_base_de_donnée, 
                $this->identifiant, 
                $this->mot_de_passe
            );
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch(PDOException $erreur) {
            echo "Erreur de connexion à la base de donnée : " . $erreur->getMessage();
        }
        return $this->connexion;
    }
    
}
/**
 * Classe pour toutes les méthodes et définitions en rapport avec les animaux du zoo
 */
class Animal {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }
    public function getAll() {
        $stmt = $this->db->prepare("SELECT animals.*, habitats.name AS habitat_name, animals.image FROM animals LEFT JOIN habitats ON animals.habitat_id = habitats.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ajouterLike($animal_id) {
        $stmt = $this->db->prepare("UPDATE animals SET likes = likes + 1 WHERE id = :id");
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function ajouterAvis($visitorName, $reviewText, $animalId) {
        $stmt = $this->db->prepare("INSERT INTO reviews (visitor_name, review_text, animal_id) VALUES (:visitor_name, :review_text, :animal_id)");
        $stmt->bindParam(':visitor_name', $visitorName, PDO::PARAM_STR);
        $stmt->bindParam(':review_text', $reviewText, PDO::PARAM_STR);
        $stmt->bindParam(':animal_id', $animalId, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function getAvisAnimaux($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE animal_id = :animal_id AND approved = 1");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getDetailsAnimal($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM animals WHERE id = :id");
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getReports() {
        $stmt = $this->db->prepare("SELECT vet_reports.*, animals.name AS animal_name FROM vet_reports JOIN animals ON vet_reports.animal_id = animals.id ORDER BY vet_reports.visit_date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode pour appliquer les filtres par date et/ou par animal 
    public function appliquerFiltres($selectedDate, $selectedAnimalId) {  // ar = vet_reports table
        $query = "SELECT ar.*, a.name as animal_name FROM vet_reports ar JOIN animals a ON ar.animal_id = a.id";
        $conditions = [];
        $params = [];
        if (!empty($selectedDate)) {
            $conditions[] = "ar.visit_date = ?";
            $params[] = $selectedDate;
        }
        if (!empty($selectedAnimalId)) {
            $conditions[] = "ar.animal_id = ?";
            $params[] = $selectedAnimalId;
        }
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(' AND ', $conditions);
        }
        $query .= " ORDER BY ar.visit_date DESC"; // Tri par ordre décroissant des dates de rapports vétérinaires

        return [$query, $params];
    }
    public function getRapportsAnimalParId($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM vet_reports WHERE animal_id = :animal_id ORDER BY visit_date DESC");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function ajouterRapports($animal_id, $vet_id, $healthStatus, $foodGiven, $foodQuantity, $visitDate, $details) {
        $stmt = $this->db->prepare("INSERT INTO vet_reports (animal_id, vet_id, health_status, food_given, food_quantity, visit_date, details) VALUES (:animal_id, :vet_id, :health_status, :food_given, :food_quantity, :visit_date, :details)");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':vet_id', $vet_id, PDO::PARAM_INT);
        $stmt->bindParam(':health_status', $healthStatus, PDO::PARAM_STR);
        $stmt->bindParam(':food_given', $foodGiven, PDO::PARAM_STR);
        $stmt->bindParam(':food_quantity', $foodQuantity, PDO::PARAM_INT);
        $stmt->bindParam(':visit_date', $visitDate, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function deleteRapport($id) {
        $stmt = $this->db->prepare("DELETE FROM vet_reports WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function getAnimalParHabitat($habitat_id) {
        $stmt = $this->db->prepare("SELECT animals.*, habitats.name AS habitat_name, animals.image FROM animals LEFT JOIN habitats ON animals.habitat_id = habitats.id WHERE animals.habitat_id = ?");
        $stmt->execute([$habitat_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getListeAllHabitats() {
        $stmt = $this->db->prepare("SELECT * FROM habitats");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateAvecImage($data) {
        $stmt = $this->db->prepare("UPDATE animals SET name = ?, species = ?, habitat_id = ?, image = ? WHERE id = ?");
        return $stmt->execute($data);
    }
    public function updateSansImage($data) {
        $stmt = $this->db->prepare("UPDATE animals SET name = ?, species = ?, habitat_id = ? WHERE id = ?");
        return $stmt->execute($data);
    }
    public function delete($animalId) {
        $stmt = $this->db->prepare("DELETE FROM animals WHERE id = ?");
        $stmt->execute([$animalId]);
    }
    public function add($data) {
        $stmt = $this->db->prepare("INSERT INTO animals (name, species, habitat_id, image) VALUES (?, ?, ?, ?)");
        $stmt->execute($data);
    }
    public function donnerNourriture($animal_id, $food_given, $food_quantity, $date_given) {
        $sql = "INSERT INTO food (animal_id, food_given, food_quantity, date_given) VALUES (:animal_id, :food_given, :food_quantity, :date_given)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':food_given', $food_given, PDO::PARAM_STR);
        $stmt->bindParam(':food_quantity', $food_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':date_given', $date_given, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function getNourritureAnimaux($animal_id) {
        $query = "
            SELECT f.food_given, f.food_quantity, f.date_given 
            FROM food f 
            WHERE f.animal_id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$animal_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
/**
 * Classe pour les définitions et les méthodes en rapport avec les avis et commentaires des visiteurs
 */
class Review {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function addAvis($visitor_name, $subject, $review_text, $animal_id = null) {
        $stmt = $this->db->prepare("INSERT INTO reviews (animal_id, visitor_name, subject, review_text, approved) VALUES (:animal_id, :visitor_name, :subject, :review_text, 0)");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':visitor_name', $visitor_name, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function approve($id) {
        $stmt = $this->db->prepare("UPDATE reviews SET approved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function deleteAvis($id) {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function getAvisApprouvés() {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE approved = TRUE ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvisTout() {
        $stmt = $this->db->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAvisById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateAvis($id, $visitorName, $subject, $reviewText) {
        $stmt = $this->db->prepare("UPDATE reviews SET visitor_name = ?, subject = ?, review_text = ? WHERE id = ?");
        $stmt->execute([$visitorName, $subject, $reviewText, $id]);
    }
}
/**
 * Classe Service qui regroupe toutes les méthodes et définitions en rapport avec les services du zoo
 */
class Service {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getServices() {
        $stmt = $this->db->prepare("SELECT * FROM services");
        $stmt->execute();
        return $stmt->fetchAll
        (PDO::FETCH_ASSOC);
    }
    public function ajouterService($name, $description, $image) {
        $stmt = $this->db->prepare("INSERT INTO services (name, description, image) VALUES (:name, :description, :image)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        return $stmt->execute();
    }
    public function updateServiceSansImage($id, $name, $description) {
        $stmt = $this->db->prepare("UPDATE services SET name = :name, description = :description WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function updateServiceAvecImage($id, $name, $description, $imageName) {
        $stmt = $this->db->prepare("UPDATE services SET name = :name, description = :description, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imageName, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function ajouterImage($file) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDirection = '../uploads/';
            $dest_path = "{$uploadFileDirection}{$fileName}";

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return $fileName;
            } else {
                throw new Exception('Une erreur est survenue au moment de l\'enregistrement de l\'image. Assurez vous que le dossier upload existe bien dans votre répertoire.');
            }
        } else {
            throw new Exception('Type de fichier incorrect veuillez insérer le bon type de fichier : ' . implode(',', $allowedfileExtensions));
        }
    }
}
/**
 * Classe regroupant toutes les méthodes et définitions en rapport avec les habitats du zoo
 */
class Habitat {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getToutHabitats() {
        $stmt = $this->db->prepare("SELECT * FROM habitats");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM habitats WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function updateHabitat($id, $name, $description, $image) {
        $stmt = $this->db->prepare("UPDATE habitats SET name = :name, description = :description, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function uploadImage($file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $image = time() . '_' . $file['name'];
            move_uploaded_file($file['tmp_name'], '../uploads/' . $image);
            return $image;
        }
        return null;
    }
    public function addHabitat($name, $description, $image) {
        $stmt = $this->db->prepare("INSERT INTO habitats (name, description, image) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $description, $image]);
    }
    public function deleteHabitat($id) {
        $stmt = $this->db->prepare("DELETE FROM habitats WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function getAnimauxParHabitat($id) {
        $stmt = $this->db->prepare("SELECT * FROM animals WHERE habitat_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getCommentsApprouvés($id) {
        $stmt = $this->db->prepare("SELECT habitat_comments.comment, habitat_comments.created_at, users.username 
                                    FROM habitat_comments 
                                    JOIN users ON habitat_comments.vet_id = users.id 
                                    WHERE habitat_comments.habitat_id = :id 
                                    AND habitat_comments.approved = 1 
                                    ORDER BY habitat_comments.created_at DESC");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getToutHabitatsComments() {
        $stmt = $this->db->prepare("SELECT * FROM habitat_comments ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function deleteHabitatComment($comment_id) {
        $stmt = $this->db->prepare("DELETE FROM habitat_comments WHERE id = :id");
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function approveHabitatComment($comment_id) {
        $stmt = $this->db->prepare("UPDATE habitat_comments SET approved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    public function submitHabitatComment($habitatId, $vetId, $comment) {
        $stmt = $this->db->prepare("INSERT INTO habitat_comments (habitat_id, vet_id, comment, approved) VALUES (:habitat_id, :vet_id, :comment, 0)");
        $stmt->bindParam(':habitat_id', $habitatId, PDO::PARAM_INT);
        $stmt->bindParam(':vet_id', $vetId, PDO::PARAM_INT);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->execute();
    }
}
/**
 * Classe regroupant toutes les définitions et méthodes en rapport avec les horaires du zoo
 */
class ZooHours {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getAllHours() {
        $stmt = $this->db->prepare("SELECT * FROM zoo_hours ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateHours($open, $close, $id) {
        $stmt = $this->db->prepare("UPDATE zoo_hours SET open_time = ?, close_time = ? WHERE id = ?");
        $stmt->execute([$open, $close, $id]);
        return $stmt->rowCount();
    }
}
/**
 * Classe User regroupant toutes les méthodes et définitions en rapport avec les utilisateurs
 */
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getAllUtilisateurs() {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUtilisateurParEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUtilisateurParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addUser($email, $password, $role_id, $username) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->db->prepare("INSERT INTO users (email, password, role_id, username) VALUES (:email, :password, :role_id, :username)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function updateUser($id, $email, $role_id, $username, $password = null) {
        $sql = "UPDATE users SET email = :email, role_id = :role_id, username = :username";
    
        $params = [
            ':id' => $id,
            ':email' => $email,
            ':role_id' => $role_id,
            ':username' => $username
        ];
    
        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }
    
        $sql .= " WHERE id = :id";
    
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la mise à jour de l'utilisateur : " . $erreur->getMessage());
            return false;
        }
    }
}

/**
 * Classe Contact qui stocke toutes les méthodes et définitions en rapport avec le formulaire de contact
 */
class Contact {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function saveMessage($name, $email, $message) {
        $stmt = $this->conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
    }
}
