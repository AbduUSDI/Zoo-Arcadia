<?php
require 'functions.php';

// Connexion à la base de données
$db = (new Database())->connect();

$pseudo = $_POST['pseudo'];
$subject = $_POST['subject'];
$review_text = $_POST['review_text'];

$review = new Review($db);
$review->addAvis($pseudo, $subject, $review_text);

header('Location: index.php');
exit;
