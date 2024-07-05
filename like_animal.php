<?php
require 'functions.php';

// Connexion à la base de données
$db = (new Database())->connect();
$animal = new Animal($db);

// Récupération des like au clic sur le bouton like (POST)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'];
    $animal->ajouterLike($animal_id);
    header('Location: animals.php');
    exit;
}