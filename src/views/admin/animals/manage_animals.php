<?php
session_start();

// Durée de vie de la session
$sessionLifetime = 1800;

// Vérification si l'utilisateur est connecté et a le bon rôle
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

// Vérification de l'expiration de la session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Génération d'un token CSRF pour protéger contre les attaques CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Database\MongoDBConnection;
use Repositories\AnimalRepository;
use Repositories\ClickRepository;
use Services\ClickService;
use Services\AnimalService;
use Controllers\AnimalController;

// Connexion à la base de données MySQL
$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->connect();

// Connexion à la base de données MongoDB
$mongoDBConnection = new MongoDBConnection();
$clicksCollection = $mongoDBConnection->getCollection('clicks');

// Création des instances des dépôts
$animalRepository = new AnimalRepository($pdo);
$clickRepository = new ClickRepository($clicksCollection);
$clickService = new ClickService($clickRepository);

// Création des instances des services
$animalService = new AnimalService($animalRepository, $clickRepository);

// Création du contrôleur
$animalController = new AnimalController($animalService, $clickService);

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    if ($action) {
        if ($action === 'list') {
            $animals = $animalController->getAllAnimals();

            // Grouper les animaux par habitat
            $animalsByHabitat = [];
            foreach ($animals as $animal) {
                $habitatName = htmlspecialchars($animal['habitat_name']);
                if (!isset($animalsByHabitat[$habitatName])) {
                    $animalsByHabitat[$habitatName] = [];
                }
                $animalsByHabitat[$habitatName][] = $animal;
            }

            // Afficher les animaux par habitat dans un menu de sélection horizontal
            foreach ($animalsByHabitat as $habitatName => $animals) {
                echo '<h3 class="my-4">' . $habitatName . '</h3>';
                echo '<div class="animal-selection-container">';
                echo '<div class="animal-list">';

                foreach ($animals as $animal) {
                    echo '<div class="animal-card">';
                    if (!empty($animal['image'])) {
                        echo '<img src="/Zoo-Arcadia-New/assets/uploads/' . htmlspecialchars($animal['image']) . '" alt="Image de l\'animal" class="animal-image">';
                    }
                    echo '<div class="animal-info">';
                    echo '<h5>' . htmlspecialchars($animal['name']) . '</h5>';
                    echo '<p>' . htmlspecialchars_decode($animal['species']) . '</p>';
                    echo '<p>' . htmlspecialchars($animal['habitat_name']) . '</p>';
                    echo '<a href="#" class="btn btn-warning btn-sm btn-edit" data-id="' . $animal['id'] . '" data-toggle="modal" data-target="#editAnimalModal">Modifier</a>';
                    echo '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="' . $animal['id'] . '">Supprimer</a>';
                    echo '</div>';
                    echo '</div>';
                }

                echo '</div>'; // Fin de la liste d'animaux
                echo '</div>'; // Fin du conteneur de sélection
            }

            exit;
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $data = [
                htmlspecialchars($_POST['name']),
                htmlspecialchars($_POST['species']),
                intval($_POST['habitat_id']),
                $animalController->uploadImage($_FILES['image'])
            ];
            $animalController->addAnimal($data);
            echo "Animal ajouté avec succès.";
            exit;
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validation CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $data = [
                htmlspecialchars($_POST['name']),
                htmlspecialchars($_POST['species']),
                intval($_POST['habitat_id']),
                intval($_POST['id'])
            ];
            if (!empty($_FILES['image']['name'])) {
                array_splice($data, 3, 0, [$animalController->uploadImage($_FILES['image'])]);
                $animalController->updateAnimalWithImage($data);
            } else {
                $animalController->updateAnimalWithoutImage($data);
            }
            echo "Animal modifié avec succès.";
            exit;
        } elseif ($action === 'get' && isset($_GET['id'])) {
            $id = intval($_GET['id']);
            $animal = $animalController->getAnimalDetails($id);
            echo json_encode($animal);
            exit;
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            // Validation CSRF
            if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $id = intval($_GET['id']);
            $animalController->deleteAnimal($id);
            echo "Animal supprimé avec succès.";
            exit;
        }
    }
}

$scriptRepository = new Repositories\ScriptRepository;
$script = $scriptRepository->manageAnimalAdminScript();

$animals = $animalController->getAllAnimals();
$habitats = $animalController->getAllHabitats();

include_once '../../../../src/views/templates/header.php';
include_once '../navbar_admin.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Gérer les Animaux</h1>
    <hr>
    <br>
    <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addAnimalModal">Ajouter un Animal</a>
    <main></main>
</div>

<!-- Modal pour Ajouter un Animal -->
<div class="modal fade" id="addAnimalModal" tabindex="-1" role="dialog" aria-labelledby="addAnimalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addAnimalModalLabel">Ajouter un Animal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addAnimalForm" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-group">
                        <label for="name">Nom:</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="species">Espèce:</label>
                        <input type="text" id="species" name="species" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="habitat_id">Habitat:</label>
                        <select id="habitat_id" name="habitat_id" class="form-control" required>
                            <?php foreach ($habitats as $habitat): ?>
                                <option value="<?php echo htmlspecialchars($habitat['id']); ?>"><?php echo htmlspecialchars($habitat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="image">Image:</label>
                        <input type="file" id="image" name="image" class="form-control-file" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-success">Ajouter Animal</button>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour Modifier un Animal -->
<div class="modal fade" id="editAnimalModal" tabindex="-1" role="dialog" aria-labelledby="editAnimalModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAnimalModalLabel">Modifier un Animal</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editAnimalForm" enctype="multipart/form-data">
                    <input type="hidden" id="editAnimalId" name="id">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                    <div class="form-group">
                        <label for="editName">Nom:</label>
                        <input type="text" id="editName" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editSpecies">Espèce:</label>
                        <input type="text" id="editSpecies" name="species" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="editHabitatId">Habitat:</label>
                        <select id="editHabitatId" name="habitat_id" class="form-control" required>
                            <?php foreach ($habitats as $habitat): ?>
                                <option value="<?php echo htmlspecialchars($habitat['id']); ?>"><?php echo htmlspecialchars($habitat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editImage">Image:</label>
                        <input type="file" id="editImage" name="image" class="form-control-file" accept="image/*">
                    </div>
                    <button type="submit" class="btn btn-success">Modifier Animal</button>
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
