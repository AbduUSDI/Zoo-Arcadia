<?php 
session_start();

// Protection CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ReviewRepository;
use Services\ReviewService;
use Controllers\ReviewController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories, services, et contrôleurs
$reviewRepository = new ReviewRepository($db);
$reviewService = new ReviewService($reviewRepository);
$reviewController = new ReviewController($reviewService);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Validation des entrées sans échapper les caractères spéciaux
    $pseudo = trim($_POST['pseudo']);
    $subject = trim($_POST['subject']);
    $reviewText = trim($_POST['review_text']);

    if ($pseudo && $subject && $reviewText) {
        // Insertion des données sans échapper ici, car htmlspecialchars doit être utilisé lors de l'affichage
        $reviewController->addReview($pseudo, $subject, $reviewText);
        $_SESSION['message'] = "Votre avis a été envoyé avec succès.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
        $_SESSION['message_type'] = "danger";
    }

    // Redirection vers l'URL réécrite pour la page d'accueil
    header('Location: /Zoo-Arcadia-New/home');
    exit;
}

// Affichage des messages d'erreur
if (isset($_SESSION['message'])) {
    echo '<div class="alert alert-' . htmlspecialchars($_SESSION['message_type']) . ' alert-dismissible fade show" role="alert">
            ' . htmlspecialchars($_SESSION['message']) . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
