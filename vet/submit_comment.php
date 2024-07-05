<?php
session_start();
require '../functions.php';

// Vérification du role_id pour voir si l'utilisateur est bien vétérinaire (3)

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

// Récupération des informations du formulaire POST dans le fichier habitats.php (ajout de commentaire habitat)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Connexion à la base de donnée si le POST est soumis

    $db = new Database();
    $conn = $db->connect();
    $habitat_id = $_POST['habitat_id'];
    $comment = $_POST['comment'];
    $vet_id = $_SESSION['user']['id'];

    // Instance Habitat utilisée pour utiliser la méthode de soumission de commentaire habitat

    $habitatManager = new Habitat($conn);
    $habitatManager->submitHabitatComment($habitat_id, $vet_id, $comment);

    // Une fois l'action terminée retour à la page habitats.php sinon login.php

    header('Location: habitats.php');
    exit;
} else {
    header('Location: ../login.php');
    exit;
}
