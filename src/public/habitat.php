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
$habitatId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$habitatId) {
    header("Location: /Zoo-Arcadia-New/habitats");
    exit;
}

try {
    // Récupérer les données de l'habitat
    $habitat = $habitatController->getHabitatById($habitatId);
    if (!$habitat) {
        throw new Exception("Habitat introuvable.");
    }

    $animals = $habitatController->getAnimalsByHabitat($habitatId);
    $vetComments = $habitatController->getApprovedComments($habitatId);
} catch (Exception $e) {
    die("Erreur : " . htmlspecialchars($e->getMessage()));
}

$scriptRepository = new \Repositories\ScriptRepository;
$script = $scriptRepository->habitatScript();

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mb-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4"><?php echo htmlspecialchars($habitat['name']); ?></h1>
    <hr>
    <br>
    <img src="/Zoo-Arcadia-New/assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
    <p class="lead"><?php echo htmlspecialchars_decode($habitat['description']); ?></p>
    <br>
    <hr>
    <h2>Commentaires sur l'Habitat</h2>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nom du Vétérinaire</th>
                    <th>Date du Commentaire</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vetComments as $comment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($comment['created_at']))); ?></td>
                        <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($vetComments)): ?>
                    <tr>
                        <td colspan="3" class="text-center">Aucun commentaire disponible pour cet habitat.</td>
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
                    <img class="card-img-top" src="/Zoo-Arcadia-New/assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
                    <div class="card-body text-center">
                        <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                        <button onclick="registerClick(<?php echo htmlspecialchars($animal['id']); ?>)" class="btn btn-success">Plus de détails</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php
echo $script
?>

<?php include '../../src/views/templates/footer.php'; ?>
