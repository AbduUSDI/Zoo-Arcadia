<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de donnée

$db = new Database();
$conn = $db->connect();

// Instance Animal pour utiliser la méthode "delete" afin de supprimer un animal 

$animalManager = new Animal($conn);

// Si l'id n'est pas nulle alors il récupère l'id de l'URL est utilise la méthode "delete"

if (isset($_GET['id'])) {
    $animalId = $_GET['id'];
    // Utilisation la méthode préparée "delete" de la classe Animal pour supprimer l'animal
    $animalManager->delete($animalId);
}

header('Location: manage_animals.php');
exit;