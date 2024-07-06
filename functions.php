<?php
/**
 * Classe pour établir une connexion à la base de données
 */
class Database {
    private $hôte = 'localhost';
    private $nom_base_de_donnée = 'zoo_arcadia';
    private $identifiant = 'Abdurahman';
    private $mot_de_passe = 'Abdufufu2525+';
    private $connexion;

    public function connect() {
        // Initialisation de la connexion à null car au début pas de connexion active
        $this->connexion = null;
    
        try {
            // Création d'une nouvelle instance de PDO pour se connecter à la base de données
            // Remplacer les informations de connexion avec les propriétés de l'objet
            $this->connexion = new PDO(
                "mysql:host=" . $this->hôte . ";dbname=" . $this->nom_base_de_donnée, 
                $this->identifiant, 
                $this->mot_de_passe
            );
    
            // Configurer PDO pour lancer des exceptions en cas d'erreur
            $this->connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        } catch(PDOException $erreur) {
            // En cas d'erreur, afficher un message compréhensible
            echo "Erreur de connexion à la base de donnée : " . $erreur->getMessage();
        }
    
        // Retourner l'objet de connexion PDO ou null si la connexion a échoué
        return $this->connexion;
    }
    
}
/**
 * Classe pour toutes les méthodes et définitions en rapport avec les animaux du zoo
 */
class Animal {
    private $db;
    // Création d'une définition pour la méthode ajouterNourriture
    private $nourriture = 'food';
    private $table = 'vet_reports';
    // Constructeur qui initialise la connexion à la BDD
    public function __construct($db) {
        $this->db = $db;
    }
    // Méthode (CRUD = Read) préparée pour récupérer toutes les informations des animaux : habitat, image et id de l'animal et de l'habitat
    public function getAll() {
        $stmt = $this->db->prepare("SELECT animals.*, habitats.name AS habitat_name, animals.image FROM animals LEFT JOIN habitats ON animals.habitat_id = habitats.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour ajouter un like sur un animal : sur la page d'un animal on peut cliquer sur un bouton like pour améliorer le score de l'animal
    public function ajouterLike($animal_id) {
        $stmt = $this->db->prepare("UPDATE animals SET likes = likes + 1 WHERE id = :id");
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour qu'un visiteur puisse ajouter un avis qui devra être approuver par l'employé(e)
    public function ajouterAvis($visitorName, $subject, $reviewText, $animalId) {
        $stmt = $this->db->prepare("INSERT INTO reviews (visitor_name, subject, review_text, animal_id) VALUES (:visitor_name, :subject, :review_text, :animal_id)");
        $stmt->bindParam(':visitor_name', $visitorName, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':review_text', $reviewText, PDO::PARAM_STR);
        $stmt->bindParam(':animal_id', $animalId, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour récupérer les avis et commentaires visiteur approuvés par l'employé en utilisant l'id de l'animal pour afficher sur la bonne colonne animal
    public function getAvisAnimaux($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE animal_id = :animal_id AND approved = 1");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer les details des animaux par id
    public function getDetailsAnimal($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM animals WHERE id = :id");
        $stmt->bindParam(':id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer tout les rapports vétérinaires existants
    public function getReports() {
        $stmt = $this->db->prepare("SELECT vet_reports.*, animals.name AS animal_name FROM vet_reports JOIN animals ON vet_reports.animal_id = animals.id ORDER BY vet_reports.visit_date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode pour appliquer les filtres par date et/ou par animal : ar = animal reports ; a = animals
    public function appliquerFiltres($selectedDate, $selectedAnimalId) {
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
    // Sous-méthode préparée de la méthode appliquerFiltres pour exécuter l'algorithme
    public function filtresDateAnimal($selectedDate, $selectedAnimalId) {
        list($query, $params) = $this->appliquerFiltres($selectedDate, $selectedAnimalId);
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer les rapports vétérinaire par animal en utilisant $animal_id
    public function getRapportsAnimal($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM vet_reports WHERE animal_id = :animal_id ORDER BY visit_date DESC");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour que le vétérinaire puisse ajouter un rapport sur un animal
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
    // Méthode préparée pour supprimer un rapport vétérinaire
    public function deleteRapport($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        
        if($stmt->execute()) {
            return true;
        }
        return false;
    }
    // Méthode préparée pour sélectionner un animal par son habitat et son id
    public function getParHabitat($habitat_id) {
        $stmt = $this->db->prepare("SELECT animals.*, habitats.name AS habitat_name, animals.image FROM animals LEFT JOIN habitats ON animals.habitat_id = habitats.id WHERE animals.habitat_id = ?");
        $stmt->execute([$habitat_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer les habitats et les utiliser comme des options pour une liste déroulante pour le formulaire d'ajout d'animal
    public function getAllHabitats() {
        $stmt = $this->db->prepare("SELECT * FROM habitats");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode (CRUD = Update) préparée pour mettre à jour un animal avec une image
    public function updateAvecImage($data) {
        $stmt = $this->db->prepare("UPDATE animals SET name = ?, species = ?, habitat_id = ?, image = ? WHERE id = ?");
        return $stmt->execute($data);
    }

    // Méthode (CRUD = Update) préparée pour mettre à jour un animal sans changer l'image
    public function updateSansImage($data) {
        $stmt = $this->db->prepare("UPDATE animals SET name = ?, species = ?, habitat_id = ? WHERE id = ?");
        return $stmt->execute($data);
    }
    // Méthode (CRUD = Delete) préparée pour supprimer un animal par son id
    public function delete($animalId) {
        $stmt = $this->db->prepare("DELETE FROM animals WHERE id = ?");
        $stmt->execute([$animalId]);
    }
    // Méthode (CRUD = Create) pour ajouter un nouvel animal
    public function add($data) {
        $stmt = $this->db->prepare("INSERT INTO animals (name, species, habitat_id, image) VALUES (?, ?, ?, ?)");
        $stmt->execute($data);
    }
    // Méthode préparée pour donner la nourriture à un animal via un formulaire
    public function donnerNourriture($animal_id, $food_given, $food_quantity, $date_given) {
        $query = "INSERT INTO " . $this->nourriture . " (animal_id, food_given, food_quantity, date_given) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$animal_id, $food_given, $food_quantity, $date_given]);
    }
    // Méthode préparée pour récupérer les animaux existants afin de le faire apparaître sur le formulaire pour donner la nourriture
    public function getAnimaux() {
        $query = "SELECT id, name FROM animals ORDER BY name";
        $stmt = $this->db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer toutes les nourritures données dans une <div> propre à l'animal qui apparaît sous forme d'un accordéon Bootstrap
    /** f = food */
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
    // Définition du constructeur qui est relié à la base de données
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Méthode préparée pour ajouter un avis
    public function addAvis($visitor_name, $subject, $review_text, $animal_id = null) {
        $stmt = $this->db->prepare("INSERT INTO reviews (animal_id, visitor_name, subject, review_text, approved) VALUES (:animal_id, :visitor_name, :subject, :review_text, 0)");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':visitor_name', $visitor_name, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
        $stmt->execute();
    }
    // Méthode préparée pour que l'employé Approuve le commentaire ou l'avis
    public function approve($id) {
        $stmt = $this->db->prepare("UPDATE reviews SET approved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour supprimer le commentaire ou l'avis
    public function deleteAvis($id) {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour récupérer les commentaires et avis approuvés par un employé, par ordre décroissant de la date de soumission
    public function getAvisApprouvés() {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE approved = TRUE ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour afficher les commentaires et avis approuvés ou non, c'est avec cette méthode qu'on obtient les avis en attente
    public function getAvisTout() {
        $stmt = $this->db->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer un avis ou un commentaire approuvé
    public function getAvisById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode préparée pour modifier un avis ou un commentaire existant déjà approuvés
    public function updateAvis($id, $visitorName, $subject, $reviewText) {
        $stmt = $this->db->prepare("UPDATE reviews SET visitor_name = ?, subject = ?, review_text = ? WHERE id = ?");
        $stmt->execute([$visitorName, $subject, $reviewText, $id]);
    }
}
/**
 * Classe Service qui regroupe toutes les méthodes et définitions en rapport avec les services du zoo
 */
class Service {
    // Définition du constructeur qui est relié à la base de données
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Méthode préparée pour afficher tout les services existants 
    public function getServices() {
        $stmt = $this->db->prepare("SELECT * FROM services");
        $stmt->execute();
        return $stmt->fetchAll
        (PDO::FETCH_ASSOC);
    }
    // Méthode (CRUD) préparée pour ajouter un Service : Nom, description et image ; Grâce à un formulaire POST (create)
    public function ajouterService($name, $description, $image) {
        $stmt = $this->db->prepare("INSERT INTO services (name, description, image) VALUES (:name, :description, :image)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        return $stmt->execute();
    }
    // Méthode (CRUD) préparée pour mettre à jour les infos d'un service sans modifier l'image (update)
    public function updateServiceSansImage($id, $name, $description) {
        $stmt = $this->db->prepare("UPDATE services SET name = :name, description = :description WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Méthode (CRUD) préparée pour mettre à jour toutes les infos d'un services y compris l'image (update)
    public function updateServiceAvecImage($id, $name, $description, $imageName) {
        $stmt = $this->db->prepare("UPDATE services SET name = :name, description = :description, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imageName, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    // Méthode (CRUD) préparée pour supprimer un service (delete)
    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode (CRUD) préparée pour afficher les services (read)
    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode préparée reliée au "create" pour ajouter une image
    public function ajouterImage($file) {
        $fileTempPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        // Définition d'un bloqueur qui accepte seulement les image en format jpg, gif, png, jpeg
        $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDirection = '../uploads/';
            $dest_path = $uploadFileDirection . $fileName;

            if (move_uploaded_file($fileTempPath, $dest_path)) {
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
    // Définition de la base de données et du constructeur
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Méthode (CRUD) préparée pour récupérer tout les habitats existants (read)
    public function getToutHabitats() {
        $stmt = $this->db->prepare("SELECT * FROM habitats");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour afficher l'habitat sélectionné par son ID
    public function getParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM habitats WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode (CRUD) préparée pour mettre à jour les infos d'un habitat sélectionné (update)
    public function updateHabitat($id, $name, $description, $image) {
        $stmt = $this->db->prepare("UPDATE habitats SET name = :name, description = :description, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
        // Sous méthode préparée pour ajouter une image à l'habitat, l'image sera répertorié dans le dossier "uploads"
    public function uploadImage($file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $image = time() . '_' . $file['name'];
            move_uploaded_file($file['tmp_name'], '../uploads/' . $image);
            return $image;
        }
        return null;
    }
    // Méthode (CRUD) préparée pour ajouter un nouvel habitat (create)
    public function addHabitat($name, $description, $image) {
        $stmt = $this->db->prepare("INSERT INTO habitats (name, description, image) VALUES (?, ?, ?)");
        return $stmt->execute([$name, $description, $image]);
    }
    // Méthode (CRUD) préparée pour supprimer un habitat existant (delete)
    public function deleteHabitat($id) {
        $stmt = $this->db->prepare("DELETE FROM habitats WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour afficher tout les animaux d'un habitat
    public function getAnimauxParHabitat($id) {
        $stmt = $this->db->prepare("SELECT * FROM animals WHERE habitat_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour afficher le commentaires approuvés d'un habitat
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
    // Méthode préparée pour afficher tout les commentaires sur les habitats
    public function getToutHabitatsComments() {
        $stmt = $this->db->prepare("SELECT * FROM habitat_comments ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour supprimer un commentaire habitat en prenant l'id du commentaire
    public function deleteHabitatComment($comment_id) {
        $stmt = $this->db->prepare("DELETE FROM habitat_comments WHERE id = :id");
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour approuver un commentaire sur un habitat
    public function approveHabitatComment($comment_id) {
        $stmt = $this->db->prepare("UPDATE habitat_comments SET approved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $comment_id, PDO::PARAM_INT);
        $stmt->execute();
    }
    // Méthode préparée pour ajouter (vétérinaire) un commentaire sur l'habitat 
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
    // Définition de la base de données et du constructeur
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Méthode préparée pour récupérer tout les horaires existants du zoo
    public function getAllHours() {
        $stmt = $this->db->prepare("SELECT * FROM zoo_hours ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour mettre à jour les horaires du zoo
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
    // Définition de la base de données et du constructeur
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    // Méthode (CRUD) préparée pour récupérer tout les comptes utilisateurs existants du site (read)
    public function getAllUtilisateurs() {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour sélectionner un compte existant en se servant de son email comme paramètre
    public function getUtilisateurParEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode préparée pour récupérer les informations de l'utilisateur par son id
    public function getUtilisateurParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    // Méthode (CRUD) préparée pour ajouter un nouvel utilisateur (create)
    public function addUser($email, $password, $role_id, $username) {
        // Hacher le mot de passe
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->db->prepare("INSERT INTO users (email, password, role_id, username) VALUES (:email, :password, :role_id, :username)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    // Méthode (CRUD) préparée pour mettre à jour les informations d'un utilisateur (update)
    public function updateUser($id, $email, $role_id, $username, $password = null) {
        // Commencer la requête SQL
        $sql = "UPDATE users SET email = :email, role_id = :role_id, username = :username";
        
        // Créer le tableau des paramètres
        $params = [
            ':id' => $id,
            ':email' => $email,
            ':role_id' => $role_id,
            ':username' => $username
        ];
    
        // Si un nouveau mot de passe est fourni, l'ajouter à la requête
        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }
    
        // Terminer la requête SQL
        $sql .= " WHERE id = :id";
    
        // Préparer et exécuter la requête
        $stmt = $this->db->prepare($sql);
    
        // Lier les paramètres
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    
        // Exécuter la requête
        try {
            $stmt->execute();
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour de l'utilisateur : " . $e->getMessage());
            return false;
        }
        return true;
    }
    // Méthode (CRUD) préparée pour supprimer un utilisateur existant (delete)
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}

/**
 * Classe Contact qui stocke toutes les méthodes et définitions en rapport avec le formulaire de contact
 */
class Contact {
    // Définition de la base de données et du constructeur
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    // Méthode préparée pour stocker le message envoyé dans la base de données
    public function saveMessage($name, $email, $message) {
        $stmt = $this->conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
    }
}
