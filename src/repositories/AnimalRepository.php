<?php
namespace Repositories;

use PDO;
use Interfaces\AnimalRepositoryInterface;

class AnimalRepository implements AnimalRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
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
    public function getRapportsAnimalParId($animal_id) {
        $stmt = $this->db->prepare("SELECT * FROM vet_reports WHERE animal_id = :animal_id ORDER BY visit_date DESC");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        return $stmt->execute([$animalId]);
    }
    public function add($data) {
        $stmt = $this->db->prepare("INSERT INTO animals (name, species, habitat_id, image) VALUES (?, ?, ?, ?)");
        return $stmt->execute($data);
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
