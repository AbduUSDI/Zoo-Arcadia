<?php

namespace Interfaces;

interface ClickServiceInterface {
    public function recordClick($animalId);
    public function getClicks($animalId);
}
