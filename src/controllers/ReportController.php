<?php
// Controllers/ReportController.php
namespace Controllers;

use Interfaces\ReportServiceInterface;

class ReportController {
    private $reportService;

    public function __construct(ReportServiceInterface $reportService) {
        $this->reportService = $reportService;
    }

    public function getReports($visitDate = null, $animalId = null) {
        return $this->reportService->getReports($visitDate, $animalId);
    }

    public function getDistinctDates() {
        return $this->reportService->getDistinctDates();
    }

    public function getAllAnimals() {
        return $this->reportService->getAllAnimals();
    }

    public function deleteReport($id) {
        return $this->reportService->deleteReport($id);
    }
    public function addReport($vet_id, $animal_id,  $visit_date, $health_status, $food_given, $food_quantity, $details) {
        return $this->reportService->addReport($vet_id, $animal_id,  $visit_date, $health_status, $food_given, $food_quantity, $details);
    }    
    public function updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details) {
        return $this->reportService->updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
    }
    public function getReportById($id) {
        return $this->reportService->getReportById($id);
    }
}
