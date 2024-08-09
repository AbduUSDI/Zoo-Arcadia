<?php
namespace Services;

use Interfaces\AnimalServiceInterface;
use Repositories\AnimalRepository;
use Repositories\ClickRepository;

class AnimalService implements AnimalServiceInterface {
    private $animalRepository;
    private $clickRepository;

    public function __construct(AnimalRepository $animalRepository, ClickRepository $clickRepository) {
        $this->animalRepository = $animalRepository;
        $this->clickRepository = $clickRepository;
    }
    public function getAll() {
        $animals = $this->animalRepository->getAll();
        foreach ($animals as &$animal) {
            try {
                $animal['clicks'] = $this->clickRepository->getClicks($animal['id']);
            } catch (\Exception $e) {
                $animal['clicks'] = 0;  // Valeur par défaut si les clics ne peuvent pas être récupérés
            }
        }
        return $animals;
    }
    public function ajouterLike($animal_id) {
        $this->animalRepository->ajouterLike($animal_id);
    }
    public function ajouterAvis($visitorName, $reviewText, $animalId) {
        $this->animalRepository->ajouterAvis($visitorName, $reviewText, $animalId);
    }
    public function getAvisAnimaux($animal_id) {
        return $this->animalRepository->getAvisAnimaux($animal_id);
    }
    public function getDetailsAnimal($animal_id) {
        return $this->animalRepository->getDetailsAnimal($animal_id);
    }
    public function getRapportsAnimalParId($animal_id) {
        return $this->animalRepository->getRapportsAnimalParId($animal_id);
    }
    public function getAnimalParHabitat($habitat_id) {
        return $this->animalRepository->getAnimalParHabitat($habitat_id);
    }
    public function getListeAllHabitats() {
        return $this->animalRepository->getListeAllHabitats();
    }
    public function updateAvecImage($data) {
        return $this->animalRepository->updateAvecImage($data);
    }
    public function updateSansImage($data) {
        return $this->animalRepository->updateSansImage($data);
    }

    public function delete($animalId) {
        return $this->animalRepository->delete($animalId);
    }

    public function add($data) {
        return $this->animalRepository->add($data);
    }

    public function getNourritureAnimaux($animal_id) {
        return $this->animalRepository->getNourritureAnimaux($animal_id);
    }
    public function getTotalLikes($animals) {
        $totalLikes = 0;
        foreach ($animals as $animal) {
            $totalLikes += $animal['likes'];
        }
        return $totalLikes;
    }

    public function getTotalClicks($animals) {
        $totalClicks = 0;
        foreach ($animals as $animal) {
            $totalClicks += $animal['clicks'];
        }
        return $totalClicks;
    }
}
