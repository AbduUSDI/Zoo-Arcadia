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

require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = new Database();
$conn = $db->connect();

$services = new Service($conn);
$servicesList = $services->getServices();

include '../../../src/views/templates/header.php';
include '../navbar_admin.php';
?>
    <style>
        h1,h2,h3 {
            text-align: center;
        }

        body {
            background-image: url('../../../assets/image/background.jpg');
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

    // Fonctions pour le CRUD sans rechargement de page grâce à AJAX

$(document).ready(function() {
    // Fonction pour rafraîchir la table des services
    function refreshServiceTable() {
        $.ajax({
            url: 'fetch_services.php',
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
            url: 'add_service_handler.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#responseMessage').html(response);
                $('#addServiceModal').modal('hide');
                refreshServiceTable();
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
            url: 'edit_service_handler.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editResponseMessage').html(response);
                $('#editServiceModal').modal('hide');
                refreshServiceTable();
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
            url: 'get_service.php',
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
        var url = $(this).attr('href');

        if (confirm('Êtes-vous sûr de vouloir supprimer ce service ?')) {
            $.ajax({
                url: url,
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

<?php include '../../../src/views/templates/footerconnected.php'; ?>
</body>
</html>
