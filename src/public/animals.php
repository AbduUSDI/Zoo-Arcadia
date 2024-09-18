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

// Générer un token CSRF unique s'il n'existe pas
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
$clickCollection = $mongoConnection->getCollection(collectionName: 'clicks');

// Initialisation des repositories
$animalRepository = new AnimalRepository($db);
$habitatRepository = new HabitatRepository($db);
$clickRepository = new ClickRepository(collection: $clickCollection);

// Initialisation des services
$animalService = new AnimalService(animalRepository: $animalRepository, clickRepository: $clickRepository);
$habitatService = new HabitatService(habitatRepository: $habitatRepository);
$clickService = new ClickService(clickRepository: $clickRepository);

// Initialisation des contrôleurs
$animalController = new AnimalController(animalService: $animalService, clickService: $clickService);
$habitatController = new HabitatController(habitatService: $habitatService);

// Récupérer tous les animaux
$animals = $animalController->getAllAnimals();

// Gestion des likes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    $animalId = filter_input(type: INPUT_POST, var_name: 'animal_id', filter: FILTER_VALIDATE_INT);
    if ($animalId) {
        $animalController->addLike(animal_id: $animalId);
    } else {
        $error = "ID d'animal invalide.";
    }
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mb-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Découvrez Nos Animaux</h1>
    <hr>
    <br>
    <div class="row">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger col-12"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if (count($animals) > 0): ?>
            <?php foreach ($animals as $animal): ?>
                <?php
                // Récupérer le nom de l'habitat pour chaque animal
                $habitat = $habitatController->getHabitatById(id: $animal['habitat_id']);
                $habitatName = $habitat ? htmlspecialchars($habitat['name']) : 'Habitat indisponible'; ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($animal['name']); ?>"
                             onclick="registerClick(<?php echo $animal['id']; ?>)">
                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                            <p class="card-text">Espèce : <?php echo htmlspecialchars_decode($animal['species']); ?></p>
                            <p class="card-text">Habitat : <?php echo $habitatName; ?></p>
                            <p class="card-text">Likes : <?php echo $animal['likes']; ?></p>
                            <form action="animals.php" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="animal_id" value="<?php echo $animal['id']; ?>">
                                <button type="submit" name="like" class="btn btn-success btn-block">👍 Like</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="col-12 text-center">Aucun animal trouvé.</p>
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
            window.location.href = 'index.php?page=animal&id=' + animalId;
        })
        .catch(error => {
            console.error("Erreur lors de l'enregistrement du clic:", error);
        });
}
</script>

<?php include '../../src/views/templates/footer.php'; ?>
