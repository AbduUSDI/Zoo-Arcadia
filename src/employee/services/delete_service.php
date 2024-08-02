<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = (new Database())->connect();

$service = new Service($db);

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_services.php');
    exit();
}

$id = $_GET['id'];

try {
    $service->deleteService($id);
    $_SESSION['message'] = "Service supprimé avec succès.";
    header('Location: manage_services.php');
    exit();
    
} catch (Exception $erreur) {
    $_SESSION['error'] = "Erreur lors de la suppression du service : " . $erreur->getMessage();
    header('Location: manage_services.php');
    exit();
}