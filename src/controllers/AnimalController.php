<?php
namespace Controllers;

use Services\AnimalService;
use Services\ClickService;

class AnimalController {
    private $animalService;
    private $clickService;

    public function __construct(AnimalService $animalService, ClickService $clickService) {
        $this->animalService = $animalService;
        $this->clickService = $clickService;
    }

    public function getAllAnimals() {
        return $this->animalService->getAll();
    }

    public function addLike($animal_id) {
        $this->animalService->ajouterLike($animal_id);
    }

    public function addReview($visitorName, $reviewText, $animalId) {
        $this->animalService->ajouterAvis($visitorName, $reviewText, $animalId);
    }

    public function getAnimalReviews($animal_id) {
        return $this->animalService->getAvisAnimaux($animal_id);
    }

    public function getAnimalDetails($animal_id) {
        return $this->animalService->getDetailsAnimal($animal_id);
    }
    public function getReportsByAnimalId($animal_id) {
        return $this->animalService->getRapportsAnimalParId($animal_id);
    }
    public function getAnimalsByHabitat($habitat_id) {
        $animals = $this->animalService->getAnimalParHabitat($habitat_id);
        foreach ($animals as &$animal) {
            try {
                $animal['clicks'] = $this->clickService->getClicks($animal['id']);
            } catch (\Exception $e) {
                $animal['clicks'] = 0;
            }
        }
        return $animals;
    }

    public function getAllHabitats() {
        return $this->animalService->getListeAllHabitats();
    }
    public function uploadImage($file) {
        if ($file['error'] == UPLOAD_ERR_OK) {
            $image = time() . '_' . $file['name'];
            move_uploaded_file($file['tmp_name'], '../../../../assets/uploads/' . $image);
            return $image;
        }
        return null;
    }
    public function updateAnimalWithImage($data) {
        return $this->animalService->updateAvecImage($data);
    }

    public function updateAnimalWithoutImage($data) {
        return $this->animalService->updateSansImage($data);
    }

    public function deleteAnimal($animalId) {
        return $this->animalService->delete($animalId);
    }

    public function addAnimal($data) {
        return $this->animalService->add($data);
    }
    public function getAnimalFoodRecords($animal_id) {
        return $this->animalService->getNourritureAnimaux($animal_id);
    }
    public function getTotalLikes($animals) {
        return $this->animalService->getTotalLikes($animals);
    }

    public function getTotalClicks($animals) {
        return $this->animalService->getTotalClicks($animals);
    }
    public function getTopThreeAnimalsByClicks() {
        // Obtenir les 3 animaux les plus cliqués de MongoDB
        $topAnimalsClicks = $this->clickService->getTopThreeAnimalsByClicks();
    
        $topAnimals = [];
        
        // Récupérer les informations des animaux de MySQL en utilisant leurs IDs
        foreach ($topAnimalsClicks as $animalClick) {
            $animalDetails = $this->animalService->getDetailsAnimal($animalClick['animal_id']); // Utilise la méthode du service
            if ($animalDetails) {
                $animalDetails['clicks'] = $animalClick['clicks']; // Ajouter le nombre de clics aux détails de l'animal
                $topAnimals[] = $animalDetails;
            }
        }
    
        return $topAnimals;
    }
}
