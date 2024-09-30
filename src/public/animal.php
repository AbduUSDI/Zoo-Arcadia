<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Génération d'un token CSRF unique s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../../vendor/autoload.php';

use Database\DatabaseConnection;
use Database\MongoDBConnection;
use Repositories\AnimalRepository;
use Repositories\HabitatRepository;
use Repositories\ClickRepository;
use Services\AnimalService;
use Services\HabitatService;
use Services\ClickService;
use Controllers\AnimalController;
use Controllers\HabitatController;

// Connexion à la base de données
$databaseConnection = new DatabaseConnection();
$db = $databaseConnection->connect();

// Connexion à la base de données MongoDB
$mongoConnection = new MongoDBConnection();
$clickCollection = $mongoConnection->getCollection('clicks');

// Initialisation des repositories
$animalRepository = new AnimalRepository($db);
$habitatRepository = new HabitatRepository($db);
$clickRepository = new ClickRepository($clickCollection);

// Initialisation des services
$animalService = new AnimalService($animalRepository, $clickRepository);
$habitatService = new HabitatService($habitatRepository);
$clickService = new ClickService($clickRepository);

// Initialisation des contrôleurs
$animalController = new AnimalController($animalService, $clickService);
$habitatController = new HabitatController($habitatService);

$animal_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$animal_id) {
    header("Location: /Zoo-Arcadia-New/animals");
    exit;
}

$animal = $animalController->getAnimalDetails($animal_id);
$reports = $animalController->getReportsByAnimalId($animal_id);

if (!$animal) {
    header("Location: /Zoo-Arcadia-New/animals");
    exit;
}

$habitat = $habitatController->getHabitatById($animal['habitat_id']);

if (!$habitat) {
    $habitat['name'] = 'Habitat indisponible';
}

$avis_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    if (isset($_POST['like'])) {
        $animalController->addLike($animal_id);
        $animal['likes']++;
    } elseif (isset($_POST['comment'])) {
        $visitorName = filter_input(INPUT_POST, 'visitor_name', FILTER_SANITIZE_STRING);
        $reviewText = filter_input(INPUT_POST, 'review_text', FILTER_SANITIZE_STRING);

        if ($visitorName && $reviewText) {
            $animalController->addReview($visitorName, $reviewText, $animal_id);
            $avis_success = true;
        } else {
            $error = "Veuillez remplir tous les champs.";
        }
    }
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4"><?php echo htmlspecialchars($animal['name']); ?></h1>
    <hr>
    <br>
    <img src="/Zoo-Arcadia-New/assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($animal['name']); ?>">
    <p>Race: <?php echo htmlspecialchars_decode($animal['species']); ?></p>
    <p>Habitat: <?php echo htmlspecialchars($habitat['name']); ?></p>
    <p>Likes: <?php echo $animal['likes']; ?></p>
    <form action="/Zoo-Arcadia-New/animal/<?php echo $animal_id; ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <button type="submit" name="like" class="btn btn-success">👍 Like</button>
    </form>
    <hr>
    <div class="my-4">
        <h2>Ajouter un avis</h2>
        <form action="/Zoo-Arcadia-New/animal/<?php echo $animal_id; ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="mb-3">
                <label for="visitor_name" class="form-label">Nom</label>
                <input type="text" class="form-control" name="visitor_name" required>
            </div>
            <div class="mb-3">
                <label for="review_text" class="form-label">Commentaire</label>
                <textarea class="form-control" name="review_text" rows="3" required></textarea>
            </div>
            <button type="submit" name="comment" class="btn btn-success">Poster l'avis</button>
        </form>
        <?php if ($avis_success): ?>
            <div class="alert alert-success mt-3" role="alert">
                Votre avis a bien été envoyé, il sera soumis à la validation par nos employés !
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger mt-3" role="alert">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
    </div>
    <hr>
    <div class="accordion" id="accordionExample-<?php echo $animal['id']; ?>">
        <div class="accordion-item">
            <h2 class="accordion-header" id="headingOne-<?php echo $animal['id']; ?>">
                <button class="btn btn-outline-success" type="button" data-toggle="collapse" data-target="#collapseExample-<?php echo $animal['id']; ?>" aria-expanded="false" aria-controls="collapseExample">
                    Voir les commentaires
                </button>
            </h2>
            <div id="collapseExample-<?php echo $animal['id']; ?>" class="collapse" aria-labelledby="headingOne-<?php echo $animal['id']; ?>" data-parent="#accordionExample-<?php echo $animal['id']; ?>">
                <div class="accordion-body">
                    <ul class="list-group">
                        <?php
                        $comments = $animalController->getAnimalReviews($animal['id']);
                        foreach ($comments as $comment): ?>
                            <li class="list-group-item">
                                <strong><?php echo htmlspecialchars($comment['visitor_name']); ?>:</strong> <?php echo htmlspecialchars($comment['review_text']); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <h2>Rapports du Vétérinaire</h2>
    <div class="row">
        <?php foreach ($reports as $report): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Date de Visite: <?php echo htmlspecialchars($report['visit_date']); ?></h5>
                        <p class="card-text"><strong>État de Santé:</strong> <?php echo htmlspecialchars_decode($report['health_status']); ?></p>
                        <p class="card-text"><strong>Nourriture Donnée:</strong> <?php echo htmlspecialchars($report['food_given']); ?></p>
                        <p class="card-text"><strong>Quantité de Nourriture:</strong> <?php echo htmlspecialchars($report['food_quantity']); ?> grammes</p>
                        <p class="card-text"><strong>Détails:</strong> <?php echo htmlspecialchars_decode($report['details']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../src/views/templates/footer.php'; ?>
