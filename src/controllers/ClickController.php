<?php 
namespace Controllers;

use Services\ClickService;

class ClickController {
    private $clickService;
    public function __construct(ClickService $clickService) {
        $this->clickService = $clickService;
    }
    public function getClicks($animalId) {
        return $this->clickService->getClicks($animalId);
    }
    public function recordClick($animalId) {
        return $this->clickService->recordClick($animalId);
    }
}