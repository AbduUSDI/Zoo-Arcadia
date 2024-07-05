<?php

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employee, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = (new Database())->connect();

// Instance Service pour utiliser les méthodes en rapport avec les services

$service = new Service($db);

// Vérifier si l'ID du service à supprimer est présent dans la requête

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_services.php');
    exit();
}

// Vérifie si l'id est présent dan l'URL

$id = $_GET['id'];

try {

    // Supprimer le service de la base de données en utilisant la méthode préparée "deleteService"

    $service->deleteService($id);

    // Redirirection vers la page des services avec un message de succès

    $_SESSION['message'] = "Service supprimé avec succès.";
    header('Location: manage_services.php');
    exit();
} catch (Exception $erreur) {

    // Redirirection vers la page des services avec un message d'erreur

    $_SESSION['error'] = "Erreur lors de la suppression du service : " . $erreur->getMessage();
    header('Location: manage_services.php');
    exit();
}