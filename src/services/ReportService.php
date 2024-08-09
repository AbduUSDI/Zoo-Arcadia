<?php
// Services/ReportService.php
namespace Services;

use Interfaces\ReportRepositoryInterface;
use Interfaces\ReportServiceInterface;

class ReportService implements ReportServiceInterface {
    private $reportRepository;

    public function __construct(ReportRepositoryInterface $reportRepository) {
        $this->reportRepository = $reportRepository;
    }

    public function getReports($visitDate = null, $animalId = null) {
        return $this->reportRepository->getReports($visitDate, $animalId);
    }

    public function getDistinctDates() {
        return $this->reportRepository->getDistinctDates();
    }

    public function getAllAnimals() {
        return $this->reportRepository->getAllAnimals();
    }

    public function deleteReport($id) {
        return $this->reportRepository->deleteReport($id);
    }
    public function addReport($vet_id, $animal_id,  $visit_date, $health_status, $food_given, $food_quantity, $details) {
        return $this->reportRepository->ajouterRapports($vet_id, $animal_id,  $visit_date, $health_status, $food_given, $food_quantity, $details);
    }    
    public function updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details) {
        return $this->reportRepository->updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
    }
    public function getReportById($id) {
        return $this->reportRepository->getReportById($id);
    }
}
