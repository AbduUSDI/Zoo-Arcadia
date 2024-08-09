<?php
namespace Services;

use Interfaces\HabitatRepositoryInterface;
use Interfaces\HabitatServiceInterface;

class HabitatService implements HabitatServiceInterface {
    private $habitatRepository;

    public function __construct(HabitatRepositoryInterface $habitatRepository) {
        $this->habitatRepository = $habitatRepository;
    }

    public function getAllHabitats() {
        return $this->habitatRepository->getAllHabitats();
    }

    public function getHabitatById($habitatId) {
        return $this->habitatRepository->getHabitatById($habitatId);
    }

    public function updateHabitat($id, $name, $description, $image) {
        if ($image) {
            $imagePath = $this->habitatRepository->uploadImage($image);
            $this->habitatRepository->updateHabitat($id, $name, $description, $imagePath);
        } else {
            $this->habitatRepository->updateHabitatWithoutImage($id, $name, $description);
        }
    }

    public function uploadImage($file) {
        return $this->habitatRepository->uploadImage($file);
    }

    public function addHabitat($name, $description, $image) {
        $imagePath = $this->habitatRepository->uploadImage($image);
        return $this->habitatRepository->addHabitat($name, $description, $imagePath);
    }

    public function deleteHabitat($id) {
        return $this->habitatRepository->deleteHabitat($id);
    }

    public function getAnimalsByHabitat($id) {
        return $this->habitatRepository->getAnimalsByHabitat($id);
    }

    public function getApprovedComments($id) {
        return $this->habitatRepository->getApprovedComments($id);
    }

    public function getAllHabitatComments() {
        return $this->habitatRepository->getAllHabitatComments();
    }

    public function deleteHabitatComment($comment_id) {
        return $this->habitatRepository->deleteHabitatComment($comment_id);
    }

    public function approveHabitatComment($comment_id) {
        return $this->habitatRepository->approveHabitatComment($comment_id);
    }

    public function submitHabitatComment($habitatId, $vetId, $comment) {
        return $this->habitatRepository->submitHabitatComment($habitatId, $vetId, $comment);
    }
}
