<?php
namespace Services;

use Repositories\ClickRepository;

class ClickService {
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
}
