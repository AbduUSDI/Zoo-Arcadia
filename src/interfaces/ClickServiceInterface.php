<?php

namespace Interfaces;

use MongoDB\Collection;

interface ClickServiceInterface {
    public function recordClick($animalId);
    public function getClicks($animalId);
}
