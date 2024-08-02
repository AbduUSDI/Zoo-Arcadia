<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
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

$animals = new Animal($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $food_given = htmlspecialchars($_POST['food_given']);
    $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
    $date_given = $_POST['date_given'];

    // Validation et formatage de la date
    $dateTime = DateTime::createFromFormat('Y-m-d', $date_given);
    $formatted_date_given = $dateTime->format('Y-m-d');

    $animal = new Animal($conn);

    $animal->donnerNourriture($animal_id, $food_given, $food_quantity, $formatted_date_given);

    header('Location: manage_food.php');
    exit;
}
