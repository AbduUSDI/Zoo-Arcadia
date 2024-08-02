<?php
require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = new Database();
$conn = $db->connect();
$services = new Service($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    try {
        $imageName = $services->ajouterImage($image);
        $services->ajouterService($name, $description, $imageName);
        echo "Service ajouté avec succès !";
    } catch (Exception $erreur) {
        echo "Erreur: " . $erreur->getMessage();
    }
}