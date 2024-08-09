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

$options = [
    'dates' => $reportController->getDistinctDates(),
    'animals' => $reportController->getAllAnimals()
];

header('Content-Type: application/json');
echo json_encode($options);
