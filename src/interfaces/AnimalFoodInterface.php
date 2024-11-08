<?php
namespace Interfaces;

interface AnimalFoodInterface {
    public function donnerNourriture($animal_id, $food_given, $food_quantity, $date_given);
    public function getNourritureAnimaux($animal_id);
}
