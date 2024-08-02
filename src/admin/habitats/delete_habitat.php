<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/HabitatModel.php';

$db = new Database();
$conn = $db->connect();

$id = $_GET['id'];

$habitatObj = new Habitat($conn);

$habitatObj->deleteHabitat($id);

header('Location: manage_habitats.php');
exit;