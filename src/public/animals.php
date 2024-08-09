<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

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

// Récupérer tous les animaux
$animals = $animalController->getAllAnimals();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $animalId = $_POST['animal_id'];
    $animalController->addLike($animalId);
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 68px;
}

.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mb-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Tous les Animaux</h1>
    <hr>
    <br>
    <div class="row">
        <?php if (count($animals) > 0): ?>
            <?php foreach ($animals as $animal): ?>
                <?php
                // Récupérer le nom de l'habitat pour chaque animal
                $habitat = $habitatController->getHabitatById($animal['habitat_id']);
                $habitatName = $habitat ? htmlspecialchars($habitat['name']) : 'Habitat indisponible'; ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($animal['name']); ?>"
                             onclick="registerClick(<?php echo $animal['id']; ?>)"
                             style="cursor: pointer;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                            <p class="card-text">Race: <?php echo htmlspecialchars($animal['species']); ?></p>
                            <p class="card-text">Habitat: <?php echo $habitatName; ?></p>
                            <p class="card-text">Likes: <?php echo $animal['likes']; ?></p>
                            <form action="animals.php" method="POST">
                                <input type="hidden" name="animal_id" value="<?php echo $animal['id']; ?>">
                                <button type="submit" name="like" class="btn btn-success">👍 Like</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun animal trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<script>
// Utilisation de FETCH pour enregistrer le clic dans MongoDB grâce au fichier "record_click.php"
function registerClick(animalId) {
    console.log("Tentative d'enregistrement du clic pour l'animal ID:", animalId);
    fetch('record_click.php?animal_id=' + animalId)
        .then(response => response.text())
        .then(data => {
            console.log("Données reçues:", data);
            window.location.href = 'animal.php?id=' + animalId;
        })
        .catch(error => {
            console.error("Erreur lors de l'enregistrement du clic:", error);
        });
}
</script>

<?php include '../../src/views/templates/footer.php'; ?>
