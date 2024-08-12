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

$habitats = $habitatController->getAllHabitats();

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
    <div class="table-responsive">
        <br>
        <hr>
        <h1 class="my-4">Gestion des Habitats</h1>
        <hr>
        <br>
        <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addHabitatModal">Ajouter un habitat</a>
        <div id="habitatsTable">
            <!-- La table sera remplie ici par AJAX -->
        </div>
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
                    <!-- Inclusion du token CSRF dans le formulaire -->
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
<script>

// Fonctions pour le CRUD sans rechargement de page grâce à AJAX

$(document).ready(function() {
    // Fonction pour rafraîchir la table des habitats
    function refreshHabitatTable() {
        $.ajax({
            url: 'fetch_habitats.php',
            type: 'GET',
            success: function(data) {
                $('#habitatsTable').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    }

    // Initialisation de la table des habitats
    refreshHabitatTable();

    // Formulaire pour ajouter un habitat

    $('#addHabitatForm').on('submit', function(event) {
        event.preventDefault();
        
        // Validation basique des entrées avec JavaScript
        var name = $('#name').val();
        var description = $('#description').val();
        if (name.trim() === '' || description.trim() === '') {
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }

        var formData = new FormData(this);

        $.ajax({
            url: 'manage_habitats.php?action=add',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#responseMessage').html(response);
                $('#addHabitatModal').modal('hide');
                refreshHabitatTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html("Erreur: " + error);
            }
        });
    });

    // Formulaire pour modifier un habitat

    $(document).on('submit', '#editHabitatForm', function(event) {
        event.preventDefault();

        // Validation basique des entrées avec JavaScript
        var name = $('#editName').val();
        var description = $('#editDescription').val();
        if (name.trim() === '' || description.trim() === '') {
            alert('Veuillez remplir tous les champs obligatoires.');
            return;
        }

        var formData = new FormData(this);

        $.ajax({
            url: 'manage_habitats.php?action=edit',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editResponseMessage').html(response);
                $('#editHabitatModal').modal('hide');
                refreshHabitatTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#editResponseMessage').html("Erreur: " + error);
            }
        });
    });

    // Fonction pour remplir le formulaire de modification

    $(document).on('click', '.btn-edit', function() {
        var habitatId = $(this).data('id');
        $.ajax({
            url: 'manage_habitats.php?action=get',
            type: 'GET',
            data: { id: habitatId },
            success: function(data) {
                var habitat = JSON.parse(data);
                $('#editHabitatId').val(habitat.id);
                $('#editName').val(habitat.name);
                $('#editDescription').val(habitat.description);
                $('#editHabitatModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    });

    // Fonction pour supprimer un habitat

    $(document).on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var url = $(this).attr('href');

        if (confirm('Êtes-vous sûr de vouloir supprimer ce habitat ?')) {
            $.ajax({
                url: url,
                type: 'GET',
                success: function(response) {
                    alert(response);
                    refreshHabitatTable();
                },
                error: function(xhr, status, error) {
                    alert("Erreur: " + error);
                }
            });
        }
    });
});
</script>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
