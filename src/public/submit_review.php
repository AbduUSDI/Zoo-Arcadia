<?php
require_once '../../config/Database.php';
require_once '../models/ReviewModel.php';

// Connexion à la base de données
$db = (new Database())->connect();

$pseudo = $_POST['pseudo'];
$subject = $_POST['subject'];
$review_text = $_POST['review_text'];

$review = new Review($db);  // Assuming your model class is ReviewModel
$review->addAvis($pseudo, $subject, $review_text);

header('Location: index.php');
exit;
