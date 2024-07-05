<?php

// Vérification de l'identification de l'utiliateur, il doit être role 3 donc vétérinaire, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

$vetReport = new Animal($conn);

// Si l'id n'apparaît pas sur l'URL alors retour à la page de gestion des rapports animaux

if (!isset($_GET['id'])) {
    header('Location: manage_animal_reports.php');
    exit;
}

// Vérifie si l'id est dans l'URL et assure que c'est un entier valide et ensuite valide les entrées de l'utilisateur

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: manage_animal_reports.php');
    exit;
}

// Supprime le rapport avec l'id donné en utilisant la méthode préparée "deleteRapport"

if ($vetReport->deleteRapport($id)) {
    header('Location: manage_animal_reports.php');
    exit;
} else {
    echo 'Erreur lors de la suppression du rapport.';
}
