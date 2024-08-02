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

require_once '../models/HabitatModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $db = new Database();
    $conn = $db->connect();
    $habitat_id = $_POST['habitat_id'];
    $comment = $_POST['comment'];
    $vet_id = $_SESSION['user']['id'];

    $habitatManager = new Habitat($conn);
    $habitatManager->submitHabitatComment($habitat_id, $vet_id, $comment);

    header('Location: view/habitats.php');
    exit;
} else {
    header('Location: ../../login.php');
    exit;
}
