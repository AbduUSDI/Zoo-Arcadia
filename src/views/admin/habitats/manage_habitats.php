<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
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

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

// Connexion à la base de données
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$habitatRepository = new HabitatRepository($conn);
$habitatService = new HabitatService($habitatRepository);
$habitatController = new HabitatController($habitatService);

// Génération d'un token CSRF pour sécuriser les formulaires
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestion des actions AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    if ($action) {
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $name = $_POST['name'];
                $description = $_POST['description'];
                $image = $_FILES['image'];
                $habitatController->addHabitat($name, $description, $image);
                echo "Habitat ajouté avec succès.";
            } else {
                echo "Échec de la validation du token CSRF.";
            }
            exit;
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            if (hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                $id = $_POST['habitatId'];
                $name = $_POST['name'];
                $description = $_POST['description'];
                $image = $_FILES['image'] ?? null;
                $habitatController->updateHabitat($id, $name, $description, $image);
                echo "Habitat modifié avec succès.";
            } else {
                echo "Échec de la validation du token CSRF.";
            }
            exit;
        } elseif ($action === 'get' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $habitat = $habitatController->getHabitatById($id);
            echo json_encode($habitat);
            exit;
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $habitatController->deleteHabitat($id);
            echo "Habitat supprimé avec succès.";
            exit;
        }
    }
}

$scriptRepository = new Repositories\ScriptRepository();

$script = $scriptRepository->manageHabitatScript();

$habitats = $habitatController->getAllHabitats();

include_once '../../../../src/views/templates/header.php';
include_once '../navbar_admin.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <div class="row">
        <a href="#" class="btn btn-success mb-4 addButton" data-toggle="modal" data-target="#addHabitatModal">Ajouter un habitat</a>
    </div>
    <div class="row" id="habitatsTable">
        <!-- Les cartes des habitats seront injectées ici par AJAX -->
    </div>
</div>

<!-- Modal pour Ajouter un Habitat -->
<div class="modal fade" id="addHabitatModal" tabindex="-1" role="dialog" aria-labelledby="addHabitatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addHabitatModalLabel">Ajouter un Habitat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addHabitatForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-group">
                        <label for="name">Nom:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" class="form-control-file" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter Habitat</button>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour Modifier un Habitat -->
<div class="modal fade" id="editHabitatModal" tabindex="-1" role="dialog" aria-labelledby="editHabitatModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editHabitatModalLabel">Modifier un Habitat</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editHabitatForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <input type="hidden" id="editHabitatId" name="habitatId">
                    <div class="form-group">
                        <label for="editName">Nom:</label>
                        <input type="text" id="editName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editDescription">Description:</label>
                        <textarea id="editDescription" name="description" class="form-control" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="editImage">Image:</label>
                        <input type="file" id="editImage" name="image" class="form-control-file" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-primary">Modifier Habitat</button>
                </form>
                <div id="editResponseMessage"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<?php 
echo $script;
?>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
