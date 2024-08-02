<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../public/login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {

    session_unset();  
    session_destroy(); 
    header('Location: ../../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../config/Database.php';
require_once '../../models/AnimalModel.php';

$db = new Database();
$conn = $db->connect();

$vetReport = new Animal($conn);

if (!isset($_GET['id'])) {
    header('Location: manage_animal_reports.php');
    exit;
}

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: manage_animal_reports.php');
    exit;
}

if ($vetReport->deleteRapport($id)) {
    header('Location: manage_animal_reports.php');
    exit;
} else {
    echo 'Erreur lors de la suppression du rapport.';
}
