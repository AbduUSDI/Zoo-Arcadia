<?php

namespace Repositories;

use Interfaces\ClickRepositoryInterface;
use MongoDB\Collection;

class ClickRepository implements ClickRepositoryInterface {
    private $collection;

    public function __construct(Collection $collection) {
        $this->collection = $collection;
    }

    public function recordClick($animalId) {
        $this->collection->updateOne(
            ['animal_id' => $animalId],
            ['$inc' => ['clicks' => 1]],
            ['upsert' => true]
        );
    }

    public function getClicks($animalId) {
        $click = $this->collection->findOne(['animal_id' => $animalId]);
        return $click ? $click['clicks'] : 0;
    }
    public function find(array $filter = [], array $options = []) {
        return $this->collection->find($filter, $options);
    }
    
}
