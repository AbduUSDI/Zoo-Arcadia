<?php
// Interfaces/ReportServiceInterface.php
namespace Interfaces;

interface ReportServiceInterface {
    public function getReports($visitDate = null, $animalId = null);
    public function getDistinctDates();
    public function getAllAnimals();
    public function deleteReport($id);
    public function addReport($vet_id, $animal_id,  $visit_date, $health_status, $food_given, $food_quantity, $details);
    public function updateReport($id, $animal_id, $vet_id, $visit_date, $health_status, $food_given, $food_quantity, $details);
    public function getReportById($id);
}
