<?php
namespace Repositories;

use PDO;
use Interfaces\FoodRepositoryInterface;

class FoodRepository implements FoodRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }
    public function addFoodRecord($animal_id, $food_given, $food_quantity, $date_given) {
        $sql = "INSERT INTO food (animal_id, food_given, food_quantity, date_given) VALUES (:animal_id, :food_given, :food_quantity, :date_given)";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':food_given', $food_given, PDO::PARAM_STR);
        $stmt->bindParam(':food_quantity', $food_quantity, PDO::PARAM_INT);
        $stmt->bindParam(':date_given', $date_given, PDO::PARAM_STR);
        $stmt->execute();
    }
    public function getAllAnimals() {
        $stmt = $this->db->prepare("SELECT animals.*, habitats.name AS habitat_name, animals.image FROM animals LEFT JOIN habitats ON animals.habitat_id = habitats.id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
