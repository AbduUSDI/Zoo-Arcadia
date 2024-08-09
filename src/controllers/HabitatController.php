<?php
namespace Controllers;

use Interfaces\HabitatServiceInterface;

class HabitatController {
    private $habitatService;

    public function __construct(HabitatServiceInterface $habitatService) {
        $this->habitatService = $habitatService;
    }

    public function getAllHabitats() {
        return $this->habitatService->getAllHabitats();
    }

    public function getHabitatById($id) {
        return $this->habitatService->getHabitatById($id);
    }

    public function updateHabitat($id, $name, $description, $image) {
        $this->habitatService->updateHabitat($id, $name, $description, $image);
    }
    public function addHabitat($name, $description, $image) {
        return $this->habitatService->addHabitat($name, $description, $image);
    }

    public function deleteHabitat($id) {
        return $this->habitatService->deleteHabitat($id);
    }

    public function getAnimalsByHabitat($id) {
        return $this->habitatService->getAnimalsByHabitat($id);
    }

    public function getApprovedComments($id) {
        return $this->habitatService->getApprovedComments($id);
    }

    public function getAllHabitatComments() {
        return $this->habitatService->getAllHabitatComments();
    }

    public function deleteHabitatComment($comment_id) {
        return $this->habitatService->deleteHabitatComment($comment_id);
    }

    public function approveHabitatComment($comment_id) {
        return $this->habitatService->approveHabitatComment($comment_id);
    }

    public function submitHabitatComment($habitatId, $vetId, $comment) {
        return $this->habitatService->submitHabitatComment($habitatId, $vetId, $comment);
    }
}
