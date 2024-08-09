<?php
namespace Interfaces;

interface ZooHoursServiceInterface {
    public function getAllHours();
    public function updateHours($open, $close,$closed, $id);
}
