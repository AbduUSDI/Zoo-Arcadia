<?php

require '../../config/MongoDB.php';

// Récupération de l'id d'animal par l'URL grâce à GET

$animal_id = isset($_GET['animal_id']) ? (int)$_GET['animal_id'] : 0;

if ($animal_id) {
    try {
        $mongoClient = new MongoDB();
        $mongoClient->recordClick($animal_id);
        echo "Clic enregistré !";
    } catch (Exception $erreur) {
        echo "Erreur lors de l'enregistrement du clic : " . $erreur->getMessage();
    }

} else {
    echo "Aucun animal spécifié.";
}