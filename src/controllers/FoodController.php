<?php
namespace Controllers;

use Services\FoodService;

class FoodController {
    private $foodService;

    public function __construct(FoodService $foodService) {
        $this->foodService = $foodService;
    }

    public function getAllAnimals() {
        return $this->foodService->getAllAnimals();
    }

    public function addFoodRecord($animalId, $foodGiven, $foodQuantity, $dateGiven) {
        return $this->foodService->addFoodRecord($animalId, $foodGiven, $foodQuantity, $dateGiven);
    }
}
