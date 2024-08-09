<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
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

// Autoload necessary classes
require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Database\MongoDBConnection;
use Repositories\AnimalRepository;
use Repositories\ClickRepository;
use Services\AnimalService;
use Services\ClickService;
use Controllers\AnimalController;

// Initialize database connection
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$mongoDBConnection = new MongoDBConnection();
$clicksCollection = $mongoDBConnection->getCollection('click_counts');

$clickRepository = new ClickRepository($clicksCollection);
$clickService = new ClickService($clickRepository);

$animalRepository = new AnimalRepository($conn);
$animalService = new AnimalService($animalRepository, $clickRepository);
$animalController = new AnimalController($animalService, $clickService);

// Fetch all animals
$animals = $animalController->getAllAnimals();

include '../../../../src/views/templates/header.php';
include '../navbar_vet.php';
?>
<style>
h1 {
    text-align: center;
}
body {
    background-image: url('../../../../assets/image/background.jpg');
}
.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Gestion des animaux</h1>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Race</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal): ?>
                    <tr>
                        <td><?= htmlspecialchars($animal['id']) ?></td>
                        <td><?= htmlspecialchars($animal['name']) ?></td>
                        <td><?= htmlspecialchars($animal['species']) ?></td>
                        <td><img src="../../../../assets/uploads/<?= htmlspecialchars($animal['image']) ?>" alt="<?= htmlspecialchars($animal['name']) ?>" width="100"></td>
                        <td>
                            <div class="accordion" id="accordionExampleFood-<?= htmlspecialchars($animal['id']) ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFood-<?= htmlspecialchars($animal['id']) ?>">
                                        <button class="btn btn-outline-success" type="button" data-toggle="collapse" data-target="#collapseFood-<?= htmlspecialchars($animal['id']) ?>" aria-expanded="false" aria-controls="collapseFood">
                                            Voir les nourritures
                                        </button>
                                    </h2>
                                    <div id="collapseFood-<?= htmlspecialchars($animal['id']) ?>" class="collapse" aria-labelledby="headingFood-<?= htmlspecialchars($animal['id']) ?>" data-parent="#accordionExampleFood-<?= htmlspecialchars($animal['id']) ?>">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <?php
                                                $foods = $animalController->getAnimalFoodRecords($animal['id']);
                                                foreach ($foods as $food): ?>
                                                    <li class="list-group-item">
                                                        <strong>Nourriture:</strong> <?= htmlspecialchars($food['food_given']) ?><br>
                                                        <strong>Quantité:</strong> <?= htmlspecialchars($food['food_quantity']) ?>g<br>
                                                        <strong>Date:</strong> <?= htmlspecialchars($food['date_given']) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="../view/manage_animal_reports.php" class="btn btn-success mt-2">Ajouter un rapport</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
