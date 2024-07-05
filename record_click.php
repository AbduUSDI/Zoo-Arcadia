<?php

require 'MongoDB.php';

// Récupération de l'id d'animal par l'URL grâce à la méthode GET 

$animal_id = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : 0;

// Utilisation d'un if/else ici pour savoir si il y a une erreur, si le serveur est bien connecté à la BDD MongoDB alors il utilise la méthode "recordClick" avec pour variable l'id de l'animal qui est dans l'URL, ensuite il affiche "Clic enregistré" dans la console et si le clic n'est pas récupéré un message d'erreur s'affichera

if ($animal_id) {
    try {  // try pour une nouvelle instance MongoDB qui executera la méthode "recordClick", catch pour la gestion de l'erreur
        $mongoClient = new MongoDB();
        $mongoClient->recordClick($animal_id);
        echo "Clic enregistré !";
    } catch (Exception $erreur) {
        echo "Erreur lors de l'enregistrement du clic : " . $erreur->getMessage();
    }

    // Si l'id de l'animal n'est pas apparu dans l'URL alors message d'erreur ci-dessous

} else {
    echo "Aucun animal spécifié.";
}