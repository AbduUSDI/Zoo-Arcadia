<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base donnée

$db = new Database();
$conn = $db->connect();

// Vérification si l'id est présent dans l'URL

$id = $_GET['id'];

// Instance Habitat pour utiliser la méthode "deleteHabitat" afin de supprimer l'habitat sélectionné

$habitatObj = new Habitat($conn);

// Méthode préparée "deleteHabitat"

$habitatObj->deleteHabitat($id);

header('Location: manage_habitats.php');
exit;