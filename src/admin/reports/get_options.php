<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
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

$db = new Database();
$conn = $db->connect();

// Méthode préparée pour obtenir les dates distinctes des rapports vétérinaires

$dateStmt = $conn->prepare("SELECT DISTINCT visit_date FROM vet_reports ORDER BY visit_date DESC");
$dateStmt->execute();
$dates = $dateStmt->fetchAll(PDO::FETCH_ASSOC);

// Méthode préparée pour obtenir la liste des animaux concernés par la requête

$animalStmt = $conn->prepare("SELECT id, name FROM animals ORDER BY name");
$animalStmt->execute();
$animals = $animalStmt->fetchAll(PDO::FETCH_ASSOC);

$options = [
    'dates' => $dates,
    'animals' => $animals
];

// Renvoie les résultats en format JSON

header('Content-Type: application/json');
echo json_encode($options);