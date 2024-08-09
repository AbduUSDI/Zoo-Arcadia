<?php
namespace Controllers;

use Interfaces\ServiceServiceInterface;

class ServiceController {
    private $serviceService;

    public function __construct(ServiceServiceInterface $serviceService) {
        $this->serviceService = $serviceService;
    }

    public function getServices() {
        return $this->serviceService->getServices();
    }

    public function addService($name, $description, $image) {
        return $this->serviceService->addService($name, $description, $image);
    }

    public function updateServiceWithoutImage($id, $name, $description) {
        return $this->serviceService->updateServiceWithoutImage($id, $name, $description);
    }

    public function updateServiceWithImage($id, $name, $description, $imageName) {
        return $this->serviceService->updateServiceWithImage($id, $name, $description, $imageName);
    }

    public function deleteService($id) {
        return $this->serviceService->deleteService($id);
    }

    public function getServiceById($id) {
        return $this->serviceService->getServiceById($id);
    }

    public function addServiceImage($file) {
        return $this->serviceService->addImage($file);
    }
}
