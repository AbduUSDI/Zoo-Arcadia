<?php
namespace Repositories;

use PDO;
use Interfaces\ReviewRepositoryInterface;

class ReviewRepository implements ReviewRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function addReview($visitor_name, $subject, $review_text, $animal_id = null) {
        $stmt = $this->db->prepare("INSERT INTO reviews (animal_id, visitor_name, subject, review_text, approved) VALUES (:animal_id, :visitor_name, :subject, :review_text, 0)");
        $stmt->bindParam(':animal_id', $animal_id, PDO::PARAM_INT);
        $stmt->bindParam(':visitor_name', $visitor_name, PDO::PARAM_STR);
        $stmt->bindParam(':subject', $subject, PDO::PARAM_STR);
        $stmt->bindParam(':review_text', $review_text, PDO::PARAM_STR);
        $stmt->execute();
    }

    public function approveReview($id) {
        $stmt = $this->db->prepare("UPDATE reviews SET approved = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function deleteReview($id) {
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getApprovedReviews() {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE approved = TRUE ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllReviews() {
        $stmt = $this->db->prepare("SELECT * FROM reviews ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReviewById($id) {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateReview($id, $visitorName, $subject, $reviewText) {
        $stmt = $this->db->prepare("UPDATE reviews SET visitor_name = ?, subject = ?, review_text = ? WHERE id = ?");
        $stmt->execute([$visitorName, $subject, $reviewText, $id]);
    }
}
