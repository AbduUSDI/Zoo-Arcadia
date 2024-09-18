<?php
namespace Services;

use Interfaces\ClickServiceInterface;
use Repositories\ClickRepository;

class ClickService implements ClickServiceInterface {
    private $clickRepository;

    public function __construct(ClickRepository $clickRepository) {
        $this->clickRepository = $clickRepository;
    }

    public function recordClick($animalId) {
        $this->clickRepository->recordClick($animalId);
    }

    public function getClicks($animalId) {
        return $this->clickRepository->getClicks($animalId);
    }
    public function getTopThreeAnimalsByClicks() {
        // Requête MongoDB pour récupérer les trois premiers animaux triés par clics
        $options = ['sort' => ['clicks' => -1], 'limit' => 3];
        $topAnimals = $this->clickRepository->find([], $options);
        
        return iterator_to_array($topAnimals);
    }
    
}
