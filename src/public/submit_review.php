<?php
session_start();

require '../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ReviewRepository;
use Services\ReviewService;
use Controllers\ReviewController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories
$reviewRepository = new ReviewRepository($db);

// Initialisation des services
$reviewService = new ReviewService($reviewRepository);

// Initialisation des contrôleurs
$reviewController = new ReviewController($reviewService);

// Récupérer les données du formulaire
$pseudo = filter_input(INPUT_POST, 'pseudo', FILTER_SANITIZE_STRING);
$subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
$reviewText = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING);

if ($pseudo && $subject && $reviewText) {
    $reviewController->addReview($pseudo, $subject, $reviewText);
    $_SESSION['message'] = "Votre avis a été envoyé avec succès.";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Tous les champs sont obligatoires.";
    $_SESSION['message_type'] = "danger";
}

header('Location: index.php');
exit;
