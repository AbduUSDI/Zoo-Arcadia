<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../public/login.php');
    exit;
}

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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($id) {
    $reportController->deleteReport($id);
    header('Location: manage_animal_reports.php');
    exit;
} else {
    echo 'Erreur lors de la suppression du rapport.';
}
