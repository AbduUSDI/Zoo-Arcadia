<?php
session_start();

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
$clicksCollection = $mongoDBConnection->getCollection('click_counts');

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
            foreach ($animals as $animal) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($animal['name']) . '</td>';
                echo '<td>' . htmlspecialchars($animal['species']) . '</td>';
                echo '<td>' . htmlspecialchars($animal['habitat_name']) . '</td>';
                echo '<td>';
                if (!empty($animal['image'])) {
                    echo '<img src="../../../../assets/uploads/' . htmlspecialchars($animal['image']) . '" alt="Image de l\'animal" style="width: 100px;">';
                }
                echo '</td>';
                echo '<td>';
                echo '<a href="#" class="btn btn-warning btn-sm btn-edit" data-id="' . $animal['id'] . '" data-toggle="modal" data-target="#editAnimalModal">Modifier</a>';
                echo '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="' . $animal['id'] . '">Supprimer</a>';
                echo '</td>';
                echo '</tr>';
            }
            exit;
        } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                $_POST['name'],
                $_POST['species'],
                $_POST['habitat_id'],
                $animalController->uploadImage($_FILES['image'])
            ];
            $animalController->addAnimal($data);
            echo "Animal ajouté avec succès.";
            exit;
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                $_POST['name'],
                $_POST['species'],
                $_POST['habitat_id'],
                $_POST['id']
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
            $id = $_GET['id'];
            $animal = $animalController->getAnimalDetails($id);
            echo json_encode($animal);
            exit;
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            $id = $_GET['id'];
            $animalController->deleteAnimal($id);
            echo "Animal supprimé avec succès.";
            exit;
        }
    }
}

$animals = $animalController->getAllAnimals();
$habitats = $animalController->getAllHabitats();

include_once '../../../../src/views/templates/header.php';
include_once '../navbar_admin.php';
?>
<style>
h1,h2,h3 {
    text-align: center;
}
body {
    background-image: url('../../../../assets/image/background.jpg');
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Gérer les Animaux</h1>
    <hr>
    <br>
    <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addAnimalModal">Ajouter un Animal</a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nom</th>
                    <th>Espèce</th>
                    <th>Habitat</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Le contenu du tableau sera chargé ici via AJAX -->
            </tbody>
        </table>
    </div>
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
                                <option value="<?php echo $habitat['id']; ?>"><?php echo $habitat['name']; ?></option>
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
                                <option value="<?php echo $habitat['id']; ?>"><?php echo $habitat['name']; ?></option>
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
<script>
$(document).ready(function() {
    function refreshAnimalTable() {
        $.ajax({
            url: 'manage_animals.php?action=list',
            type: 'GET',
            success: function(data) {
                $('tbody').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    }

    $('#addAnimalForm').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'manage_animals.php?action=add',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#responseMessage').html(response);
                $('#addAnimalModal').modal('hide');
                refreshAnimalTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html("Erreur: " + error);
            }
        });
    });

    $(document).on('submit', '#editAnimalForm', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'manage_animals.php?action=edit',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editResponseMessage').html(response);
                $('#editAnimalModal').modal('hide');
                refreshAnimalTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#editResponseMessage').html("Erreur: " + error);
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        var animalId = $(this).data('id');
        $.ajax({
            url: 'manage_animals.php?action=get',
            type: 'GET',
            data: { id: animalId },
            success: function(data) {
                var animal = JSON.parse(data);
                $('#editAnimalId').val(animal.id);
                $('#editName').val(animal.name);
                $('#editSpecies').val(animal.species);
                $('#editHabitatId').val(animal.habitat_id);
                $('#editAnimalModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    });

    $(document).on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var url = 'manage_animals.php?action=delete&id=' + $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer cet animal ?')) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    alert(response);
                    refreshAnimalTable();
                },
                error: function(xhr, status, error) {
                    alert("Erreur: " + error);
                }
            });
        }
    });

    refreshAnimalTable();
});
</script>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
