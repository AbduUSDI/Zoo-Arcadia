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
$animalService = new AnimalService($animalRepository);
$clickService = new ClickService($clickRepository);
// Initialisation des contrôleurs
$animalController = new AnimalController($animalService, $clickService);

// Récupérer tous les animaux
$animals = $animalController->getAllAnimals();

include '../../../views/templates/header.php';
include '../navbar_employee.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../../assets/image/background.jpg');
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
.accordion {
    max-height: 200px;
    overflow-y: auto;
}
</style>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
<br>
    <hr>
    <h1 class="my-4">Animaux</h1>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Espèce</th>
                    <th>Habitat</th>
                    <th>Image</th>
                    <th>Commentaires</th>
                    <th>Nourriture Donnée</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal): ?>
                <tr> 
                    <td><?php echo htmlspecialchars($animal['id']); ?></td>
                    <td><?php echo htmlspecialchars($animal['name']); ?></td>
                    <td><?php echo htmlspecialchars($animal['species']); ?></td>
                    <td><?php echo htmlspecialchars($animal['habitat_name']); ?></td>
                    <td><img src="../../../../assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" style="width: 100px;"></td>
                    <td>
                        <div class="accordion" id="accordionExample-<?php echo $animal['id']; ?>">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne-<?php echo $animal['id']; ?>">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapseComments-<?php echo $animal['id']; ?>" aria-expanded="false" aria-controls="collapseComments">
                                        Voir les commentaires
                                    </button>
                                </h2>
                                <div id="collapseComments-<?php echo $animal['id']; ?>" class="collapse" aria-labelledby="headingOne-<?php echo $animal['id']; ?>" data-parent="#accordionExample-<?php echo $animal['id']; ?>">
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
                    </td>
                    <td>
                        <div class="accordion" id="accordionExampleFood-<?php echo $animal['id']; ?>">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFood-<?php echo $animal['id']; ?>">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapseFood-<?php echo $animal['id']; ?>" aria-expanded="false" aria-controls="collapseFood">
                                        Voir les nourritures
                                    </button>
                                </h2>
                                <div id="collapseFood-<?php echo $animal['id']; ?>" class="collapse" aria-labelledby="headingFood-<?php echo $animal['id']; ?>" data-parent="#accordionExampleFood-<?php echo $animal['id']; ?>">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <?php
                                            $foods = $animalController->getAnimalFoodRecords($animal['id']);
                                            foreach ($foods as $food): ?>
                                                <li class="list-group-item">
                                                    <strong>Nourriture:</strong> <?php echo htmlspecialchars($food['food_given']); ?><br>
                                                    <strong>Quantité:</strong> <?php echo htmlspecialchars($food['food_quantity']); ?>g<br>
                                                    <strong>Date:</strong> <?php echo htmlspecialchars($food['date_given']); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../views/templates/footerconnected.php'; ?>
