<?php
namespace Services;

use Interfaces\ReviewRepositoryInterface;
use Interfaces\ReviewServiceInterface;

class ReviewService implements ReviewServiceInterface {
    private $reviewRepository;

    public function __construct(ReviewRepositoryInterface $reviewRepository) {
        $this->reviewRepository = $reviewRepository;
    }

    public function addReview($visitor_name, $subject, $review_text, $animal_id = null) {
        $this->reviewRepository->addReview($visitor_name, $subject, $review_text, $animal_id);
    }

    public function approveReview($id) {
        $this->reviewRepository->approveReview($id);
    }

    public function deleteReview($id) {
        $this->reviewRepository->deleteReview($id);
    }

    public function getApprovedReviews() {
        return $this->reviewRepository->getApprovedReviews();
    }

    public function getAllReviews() {
        return $this->reviewRepository->getAllReviews();
    }

    public function getReviewById($id) {
        return $this->reviewRepository->getReviewById($id);
    }

    public function updateReview($id, $visitorName, $subject, $reviewText) {
        $this->reviewRepository->updateReview($id, $visitorName, $subject, $reviewText);
    }
}
