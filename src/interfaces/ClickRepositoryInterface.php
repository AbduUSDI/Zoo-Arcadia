<?php
namespace Interfaces;

use MongoDB\Collection;

interface ClickRepositoryInterface {
    public function recordClick($animalId);
    public function getClicks($animalId);
}