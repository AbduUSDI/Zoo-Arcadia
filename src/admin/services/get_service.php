<?php
require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = new Database();
$conn = $db->connect();
$services = new Service($conn);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $service = $services->getServiceById($id);
    echo json_encode($service);
}