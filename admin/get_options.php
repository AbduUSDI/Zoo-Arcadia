<?php

// Vérification de l'identification de l'utilisateur, il doit être role 1 donc admin, sinon redirection vers la page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

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

// Nous combinons les résultat dans $options afin de le mettre dans un tableau

$options = [
    'dates' => $dates,
    'animals' => $animals
];

// Renvoie les résultats en format JSON

header('Content-Type: application/json');
echo json_encode($options);