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
use Repositories\ServiceRepository;
use Services\ServiceService;
use Controllers\ServiceController;

// Protection CSRF : Génération d'un token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories
$serviceRepository = new ServiceRepository($db);

// Initialisation des services
$serviceService = new ServiceService($serviceRepository);

// Initialisation des contrôleurs
$serviceController = new ServiceController($serviceService);

// Gestion des actions CRUD via AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    if ($action) {
        if ($action === 'list') {
            $services = $serviceController->getServices();
            foreach ($services as $service) {
                echo '<div class="col-md-6">';
                echo '<div class="card mb-4 shadow-sm">';
                echo '<img src="../../../../assets/uploads/' . htmlspecialchars($service['image']) . '" class="card-img-top" alt="Image du service">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . htmlspecialchars($service['name']) . '</h5>';
                echo '<p class="card-text">' . htmlspecialchars_decode($service['description']) . '</p>';
                echo '<a href="#" class="btn btn-warning btn-sm btn-edit" data-id="' . htmlspecialchars($service['id']) . '" data-name="' . htmlspecialchars($service['name']) . '" data-description="' . htmlspecialchars($service['description']) . '" data-image="' . htmlspecialchars($service['image']) . '" data-toggle="modal" data-target="#editServiceModal">Modifier</a>';
                echo '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="' . htmlspecialchars($service['id']) . '">Supprimer</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            exit;
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $name = htmlspecialchars($_POST['name']);
            $description = htmlspecialchars($_POST['description']);
            $image = $_FILES['image'];

            try {
                $imageName = $serviceController->addServiceImage($image);
                $serviceController->addService($name, $description, $imageName);
                echo "Service ajouté avec succès !";
            } catch (Exception $erreur) {
                echo "Erreur: " . $erreur->getMessage();
            }
            exit;
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            // Modification d'un service
            $id = filter_input(INPUT_POST, 'serviceId', FILTER_VALIDATE_INT);
            $name = htmlspecialchars($_POST['name']);
            $description = htmlspecialchars($_POST['description']);
            $image = $_FILES['image'];

            try {
                if ($image['error'] === UPLOAD_ERR_NO_FILE) {
                    $serviceController->updateServiceWithoutImage($id, $name, $description);
                } else {
                    $imageName = $serviceController->addServiceImage($image);
                    $serviceController->updateServiceWithImage($id, $name, $description, $imageName);
                }
                echo "Service modifié avec succès !";
            } catch (Exception $erreur) {
                echo "Erreur: " . $erreur->getMessage();
            }
            exit;
        } elseif ($action === 'get' && isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $service = $serviceController->getServiceById($id);
                echo json_encode($service);
            } else {
                echo json_encode(['error' => 'ID invalide']);
            }
            exit;
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            // Vérification du token CSRF pour la suppression
            if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $serviceController->deleteService($id);
                echo "Service supprimé avec succès.";
            } else {
                echo "Erreur: ID invalide.";
            }
            exit;
        }
    }
}

$scriptRepository = new Repositories\ScriptRepository();

$script = $scriptRepository->manageServiceScript();

include '../../../views/templates/header.php';
include '../navbar_employee.php';
?>

<body>
<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <!-- Bouton pour ajouter un service -->
    <div class="row button-row">
        <div class="col-md-12 text-center">
            <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addServiceModal">Ajouter un service</a>
        </div>
    </div>
    
    <!-- Conteneur des cartes de services -->
    <div class="row" id="servicesTable">
        <!-- Les cartes des services seront injectées ici par AJAX -->
    </div>
</div>

<!-- Modal pour Ajouter un Service -->
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Ajouter un Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addServiceForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                    <button type="submit" class="btn btn-primary">Ajouter Service</button>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour Modifier un Service -->
<div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Modifier un Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editServiceForm" enctype="multipart/form-data">
                    <input type="hidden" id="editServiceId" name="serviceId">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                    <button type="submit" class="btn btn-primary">Modifier Service</button>
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

<?php include '../../../views/templates/footerconnected.php'; ?>
