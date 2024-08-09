<?php
require '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ServiceRepository;
use Services\ServiceService;
use Controllers\ServiceController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories
$serviceRepository = new ServiceRepository($db);

// Initialisation des services
$serviceService = new ServiceService($serviceRepository);

// Initialisation des contrôleurs
$serviceController = new ServiceController($serviceService);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $serviceController->deleteService($id);
        echo "Service supprimé avec succès !";
    } catch (Exception $erreur) {
        echo "Erreur: " . $erreur->getMessage();
    }
}
