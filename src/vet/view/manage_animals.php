<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/AnimalModel.php';

$db = new Database();
$conn = $db->connect();

$animalManager = new Animal($conn);

include '../../../src/views/templates/header.php';
include '../navbar_vet.php';
?>
<style>
h1 {
    text-align: center;
}
body {
    background-image: url('../../../assets/image/background.jpg');
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
                <?php
                $animals = $animalManager->getAll();

                // Essai d'une nouvelle façon que j'ai apprise, en créant des variables pour afficher les lignes ce qui rend le code plus lisible
                foreach ($animals as $animal) {
                    $animalId = htmlspecialchars($animal['id']);
                    $animalName = htmlspecialchars($animal['name']);
                    $animalSpecies = htmlspecialchars($animal['species']);
                    $animalImage = htmlspecialchars($animal['image']);
                ?>
                    <tr>
                        <td><?= $animalId ?></td>
                        <td><?= $animalName ?></td>
                        <td><?= $animalSpecies ?></td>
                        <td><img src="../../../assets/uploads/<?= $animalImage ?>" alt="<?= $animalName ?>" width="100"></td>
                        <td>
                            <div class="accordion" id="accordionExampleFood-<?= $animalId ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingFood-<?= $animalId ?>">
                                        <button class="btn btn-outline-success" type="button" data-toggle="collapse" data-target="#collapseFood-<?= $animalId ?>" aria-expanded="false" aria-controls="collapseFood">
                                            Voir les nourritures
                                        </button>
                                    </h2>
                                    <div id="collapseFood-<?= $animalId ?>" class="collapse" aria-labelledby="headingFood-<?= $animalId ?>" data-parent="#accordionExampleFood-<?= $animalId ?>">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <?php
                                                $foods = $animalManager->getNourritureAnimaux($animalId);
                                                foreach ($foods as $food) {
                                                    $foodGiven = htmlspecialchars($food['food_given']);
                                                    $foodQuantity = htmlspecialchars($food['food_quantity']);
                                                    $dateGiven = htmlspecialchars($food['date_given']);
                                                ?>
                                                    <li class="list-group-item">
                                                        <strong>Nourriture:</strong> <?= $foodGiven ?><br>
                                                        <strong>Quantité:</strong> <?= $foodQuantity ?>g<br>
                                                        <strong>Date:</strong> <?= $dateGiven ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a href="../reports/add_animal_report.php?id=<?= $animalId ?>" class="btn btn-success mt-2">Ajouter un rapport</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
