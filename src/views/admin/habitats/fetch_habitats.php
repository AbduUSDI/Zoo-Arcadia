<?php
session_start();
require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

// Vérification de l'authentification et du rôle de l'utilisateur
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    http_response_code(403); // Accès interdit
    echo 'Accès interdit';
    exit;
}

// Protection CSRF : Génération d'un token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$habitatRepository = new HabitatRepository($conn);
$habitatService = new HabitatService($habitatRepository);
$habitatController = new HabitatController($habitatService);

$habitatsList = $habitatController->getAllHabitats();
?>

<table class="table table-bordered table-striped table-hover">
    <thead class="thead-dark">
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Image</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($habitatsList as $habitat): ?>
            <tr>
                <td><?php echo htmlspecialchars($habitat['id']); ?></td>
                <td><?php echo htmlspecialchars($habitat['name']); ?></td>
                <td>
                    <?php if (!empty($habitat['image'])): ?>
                        <img src="../../../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="Image de l'habitat" style="width: 250px;">
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($habitat['description']); ?></td>
                <td>
                    <a href="#" class="btn btn-warning btn-edit" data-id="<?php echo $habitat['id']; ?>">Modifier</a>
                    <!-- Inclusion du token CSRF dans l'URL de suppression -->
                    <a href="manage_habitats.php?action=delete&id=<?php echo $habitat['id']; ?>&csrf_token=<?php echo $_SESSION['csrf_token']; ?>" class="btn btn-danger btn-delete">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
