<?php
require 'functions.php';

// Connexion à la base de données
$db = (new Database())->connect();

// Récupération des données entrée dans le formulaire
$pseudo = $_POST['pseudo'];
$subject = $_POST['subject'];
$review_text = $_POST['review_text'];

// Création d'une instance $review pour récupérer les avis envoyés
$review = new Review($db);
$review->addAvis($pseudo, $subject, $review_text);

// Redirection vers la page d'accueil
header('Location: index.php');
exit;
