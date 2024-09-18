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
    die("Erreur lors de la récupération des habitats : " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
}

include '../../../views/templates/header.php';
include '../navbar_employee.php';
?>

<div class="container habitat-container mt-5">
    <h1 class="habitat-title my-4">Habitats</h1>
    <div class="habitat-cards">
        <?php foreach ($habitats as $habitat): ?>
            <div class="habitat-card">
                <div class="habitat-card-image">
                    <img src="../../../../assets/uploads/<?= htmlspecialchars($habitat['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($habitat['name'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="habitat-card-body">
                    <h5 class="habitat-card-title"><?= htmlspecialchars($habitat['name'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <p class="habitat-card-description"><?= htmlspecialchars_decode($habitat['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../../views/templates/footerconnected.php'; ?>
