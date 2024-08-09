<?php
namespace Interfaces;

interface ZooHoursRepositoryInterface {
    public function getAllHours();
    public function updateHours($open, $close,$closed, $id);
}
