<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: Zoo-Arcadia-New/login');
    exit;
}

// Vérification de l'expiration de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Génération d'un token CSRF unique
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../../../../vendor/autoload.php';

use Database\MongoDBConnection;
use Database\DatabaseConnection;
use Repositories\AnimalRepository;
use Services\AnimalService;
use Controllers\AnimalController;
use Services\ClickService;
use Repositories\ClickRepository;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();
$mongoCollection = (new MongoDBConnection())->getCollection('clicks');

// Initialisation des repositories
$animalRepository = new AnimalRepository($db);
$clickRepository = new ClickRepository($mongoCollection);

// Initialisation des services
$clickService = new ClickService($clickRepository);
$animalService = new AnimalService($animalRepository, $clickRepository);

// Initialisation des contrôleurs
$animalController = new AnimalController($animalService, $clickService);

// Récupérer tous les animaux
try {
    $animals = $animalController->getAllAnimals();
} catch (Exception $e) {
    die("Erreur lors de la récupération des animaux : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

include '../../../views/templates/header.php';
include '../navbar_employee.php';
?>

<div class="container animal-selection-container mt-5">
    <h1 class="text-center my-4">Animaux</h1>
    <div class="animal-list">
        <?php foreach ($animals as $animal): ?>
            <div class="animal-card">
                <div class="animal-image">
                    <img src="/Zoo-Arcadia-New/assets/uploads/<?= htmlspecialchars($animal['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="animal-info">
                    <h5><?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <p><strong>Espèce:</strong> <?= htmlspecialchars_decode($animal['species'], ENT_QUOTES) ?></p>
                    <p><strong>Habitat:</strong> <?= htmlspecialchars($animal['habitat_name'], ENT_QUOTES) ?></p>
                    <div class="accordion" id="accordionExample-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapseComments-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>" aria-expanded="false" aria-controls="collapseComments">
                                    Voir les commentaires
                                </button>
                            </h2>
                            <div id="collapseComments-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>" class="collapse" aria-labelledby="headingOne-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>" data-parent="#accordionExample-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        <?php
                                        try {
                                            $comments = $animalController->getAnimalReviews($animal['id']);
                                            foreach ($comments as $comment): ?>
                                                <li class="list-group-item">
                                                    <strong><?php echo htmlspecialchars($comment['visitor_name'], ENT_QUOTES, 'UTF-8'); ?>:</strong> <?php echo htmlspecialchars($comment['review_text'], ENT_QUOTES, 'UTF-8'); ?>
                                                </li>
                                            <?php endforeach;
                                        } catch (Exception $e) {
                                            echo '<li class="list-group-item text-danger">Erreur lors de la récupération des commentaires : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion" id="accordionExampleFood-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFood-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapseFood-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>" aria-expanded="false" aria-controls="collapseFood">
                                    Voir les nourritures
                                </button>
                            </h2>
                            <div id="collapseFood-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>" class="collapse" aria-labelledby="headingFood-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>" data-parent="#accordionExampleFood-<?php echo htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <div class="accordion-body">
                                    <ul class="list-group">
                                        <?php
                                        try {
                                            $foods = $animalController->getAnimalFoodRecords($animal['id']);
                                            foreach ($foods as $food): ?>
                                                <li class="list-group-item">
                                                    <strong>Nourriture:</strong> <?php echo htmlspecialchars($food['food_given'], ENT_QUOTES, 'UTF-8'); ?><br>
                                                    <strong>Quantité:</strong> <?php echo htmlspecialchars($food['food_quantity'], ENT_QUOTES, 'UTF-8'); ?>g<br>
                                                    <strong>Date:</strong> <?php echo htmlspecialchars($food['date_given'], ENT_QUOTES, 'UTF-8'); ?>
                                                </li>
                                            <?php endforeach;
                                        } catch (Exception $e) {
                                            echo '<li class="list-group-item text-danger">Erreur lors de la récupération des enregistrements de nourriture : ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../../views/templates/footerconnected.php'; ?>
