<?php
require '../../vendor/autoload.php';

use Database\MongoDBConnection;
use Repositories\ClickRepository;
use Services\ClickService;

// Récupérer l'ID de l'animal
$animalId = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : 0;

if (!$animalId) {
    echo "Animal ID manquant.";
    exit;
}

// Connexion à la base de données MongoDB
$mongoCollection = (new MongoDBConnection())->getCollection('clicks');

// Initialisation du dépôt et du service
$clickRepository = new ClickRepository($mongoCollection);
$clickService = new ClickService($clickRepository);

// Enregistrer le clic
$clickService->recordClick($animalId);

echo "Clic enregistré.";
