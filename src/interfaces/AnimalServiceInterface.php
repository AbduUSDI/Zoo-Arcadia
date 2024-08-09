<?php
namespace Interfaces;

interface AnimalServiceInterface {
    public function getAll();
    public function ajouterLike($animal_id);
    public function ajouterAvis($visitorName, $reviewText, $animalId);
    public function getAvisAnimaux($animal_id);
    public function getDetailsAnimal($animal_id);
    public function getRapportsAnimalParId($animal_id);
    public function getAnimalParHabitat($habitat_id);
    public function getListeAllHabitats();
    public function updateAvecImage($data);
    public function updateSansImage($data);
    public function delete($animalId);
    public function add($data);
    public function getNourritureAnimaux($animal_id);
}
