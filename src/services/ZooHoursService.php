<?php
namespace Services;

use Interfaces\ZooHoursRepositoryInterface;
use Interfaces\ZooHoursServiceInterface;

class ZooHoursService implements ZooHoursServiceInterface {
    private $zooHoursRepository;

    public function __construct(ZooHoursRepositoryInterface $zooHoursRepository) {
        $this->zooHoursRepository = $zooHoursRepository;
    }

    public function getAllHours() {
        return $this->zooHoursRepository->getAllHours();
    }

    public function updateHours($open, $close,$closed, $id) {
        return $this->zooHoursRepository->updateHours($open, $close,$closed, $id);
    }
}
