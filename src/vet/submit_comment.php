<?php
session_start();
require '../functions.php';


if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../public/login.php');
    exit;
}

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
