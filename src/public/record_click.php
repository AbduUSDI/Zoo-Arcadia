<?php
require '../../vendor/autoload.php';

use Database\MongoDBConnection;
use Repositories\ClickRepository;
use Services\ClickService;

// Récupérer l'ID de l'animal de manière sécurisée
$animalId = filter_input(INPUT_GET, 'animal_id', FILTER_VALIDATE_INT);

if (!$animalId) {
    echo "Animal ID manquant ou invalide.";
    exit;
}

try {
    // Connexion à la base de données MongoDB
    $mongoCollection = (new MongoDBConnection())->getCollection('clicks');

    // Initialisation du dépôt et du service
    $clickRepository = new ClickRepository($mongoCollection);
    $clickService = new ClickService($clickRepository);

    // Enregistrer le clic
    $clickService->recordClick($animalId);

    echo "Clic enregistré avec succès.";
} catch (Exception $e) {
    // Gestion des erreurs en cas de problème avec la base de données ou autre
    echo "Erreur lors de l'enregistrement du clic : " . $e->getMessage();
}
