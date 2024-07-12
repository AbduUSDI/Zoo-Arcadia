<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

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
