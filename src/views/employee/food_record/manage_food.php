<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../../public/login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: ../../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Générer un token CSRF unique
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\AnimalRepository;
use Services\FoodService;
use Controllers\FoodController;

$db = (new DatabaseConnection())->connect();
$animalRepository = new AnimalRepository($db);
$foodService = new FoodService($animalRepository);
$foodController = new FoodController($foodService);

$animals = $foodController->getAllAnimals();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Validation et nettoyage des entrées
    $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $food_given = htmlspecialchars($_POST['food_given'], ENT_QUOTES, 'UTF-8');
    $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
    $date_given = $_POST['date_given'];

    if ($animal_id && $food_given && $food_quantity && $date_given) {
        try {
            // Validation et formatage de la date
            $dateTime = DateTime::createFromFormat('Y-m-d', $date_given);
            if (!$dateTime) {
                throw new Exception("Format de date invalide.");
            }
            $formatted_date_given = $dateTime->format('Y-m-d');

            // Enregistrement de la nourriture donnée
            $foodController->addFoodRecord($animal_id, $food_given, $food_quantity, $formatted_date_given);

            // Redirection après succès
            header('Location: manage_food.php');
            exit;
        } catch (Exception $e) {
            $error = "Erreur lors de l'enregistrement : " . $e->getMessage();
        }
    } else {
        $error = "Données invalides. Veuillez vérifier vos entrées.";
    }
}

include '../../../../src/views/templates/header.php';
include '../navbar_employee.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Gérer la Nourriture des Animaux</h1>
    <hr>
    <br>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>
    <form action="" method="POST">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
        <div class="form-group">
            <label for="animal_id">Animal</label>
            <select class="form-control" id="animal_id" name="animal_id" required>
                <?php foreach ($animals as $animal): ?>
                    <option value="<?php echo $animal['id']; ?>"><?php echo htmlspecialchars($animal['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="food_given">Nourriture</label>
            <input type="text" class="form-control" id="food_given" name="food_given" required>
        </div>
        <div class="form-group">
            <label for="food_quantity">Grammage</label>
            <input type="number" class="form-control" id="food_quantity" name="food_quantity" required>
        </div>
        <div class="form-group">
            <label for="date_given">Date</label>
            <input type="date" class="form-control" id="date_given" name="date_given" required>
        </div>
        <button type="submit" class="btn btn-success">Nourrir</button>
    </form>
</div>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
