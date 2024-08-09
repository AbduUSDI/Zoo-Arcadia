<?php
namespace Interfaces;

interface ReviewServiceInterface {
    public function addReview($visitor_name, $subject, $review_text, $animal_id = null);
    public function approveReview($id);
    public function deleteReview($id);
    public function getApprovedReviews();
    public function getAllReviews();
    public function getReviewById($id);
    public function updateReview($id, $visitorName, $subject, $reviewText);
}
