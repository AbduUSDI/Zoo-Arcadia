<?php
namespace Interfaces;

interface FoodRepositoryInterface {
    public function addFoodRecord($animalId, $foodGiven, $foodQuantity, $dateGiven);
    public function getAllAnimals();
}
