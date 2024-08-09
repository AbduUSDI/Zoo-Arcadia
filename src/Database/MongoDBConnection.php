<?php
namespace Database;

use MongoDB\Client;

class MongoDBConnection {
    private $client;
    private $database;

    public function __construct() {
        $uri = 'mongodb://localhost:27017';
        $databaseName = 'zoo_arcadia_click_counts';

        $this->client = new Client($uri);
        $this->database = $this->client->selectDatabase($databaseName);
    }

    public function getCollection($collectionName) {
        return $this->database->selectCollection($collectionName);
    }
}
