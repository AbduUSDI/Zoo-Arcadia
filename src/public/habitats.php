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

// Inclure les fichiers nécessaires
require '../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories
$habitatRepository = new HabitatRepository($db);

// Initialisation des services
$habitatService = new HabitatService($habitatRepository);

// Initialisation des contrôleurs
$habitatController = new HabitatController($habitatService);

// Récupérer tous les habitats
try {
    $habitats = $habitatController->getAllHabitats();
} catch (Exception $e) {
    // Gérer l'erreur en affichant un message utilisateur ou en enregistrant l'erreur dans un journal
    die("Erreur lors de la récupération des habitats : " . htmlspecialchars($e->getMessage()));
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<style>
h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 88px;
}
.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mb-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Tous les Habitats</h1>
    <hr>
    <br>
    <div class="row">
        <?php if (!empty($habitats)): ?>
            <?php foreach ($habitats as $habitat): ?>
                <div class="col-md-4">
                    <div class="card mb-4 text-dark">
                        <img class="card-img-top" src="../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title" style="text-align: center;"><?php echo htmlspecialchars($habitat['name']); ?></h5>
                            <a href="index.php?page=habitat&id=<?php echo htmlspecialchars($habitat['id']); ?>" class="btn btn-success">Voir les habitants</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center">Aucun habitat disponible pour le moment.</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../../src/views/templates/footer.php'; ?>
