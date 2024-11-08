<?php
namespace Interfaces;

interface AnimalRepositoryInterface {
    public function getAll();
    public function getDetailsAnimal($animal_id);
    public function getRapportsAnimalParId($animal_id);
    public function updateAvecImage($data);
    public function updateSansImage($data);
    public function delete($animalId);
    public function add($data);
}
