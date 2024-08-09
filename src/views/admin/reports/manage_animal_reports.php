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
use Repositories\ReportRepository;
use Services\ReportService;
use Controllers\ReportController;

$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$reportRepository = new ReportRepository($conn);
$reportService = new ReportService($reportRepository);
$reportController = new ReportController($reportService);

include '../../../../src/views/templates/header.php';
include '../navbar_admin.php';
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
    <h1 class="my-4">Gérer les Rapports Vétérinaires</h1>
    <hr>
    <br>
    <form id="filterForm">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="filterDate">Date de Visite:</label>
                <select class="form-control" id="filterDate" name="visit_date">
                    <option value="">Toutes les dates</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="filterAnimal">Animal:</label>
                <select class="form-control" id="filterAnimal" name="animal_id">
                    <option value="">Tous les animaux</option>
                </select>
            </div>
        </div>
        <button type="button" class="btn btn-success" id="filterButton">Filtrer</button>
    </form>
    <br>

    <div class="table-responsive" id="reportsTable">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Animal</th>
                    <th>État de Santé</th>
                    <th>Nourriture Donnée</th>
                    <th>Grammage</th>
                    <th>Date de Passage</th>
                    <th>Détails</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reportsBody">
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.getElementById('filterButton');
    const filterDate = document.getElementById('filterDate');
    const filterAnimal = document.getElementById('filterAnimal');
    const reportsBody = document.getElementById('reportsBody');

    function loadOptions() {
        axios.get('get_options.php')
            .then(response => {
                const dates = response.data.dates;
                const animals = response.data.animals;

                dates.forEach(date => {
                    const option = document.createElement('option');
                    option.value = date.visit_date;
                    option.textContent = date.visit_date;
                    filterDate.appendChild(option);
                });

                animals.forEach(animal => {
                    const option = document.createElement('option');
                    option.value = animal.id;
                    option.textContent = animal.name;
                    filterAnimal.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des options:', error);
            });
    }

    function loadReports() {
        const visitDate = filterDate.value;
        const animalId = filterAnimal.value;

        axios.get('get_reports.php', {
            params: {
                visit_date: visitDate,
                animal_id: animalId
            }
        })
        .then(response => {
            reportsBody.innerHTML = '';
            const reports = response.data;
            reports.forEach(report => {
                const row = `<tr>
                    <td>${report.animal_name}</td>
                    <td>${report.health_status}</td>
                    <td>${report.food_given}</td>
                    <td>${report.food_quantity}</td>
                    <td>${report.visit_date}</td>
                    <td>${report.details}</td>
                    <td>
                        <a href="delete_vet_report.php?id=${report.id}" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">Supprimer</a>
                    </td>
                </tr>`;
                reportsBody.innerHTML += row;
            });
        })
        .catch(error => {
            console.error('Erreur lors du chargement des rapports:', error);
        });
    }

    filterButton.addEventListener('click', loadReports);
    loadOptions();
    loadReports();
    setInterval(loadReports, 30000);
});
</script>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
