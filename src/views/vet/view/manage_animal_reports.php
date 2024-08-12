<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification du rôle de l'utilisateur et de la session
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
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

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ReportRepository;
use Services\ReportService;
use Controllers\ReportController;
use Repositories\AnimalRepository;

// Connexion à la base de données MySQL
$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->connect();

// Création des instances des dépôts et services
$reportRepository = new ReportRepository($pdo);
$animalRepository = new AnimalRepository($pdo);
$reportService = new ReportService($reportRepository);
$reportController = new ReportController($reportService);

$animals = $animalRepository->getAll();
$distinctVisitDates = $reportController->getDistinctDates();

// Gestion des actions AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    if ($action) {
        try {
            if ($action === 'list') {
                $visit_date = filter_input(INPUT_GET, 'visit_date', FILTER_SANITIZE_STRING);
                $animal_id = filter_input(INPUT_GET, 'animal_id', FILTER_VALIDATE_INT);
                $reports = $reportController->getReports($visit_date, $animal_id);

                foreach ($reports as $report) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($report['animal_name'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($report['visit_date'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($report['health_status'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($report['food_given'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($report['food_quantity'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>' . htmlspecialchars($report['details'], ENT_QUOTES, 'UTF-8') . '</td>';
                    echo '<td>';
                    echo '<a href="#" class="btn btn-warning btn-sm btn-edit" data-id="' . htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8') . '" data-animal_id="' . htmlspecialchars($report['animal_name'], ENT_QUOTES, 'UTF-8') . '" data-visit_date="' . htmlspecialchars($report['visit_date'], ENT_QUOTES, 'UTF-8') . '" data-health_status="' . htmlspecialchars($report['health_status'], ENT_QUOTES, 'UTF-8') . '" data-food_given="' . htmlspecialchars($report['food_given'], ENT_QUOTES, 'UTF-8') . '" data-food_quantity="' . htmlspecialchars($report['food_quantity'], ENT_QUOTES, 'UTF-8') . '" data-details="' . htmlspecialchars($report['details'], ENT_QUOTES, 'UTF-8') . '" data-toggle="modal" data-target="#editReportModal">Modifier</a>';
                    echo '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="' . htmlspecialchars($report['id'], ENT_QUOTES, 'UTF-8') . '">Supprimer</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                exit;
            } elseif ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Vérification du token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die("Échec de la validation CSRF.");
                }

                // Validation des entrées
                $vet_id = $_SESSION['user']['id'];
                $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
                $health_status = filter_input(INPUT_POST, 'health_status', FILTER_SANITIZE_STRING);
                $food_given = filter_input(INPUT_POST, 'food_given', FILTER_SANITIZE_STRING);
                $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
                $visit_date = filter_input(INPUT_POST, 'visit_date', FILTER_SANITIZE_STRING);
                $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);

                if ($animal_id && $health_status && $food_given && $food_quantity !== false && $visit_date) {
                    $reportController->addReport($vet_id, $animal_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
                    echo "Rapport ajouté avec succès.";
                } else {
                    throw new Exception("Données invalides. Veuillez vérifier vos entrées.");
                }
                exit;
            } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                // Vérification du token CSRF
                if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                    die("Échec de la validation CSRF.");
                }

                // Validation des entrées
                $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
                $vet_id = $_SESSION['user']['id'];
                $visit_date = filter_input(INPUT_POST, 'visit_date', FILTER_SANITIZE_STRING);
                $health_status = filter_input(INPUT_POST, 'health_status', FILTER_SANITIZE_STRING);
                $food_given = filter_input(INPUT_POST, 'food_given', FILTER_SANITIZE_STRING);
                $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
                $details = filter_input(INPUT_POST, 'details', FILTER_SANITIZE_STRING);

                if ($id && $animal_id && $health_status && $food_given && $food_quantity !== false && $visit_date) {
                    $reportController->updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
                    echo "Rapport modifié avec succès.";
                } else {
                    throw new Exception("Données invalides. Veuillez vérifier vos entrées.");
                }
                exit;
            } elseif ($action === 'get' && isset($_GET['id'])) {
                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                $report = $reportController->getReportById($id);
                echo json_encode($report);
                exit;
            } elseif ($action === 'delete' && isset($_GET['id'])) {
                // Vérification du token CSRF pour la suppression
                if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
                    die("Échec de la validation CSRF.");
                }

                $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
                if ($id) {
                    $reportController->deleteReport($id);
                    echo "Rapport supprimé avec succès.";
                } else {
                    throw new Exception("ID de rapport invalide.");
                }
                exit;
            }
        } catch (Exception $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}

include_once '../../../../src/views/templates/header.php';
include_once '../navbar_vet.php';
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
    <h1 class="my-4">Gérer les Rapports des Animaux</h1>
    <hr>
    <br>
    <form id="filterForm" method="GET">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="filterDate">Date de Visite:</label>
                <select class="form-control" id="filterDate" name="visit_date">
                    <option value="">Toutes les dates</option>
                    <?php foreach ($distinctVisitDates as $date): ?>
                        <option value="<?= htmlspecialchars($date['visit_date'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($date['visit_date'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="filterAnimal">Animal:</label>
                <select class="form-control" id="filterAnimal" name="animal_id">
                    <option value="">Tous les animaux</option>
                    <?php foreach ($animals as $animal): ?>
                        <option value="<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-success" id="filterButton">Filtrer</button>
    </form>
    <br>
    <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addReportModal">Ajouter un Rapport</a>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Animal</th>
                    <th>Date de Passage</th>
                    <th>État</th>
                    <th>Nourriture</th>
                    <th>Grammage (en grammes)</th>
                    <th>Détails</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Le contenu du tableau sera chargé ici via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal pour Ajouter un Rapport -->
<div class="modal fade" id="addReportModal" tabindex="-1" role="dialog" aria-labelledby="addReportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addReportModalLabel">Ajouter un Rapport</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addReportForm">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group">
                        <label for="animal_id">Animal:</label>
                        <select class="form-control" id="animal_id" name="animal_id" required>
                            <?php foreach ($animals as $animal): ?>
                                <option value="<?= htmlspecialchars($animal['id']) ?>"><?= htmlspecialchars($animal['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="visit_date">Date de Passage:</label>
                        <input type="date" class="form-control" id="visit_date" name="visit_date" required>
                    </div>
                    <div class="form-group">
                        <label for="health_status">État:</label>
                        <input type="text" class="form-control" id="health_status" name="health_status" required>
                    </div>
                    <div class="form-group">
                        <label for="food_given">Nourriture:</label>
                        <input type="text" class="form-control" id="food_given" name="food_given" required>
                    </div>
                    <div class="form-group">
                        <label for="food_quantity">Grammage (en grammes):</label>
                        <input type="number" class="form-control" id="food_quantity" name="food_quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="details">Détails (facultatif):</label>
                        <textarea class="form-control" id="details" name="details" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Ajouter</button>
                </form>
                <div id="responseMessage"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour Modifier un Rapport -->
<div class="modal fade" id="editReportModal" tabindex="-1" role="dialog" aria-labelledby="editReportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editReportModalLabel">Modifier le Rapport</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editReportForm">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" id="editReportId" name="id">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="editAnimal">Animal:</label>
                        <select class="form-control" id="editAnimal" name="animal_id" required>
                            <?php foreach ($animals as $animal): ?>
                                <option value="<?= htmlspecialchars($animal['id'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($animal['name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="editDate">Date de Passage:</label>
                        <input type="date" class="form-control" id="editDate" name="visit_date" required>
                    </div>
                    <div class="form-group">
                        <label for="editHealthStatus">État:</label>
                        <input type="text" class="form-control" id="editHealthStatus" name="health_status" required>
                    </div>
                    <div class="form-group">
                        <label for="editFoodGiven">Nourriture:</label>
                        <input type="text" class="form-control" id="editFoodGiven" name="food_given" required>
                    </div>
                    <div class="form-group">
                        <label for="editFoodQuantity">Grammage (en grammes):</label>
                        <input type="number" class="form-control" id="editFoodQuantity" name="food_quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="editDetails">Détails (facultatif):</label>
                        <textarea class="form-control" id="editDetails" name="details" rows="4"></textarea>
                    </div>
                    <button type="submit" class="btn btn-success">Enregistrer</button>
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
    
    // Ouvrir le modal si le paramètre 'action=add' est présent dans l'URL
    <?php if (isset($_GET['action']) && $_GET['action'] === 'add'): ?>
        $('#addReportModal').modal('show');
    <?php endif; ?>

    function refreshReportsTable() {
        $.ajax({
            url: 'manage_animal_reports.php?action=list',
            type: 'GET',
            success: function(data) {
                $('tbody').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    }

    $('#addReportForm').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'manage_animal_reports.php?action=add',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#responseMessage').html(response);
                $('#addReportModal').modal('hide');
                refreshReportsTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#responseMessage').html("Erreur: " + error);
            }
        });
    });

    $('#editReportForm').on('submit', function(event) {
        event.preventDefault();
        var formData = $(this).serialize();

        $.ajax({
            url: 'manage_animal_reports.php?action=edit',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#editResponseMessage').html(response);
                $('#editReportModal').modal('hide');
                refreshReportsTable();
                $('body').removeClass('modal-open');
                $('.modal-backdrop').remove();
            },
            error: function(xhr, status, error) {
                $('#editResponseMessage').html("Erreur: " + error);
            }
        });
    });

    $(document).on('click', '.btn-edit', function() {
        var reportId = $(this).data('id');
        $.ajax({
            url: 'manage_animal_reports.php?action=get',
            type: 'GET',
            data: { id: reportId },
            success: function(data) {
                var report = JSON.parse(data);
                $('#editReportId').val(report.id);
                $('#editAnimal').val(report.animal_id);
                $('#editDate').val(report.visit_date);
                $('#editHealthStatus').val(report.health_status);
                $('#editFoodGiven').val(report.food_given);
                $('#editFoodQuantity').val(report.food_quantity);
                $('#editDetails').val(report.details);
                $('#editReportModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    });

    $(document).on('click', '.btn-delete', function(event) {
        event.preventDefault();
        var reportId = $(this).data('id');
        if (confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?')) {
            $.ajax({
                url: 'manage_animal_reports.php?action=delete&id=' + reportId + '&csrf_token=<?= $_SESSION['csrf_token']; ?>',
                type: 'GET',
                success: function(response) {
                    alert(response);
                    refreshReportsTable();
                },
                error: function(xhr, status, error) {
                    alert("Erreur: " + error);
                }
            });
        }
    });

    refreshReportsTable();

    $('#filterButton').on('click', function() {
        var visitDate = $('#filterDate').val();
        var animalId = $('#filterAnimal').val();
        $.ajax({
            url: 'manage_animal_reports.php?action=list',
            type: 'GET',
            data: {
                visit_date: visitDate,
                animal_id: animalId
            },
            success: function(data) {
                $('tbody').html(data);
            },
            error: function(xhr, status, error) {
                console.log("Erreur: " + error);
            }
        });
    });
});
</script>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
