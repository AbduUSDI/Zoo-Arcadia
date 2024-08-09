<?php
namespace Services;

use Interfaces\FoodRepositoryInterface;
use Repositories\AnimalRepository;

class FoodService implements FoodRepositoryInterface {
    private $animalRepository;
    private $foodRepository;

    public function __construct(AnimalRepository $animalRepository) {
        $this->animalRepository = $animalRepository;
    }

    public function getAllAnimals() {
        return $this->animalRepository->getAll();
    }

    public function addFoodRecord($animalId, $foodGiven, $foodQuantity, $dateGiven) {
        return $this->animalRepository->donnerNourriture($animalId, $foodGiven, $foodQuantity, $dateGiven);
    }
    public function getAnimalFoods($animalId) {
        return $this->foodRepository->getAnimalFoods($animalId);
    }
}
