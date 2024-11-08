<?php
namespace Interfaces;

interface AnimalReviewInterface {
    public function ajouterLike($animal_id);
    public function ajouterAvis($visitorName, $reviewText, $animalId);
  
}
