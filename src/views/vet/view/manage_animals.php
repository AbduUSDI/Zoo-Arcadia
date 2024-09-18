<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../public/login.php');
    exit;
}

// Vérification de l'expiration de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Génération du token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Autoload des classes nécessaires
require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Database\MongoDBConnection;
use Repositories\AnimalRepository;
use Repositories\ClickRepository;
use Services\AnimalService;
use Services\ClickService;
use Controllers\AnimalController;

try {
    // Initialisation de la connexion à la base de données
    $dbConnection = new DatabaseConnection();
    $conn = $dbConnection->connect();

    $mongoDBConnection = new MongoDBConnection();
    $clicksCollection = $mongoDBConnection->getCollection('click_counts');

    // Initialisation des repositories, services et contrôleurs
    $clickRepository = new ClickRepository($clicksCollection);
    $clickService = new ClickService($clickRepository);

    $animalRepository = new AnimalRepository($conn);
    $animalService = new AnimalService($animalRepository, $clickRepository);
    $animalController = new AnimalController($animalService, $clickService);

    // Récupération des animaux
    $animals = $animalController->getAllAnimals();
} catch (Exception $e) {
    die("Erreur lors de l'initialisation : " . $e->getMessage());
}

include '../../../../src/views/templates/header.php';
include '../navbar_vet.php';
?>

<div class="container animal-selection-container mt-5">
    <h1 class="text-center my-4">Gestion des animaux</h1>
    <div class="animal-list">
        <?php foreach ($animals as $animal): ?>
            <div class="animal-card">
                <div class="animal-image">
                    <img src="../../../../assets/uploads/<?= htmlspecialchars($animal['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="animal-info">
                    <h5><?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <p><strong>Race:</strong> <?= htmlspecialchars_decode($animal['species']) ?></p>
                    <div class="accordion" id="accordionExampleFood-<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFood-<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button class="btn btn-outline-success" type="button" data-toggle="collapse" data-target="#collapseFood-<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>" aria-expanded="false" aria-controls="collapseFood">
                                    Voir les nourritures
                                </button>
                            </h2>
                            <div id="collapseFood-<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>" class="collapse" aria-labelledby="headingFood-<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>" data-parent="#accordionExampleFood-<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        <?php
                                        $foods = $animalController->getAnimalFoodRecords($animal['id']);
                                        foreach ($foods as $food): ?>
                                            <li class="list-group-item">
                                                <strong>Nourriture:</strong> <?= htmlspecialchars($food['food_given'], ENT_QUOTES, 'UTF-8') ?><br>
                                                <strong>Quantité:</strong> <?= htmlspecialchars($food['food_quantity'], ENT_QUOTES, 'UTF-8') ?>g<br>
                                                <strong>Date:</strong> <?= htmlspecialchars($food['date_given'], ENT_QUOTES, 'UTF-8') ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="../view/manage_animal_reports.php?action=add" class="btn btn-success mt-2">Ajouter un rapport</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
