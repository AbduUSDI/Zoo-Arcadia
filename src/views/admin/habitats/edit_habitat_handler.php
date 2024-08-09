<?php
require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$habitatRepository = new HabitatRepository($conn);
$habitatService = new HabitatService($habitatRepository);
$habitatController = new HabitatController($habitatService);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['habitatId'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'] ?? null;
    $habitatController->updateHabitat($id, $name, $description, $image);
    echo "Habitat modifié avec succès.";
}