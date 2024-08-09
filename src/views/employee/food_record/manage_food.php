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

include '../../../../src/views/templates/header.php';
include '../navbar_employee.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../../../assets/image/background.jpg');
}

.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Gérer la Nourriture des Animaux</h1>
    <hr>
    <br>
    <form action="add_food_record.php" method="POST">
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
