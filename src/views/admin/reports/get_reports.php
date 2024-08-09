<?php
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

$visit_date = $_GET['visit_date'] ?? null;
$animal_id = $_GET['animal_id'] ?? null;

$reports = $reportController->getReports($visit_date, $animal_id);

header('Content-Type: application/json');
echo json_encode($reports);
