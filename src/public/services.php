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
use Repositories\ServiceRepository;
use Services\ServiceService;
use Controllers\ServiceController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories
$serviceRepository = new ServiceRepository($db);

// Initialisation des services
$serviceService = new ServiceService($serviceRepository);

// Initialisation des contrôleurs
$serviceController = new ServiceController($serviceService);

// Récupérer tous les services
$services = $serviceController->getServices();

// Inclure les fichiers de template
include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4" style="text-align: center;">Nos services</h1>
    <hr>
    <br>
    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="col-md-6">
                <div class="card mb-4 text-black bg-light border-success">
                    <?php if (!empty($service['image'])): ?>
                        <img src="/Zoo-Arcadia-New/assets/uploads/<?php echo htmlspecialchars($service['image'], ENT_QUOTES, 'UTF-8'); ?>" class="card-img-top" alt="Image du Service">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars_decode($service['description']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../src/views/templates/footer.php'; ?>
