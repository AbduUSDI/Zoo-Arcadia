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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['serviceId'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    try {
        if ($image['error'] === UPLOAD_ERR_NO_FILE) {
            $serviceController->updateServiceWithoutImage($id, $name, $description);
        } else {
            $imageName = $serviceController->addServiceImage($image);
            $serviceController->updateServiceWithImage($id, $name, $description, $imageName);
        }
        echo "Service modifié avec succès !";
    } catch (Exception $erreur) {
        echo "Erreur: " . $erreur->getMessage();
    }
}
