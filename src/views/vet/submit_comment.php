<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../public/login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: ../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbConnection = new DatabaseConnection();
    $conn = $dbConnection->connect();
    
    $habitatRepository = new HabitatRepository($conn);
    $habitatService = new HabitatService($habitatRepository);
    $habitatController = new HabitatController($habitatService);

    $habitatId = $_POST['habitat_id'];
    $comment = $_POST['comment'];
    $vetId = $_SESSION['user']['id'];

    $habitatController->submitHabitatComment($habitatId, $vetId, $comment);

    header('Location: view/habitats.php');
    exit;
} else {
    header('Location: ../public/login.php');
    exit;
}