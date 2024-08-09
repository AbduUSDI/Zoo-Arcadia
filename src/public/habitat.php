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
use Repositories\HabitatRepository;
use Repositories\ClickRepository;
use Services\HabitatService;
use Services\ClickService;
use Controllers\HabitatController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();
$mongoDB = new MongoDBConnection();
$clickCollection = $mongoDB->getCollection('clicks');

// Initialisation des repositories
$habitatRepository = new HabitatRepository($db);
$clickRepository = new ClickRepository($clickCollection);

// Initialisation des services
$habitatService = new HabitatService($habitatRepository);
$clickService = new ClickService($clickRepository);

// Initialisation des contrôleurs
$habitatController = new HabitatController($habitatService);

// Récupérer l'ID de l'habitat
$habitatId = $_GET['id'] ?? null;

if (!$habitatId) {
    header("Location: habitats.php");
    exit;
}

// Récupérer les données de l'habitat
$habitat = $habitatController->getHabitatById($habitatId);
$animals = $habitatController->getAnimalsByHabitat($habitatId);
$vetComments = $habitatController->getApprovedComments($habitatId);

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<style>
h1,h2,h3 {
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
    <h1 class="my-4"><?php echo htmlspecialchars($habitat['name']); ?></h1>
    <hr>
    <br>
    <img src="../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
    <p><?php echo htmlspecialchars($habitat['description']); ?></p>
    <br>
    <hr>
    <h2>Commentaires sur l'habitat</h2>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nom du vétérinaire</th>
                    <th>Date du commentaire</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vetComments as $comment): ?>
                    <tr>
                        <td scope="col" class="col-3"><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td scope="col" class="col-3"><?php echo htmlspecialchars($comment['created_at']); ?></td>
                        <td scope="col" class="col-6"><?php echo htmlspecialchars($comment['comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($vetComments)): ?>
                    <tr>
                        <td colspan="3">Aucun commentaire disponible pour cet habitat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
    <hr>
    <h2>Animaux</h2>
    <hr>
    <br>
    <div class="row">
        <?php foreach ($animals as $animal): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img class="card-img-top" src="../../assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                        <button onclick="registerClick(<?php echo $animal['id']; ?>)" class="btn btn-success">Plus de détails</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
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
