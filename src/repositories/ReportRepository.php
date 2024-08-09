<?php
// Repositories/ReportRepository.php
namespace Repositories;

use Interfaces\ReportRepositoryInterface;
use PDO;
use Exception;

class ReportRepository implements ReportRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }
    public function ajouterRapports($vet_id, $animal_id,  $visit_date, $health_status, $food_given, $food_quantity, $details) {
        // Vérification de l'existence du vet_id dans la table users
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE id = :vet_id");
        $stmt->bindParam(':vet_id', $vet_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("L'utilisateur avec l'ID $vet_id n'existe pas.");
        }
    
        $stmt = $this->db->prepare("INSERT INTO vet_reports (animal_id, vet_id, health_status, food_given, food_quantity, visit_date, details) VALUES (:animal_id, :vet_id, :health_status, :food_given, :food_quantity, :visit_date, :details)");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':vet_id', $vet_id, PDO::PARAM_INT);
        $stmt->bindParam(':health_status', $health_status, PDO::PARAM_STR);
        $stmt->bindParam(':food_given', $food_given, PDO::PARAM_STR);
        $stmt->bindParam(':food_quantity', $food_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':visit_date', $visit_date, PDO::PARAM_STR);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    public function updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details) {
        // Vérification de l'existence de l'animal_id
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM animals WHERE id = :animal_id");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->execute();
    
        if ($stmt->fetchColumn() == 0) {
            throw new Exception("L'animal avec l'ID $animal_id n'existe pas.");
        }
    
        $query = "UPDATE vet_reports 
                  SET animal_id = :animal_id, vet_id = :vet_id, visit_date = :visit_date, 
                      health_status = :health_status, food_given = :food_given, 
                      food_quantity = :food_quantity, details = :details 
                  WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':vet_id', $vet_id, PDO::PARAM_INT);
        $stmt->bindParam(':visit_date', $visit_date, PDO::PARAM_STR);
        $stmt->bindParam(':health_status', $health_status, PDO::PARAM_STR);
        $stmt->bindParam(':food_given', $food_given, PDO::PARAM_STR);
        $stmt->bindParam(':food_quantity', $food_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':details', $details, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    
    
    public function getReports($visitDate = null, $animalId = null) {
        $query = "SELECT vr.id, a.name as animal_name, vr.health_status, vr.food_given, vr.food_quantity, vr.visit_date, vr.details 
                  FROM vet_reports vr
                  JOIN animals a ON vr.animal_id = a.id
                  WHERE 1=1";
        
        $params = [];
    
        if ($visitDate) {
            $query .= " AND vr.visit_date = :visit_date";
            $params[':visit_date'] = $visitDate;
        }
    
        if ($animalId) {
            $query .= " AND vr.animal_id = :animal_id";
            $params[':animal_id'] = $animalId;
        }
    
        $stmt = $this->db->prepare($query);
    
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
    
        $stmt->execute();
        
        $reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        return $reports !== false ? $reports : [];  // Retourner un tableau vide si aucune donnée n'est trouvée
    }
    public function getReportById($id) {
        $query = "SELECT vr.id, vr.animal_id, a.name as animal_name, vr.health_status, vr.food_given, vr.food_quantity, vr.visit_date, vr.details 
                  FROM vet_reports vr
                  JOIN animals a ON vr.animal_id = a.id
                  WHERE vr.id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getDistinctDates() {
        $stmt = $this->db->prepare("SELECT DISTINCT visit_date FROM vet_reports ORDER BY visit_date DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAnimals() {
        $stmt = $this->db->prepare("SELECT id, name FROM animals ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteReport($id) {
        $stmt = $this->db->prepare("DELETE FROM vet_reports WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
