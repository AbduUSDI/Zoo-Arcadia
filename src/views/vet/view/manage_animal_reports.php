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

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ReportRepository;
use Services\ReportService;
use Controllers\ReportController;
use Repositories\AnimalRepository;

// Protection CSRF : Génération d'un token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Connexion à la base de données MySQL
$dbConnection = new DatabaseConnection();
$pdo = $dbConnection->connect();

// Création des instances des dépôts
$reportRepository = new ReportRepository($pdo);
$animalRepository = new AnimalRepository($pdo);

// Création des instances des services
$reportService = new ReportService($reportRepository);

// Création du contrôleur
$reportController = new ReportController($reportService);
$animals = $animalRepository->getAll();
$distinctVisitDates = $reportController->getDistinctDates();

// Gestion des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    if ($action) {
        if ($action === 'list') {
            $visit_date = filter_input(INPUT_GET, 'visit_date', FILTER_SANITIZE_STRING);
            $animal_id = filter_input(INPUT_GET, 'animal_id', FILTER_VALIDATE_INT);
            $reports = $reportController->getReports($visit_date, $animal_id);

            // Affichage des rapports sous forme de cartes
            foreach ($reports as $report) {
                echo '<div class="card report-card mb-4 shadow-sm">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Rapport du vétérinaire</h5>';
                echo '<p class="card-text"><strong>Animal :</strong> ' . htmlspecialchars($report['animal_name']) . '</p>';
                echo '<p class="card-text"><strong>Date de Visite :</strong> ' . htmlspecialchars($report['visit_date']) . '</p>';
                echo '<p class="card-text"><strong>État de Santé :</strong> ' . htmlspecialchars($report['health_status']) . '</p>';
                echo '<p class="card-text"><strong>Nourriture Donnée :</strong> ' . htmlspecialchars($report['food_given']) . '</p>';
                echo '<p class="card-text"><strong>Quantité de Nourriture :</strong> ' . htmlspecialchars($report['food_quantity']) . ' grammes</p>';
                echo '<p class="card-text"><strong>Détails :</strong> ' . htmlspecialchars_decode($report['details']) . '</p>';
                echo '<div class="d-flex justify-content-between align-items-center">';
                echo '<div class="btn-group">';
                echo '<a href="#" class="btn btn-warning btn-sm btn-edit" data-id="' . htmlspecialchars($report['id']) . '" data-animal_id="' . htmlspecialchars($report['animal_name']) . '" data-visit_date="' . htmlspecialchars($report['visit_date']) . '" data-health_status="' . htmlspecialchars($report['health_status']) . '" data-food_given="' . htmlspecialchars($report['food_given']) . '" data-food_quantity="' . htmlspecialchars($report['food_quantity']) . '" data-details="' . htmlspecialchars($report['details']) . '" data-toggle="modal" data-target="#editReportModal">Modifier</a>';
                echo '<a href="javascript:void(0);" class="btn btn-danger btn-sm btn-delete" data-id="' . htmlspecialchars($report['id']) . '" data-csrf-token="' . htmlspecialchars($_SESSION['csrf_token']) . '">Supprimer</a>';
                echo '</div>';
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

            $vet_id = $_SESSION['user']['id'];
            $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
            $visit_date = filter_input(INPUT_POST, 'visit_date', FILTER_SANITIZE_STRING);
            $health_status = htmlspecialchars($_POST['health_status']);
            $food_given = htmlspecialchars($_POST['food_given']);
            $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
            $details = htmlspecialchars($_POST['details']);

            $reportController->addReport($vet_id, $animal_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
            echo "Rapport ajouté avec succès.";
            exit;
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
            $vet_id = $_SESSION['user']['id'];
            $visit_date = filter_input(INPUT_POST, 'visit_date', FILTER_SANITIZE_STRING);
            $health_status = htmlspecialchars($_POST['health_status']);
            $food_given = htmlspecialchars($_POST['food_given']);
            $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
            $details = htmlspecialchars($_POST['details']);

            $reportController->updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
            echo "Rapport modifié avec succès.";
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
            $reportController->deleteReport($id);
            echo "Rapport supprimé avec succès.";
            exit;
        }
    }
}

$scriptRepository = new \Repositories\ScriptRepository;
$script = $scriptRepository->vetReportScript();

include_once '../../../../src/views/templates/header.php';
include_once '../navbar_vet.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Gérer les Rapports des Animaux</h1>
    <hr>
    <br>
    <!-- Bouton pour Ajouter un Rapport -->
    <a href="#" class="btn btn-success mb-4" data-toggle="modal" data-target="#addReportModal">Ajouter un Rapport</a>
    <form id="filterForm" method="GET">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="filterDate">Date de Visite:</label>
                <select class="form-control" id="filterDate" name="visit_date">
                    <option value="">Toutes les dates</option>
                    <?php foreach ($distinctVisitDates as $date): ?>
                        <option value="<?= htmlspecialchars($date['visit_date']) ?>"><?= htmlspecialchars($date['visit_date']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="filterAnimal">Animal:</label>
                <select class="form-control" id="filterAnimal" name="animal_id">
                    <option value="">Tous les animaux</option>
                    <?php foreach ($animals as $animal): ?>
                        <option value="<?= htmlspecialchars($animal['id']) ?>"><?= htmlspecialchars($animal['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-success" id="filterButton">Filtrer</button>
    </form>
    <br>
    <div id="reportContainer">
        <!-- Les rapports seront chargés ici via AJAX -->
    </div>
</div>

<!-- Modal pour Ajouter un Rapport -->
<div class="modal fade" id="addReportModal" tabindex="-1" role="dialog" aria-labelledby="addReportModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="addReportModalLabel">Ajouter un Rapport</h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <form id="addReportForm">
                      <input type="hidden" name="action" value="add">
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
                  <div id="addResponseMessage"></div>
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
                  <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">
                  <form id="editReportForm">
                      <input type="hidden" name="action" value="edit">
                      <input type="hidden" id="editReportId" name="id">
                      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                      <div class="form-group">
                          <label for="editAnimal">Animal:</label>
                          <select class="form-control" id="editAnimal" name="animal_id" required>
                              <?php foreach ($animals as $animal): ?>
                                  <option value="<?= htmlspecialchars($animal['id']) ?>"><?= htmlspecialchars($animal['name']) ?></option>
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

<!-- Inclusion des scripts JavaScript nécessaires -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<?php echo $script ?>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
