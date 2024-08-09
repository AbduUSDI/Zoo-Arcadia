<?php
namespace Controllers;

use Interfaces\ReviewServiceInterface;

class ReviewController {
    private $reviewService;

    public function __construct(ReviewServiceInterface $reviewService) {
        $this->reviewService = $reviewService;
    }

    public function addReview($visitor_name, $subject, $review_text, $animal_id = null) {
        $this->reviewService->addReview($visitor_name, $subject, $review_text, $animal_id);
    }

    public function approveReview($id) {
        $this->reviewService->approveReview($id);
    }

    public function deleteReview($id) {
        $this->reviewService->deleteReview($id);
    }

    public function getApprovedReviews() {
        return $this->reviewService->getApprovedReviews();
    }

    public function getAllReviews() {
        return $this->reviewService->getAllReviews();
    }

    public function getReviewById($id) {
        return $this->reviewService->getReviewById($id);
    }

    public function updateReview($id, $visitorName, $subject, $reviewText) {
        $this->reviewService->updateReview($id, $visitorName, $subject, $reviewText);
    }
}
