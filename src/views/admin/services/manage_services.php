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
                // Ajout du conteneur du tableau ici
                echo '<table class="table table-bordered table-striped table-hover">';
                echo '<thead  class="thead-dark">';
                echo '<tr>';
                echo '<th>Nom</th>';
                echo '<th>Description</th>';
                echo '<th>Image</th>';
                echo '<th>Actions</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
            foreach ($services as $service) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($service['name']) . '</td>';
                echo '<td>' . htmlspecialchars($service['description']) . '</td>';
                echo '<td>';
                if (!empty($service['image'])) {
                    echo '<img src="../../../../assets/uploads/' . htmlspecialchars($service['image']) . '" alt="Image du service" style="width: 250px;">';
                }
                echo '</td>';
                echo '<td>';
                echo '<a href="#" class="btn btn-warning btn-sm btn-edit" data-id="' . htmlspecialchars($service['id']) . '" data-name="' . htmlspecialchars($service['name']) . '" data-description="' . htmlspecialchars($service['description']) . '" data-image="' . htmlspecialchars($service['image']) . '" data-toggle="modal" data-target="#editServiceModal">Modifier</a>';
                echo '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="' . htmlspecialchars($service['id']) . '">Supprimer</a>';
                echo '</td>';
                echo '</tr>';
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
                    // Mise à jour sans modification de l'image
                    $serviceController->updateServiceWithoutImage($id, $name, $description);
                } else {
                    // Traitement de l'image uploadée et mise à jour avec l'image
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

include '../../../views/templates/header.php';
include '../navbar_employee.php';
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
</head>
<body>
<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <div class="table-responsive">
        <br>
        <hr>
        <h1 class="my-4">Gérer les services</h1>
        <hr>
        <br>
        <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addServiceModal">Ajouter un service</a>
        <div id="servicesTable">
            <!-- La table sera remplie ici par AJAX -->
        </div>
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
<script>
$(document).ready(function() {
    // Fonction pour rafraîchir la table des services
    function refreshServiceTable() {
        $.ajax({
            url: 'manage_services.php?action=list',
            type: 'GET',
            success: function(data) {
                $('#servicesTable').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    }

    // Initialisation de la table des services
    refreshServiceTable();

    // Formulaire pour ajouter un service
    $('#addServiceForm').on('submit', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'manage_services.php?action=add',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#responseMessage').html(response);
                $('#addServiceModal').modal('hide');
                refreshServiceTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html("Erreur: " + error);
            }
        });
    });

    // Formulaire pour modifier un service
    $(document).on('submit', '#editServiceForm', function(event) {
        event.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            url: 'manage_services.php?action=edit',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editResponseMessage').html(response);
                $('#editServiceModal').modal('hide');
                refreshServiceTable();
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
        var serviceId = $(this).data('id');
        $.ajax({
            url: 'manage_services.php?action=get',
            type: 'GET',
            data: { id: serviceId },
            success: function(data) {
                var service = JSON.parse(data);
                $('#editServiceId').val(service.id);
                $('#editName').val(service.name);
                $('#editDescription').val(service.description);
                $('#editServiceModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    });

    // Fonction pour supprimer un service
    $(document).on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var serviceId = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
            $.ajax({
                url: 'manage_services.php?action=delete&id=' + serviceId + '&csrf_token=<?php echo $_SESSION['csrf_token']; ?>',
                type: 'GET',
                success: function(response) {
                    alert(response);
                    refreshServiceTable();
                },
                error: function(xhr, status, error) {
                    alert("Erreur: " + error);
                }
            });
        }
    });
});
</script>

<?php include '../../../views/templates/footerconnected.php'; ?>
