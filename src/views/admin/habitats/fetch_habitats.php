<?php
session_start();
require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    http_response_code(403); 
    echo 'Accès interdit';
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$habitatRepository = new HabitatRepository($conn);
$habitatService = new HabitatService($habitatRepository);
$habitatController = new HabitatController($habitatService);

$habitatsList = $habitatController->getAllHabitats();

foreach ($habitatsList as $habitat): ?>
    <div class="col-md-6">
        <div class="card mb-4 shadow-sm">
            <img src="/Zoo-Arcadia-New/assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" class="card-img-top" alt="Image de l'habitat">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($habitat['name']); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars_decode($habitat['description']); ?></p>
                <a href="#" class="btn btn-warning btn-edit" data-id="<?php echo $habitat['id']; ?>">Modifier</a>
                <a href="/Zoo-Arcadia-New/admin/habitats?action=delete&id=<?php echo $habitat['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" class="btn btn-danger btn-delete">Supprimer</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
