<?php
namespace Controllers;

use Interfaces\ZooHoursServiceInterface;

class ZooHoursController {
    private $zooHoursService;

    public function __construct(ZooHoursServiceInterface $zooHoursService) {
        $this->zooHoursService = $zooHoursService;
    }

    public function getAllHours() {
        return $this->zooHoursService->getAllHours();
    }

    public function updateHours($open, $close,$closed, $id) {
        return $this->zooHoursService->updateHours($open, $close,$closed, $id);
    }
}
