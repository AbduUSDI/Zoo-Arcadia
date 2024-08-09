<?php
namespace Repositories;

use PDO;
use Interfaces\HabitatRepositoryInterface;

class HabitatRepository implements HabitatRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllHabitats() {
        $stmt = $this->db->prepare("SELECT * FROM habitats");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getHabitatById($id) {
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

    public function updateHabitatWithoutImage($id, $name, $description) {
        $stmt = $this->db->prepare("UPDATE habitats SET name = :name, description = :description WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function uploadImage($file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $image = time() . '_' . $file['name'];
            move_uploaded_file($file['tmp_name'], '../../../../assets/uploads/' . $image);
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

    public function getAnimalsByHabitat($id) {
        $stmt = $this->db->prepare("SELECT * FROM animals WHERE habitat_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getApprovedComments($id) {
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

    public function getAllHabitatComments() {
        $stmt = $this->db->prepare("
            SELECT habitat_comments.*, users.username AS vet_username
            FROM habitat_comments
            JOIN users ON habitat_comments.vet_id = users.id
            ORDER BY habitat_comments.created_at DESC
        ");
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
