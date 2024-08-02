<?php
require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = new Database();
$conn = $db->connect();
$services = new Service($conn);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['serviceId'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    try {
        if ($image['error'] === UPLOAD_ERR_NO_FILE) {
            $services->updateServiceSansImage($id, $name, $description);
        } else {
            $imageName = $services->ajouterImage($image);
            $services->updateServiceAvecImage($id, $name, $description, $imageName);
        }
        echo "Service modifié avec succès !";
    } catch (Exception $erreur) {
        echo "Erreur: " . $erreur->getMessage();
    }
}