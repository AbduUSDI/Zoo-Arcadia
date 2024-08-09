<?php
namespace Services;

use Interfaces\ServiceRepositoryInterface;
use Interfaces\ServiceServiceInterface;

class ServiceService implements ServiceServiceInterface {
    private $serviceRepository;

    public function __construct(ServiceRepositoryInterface $serviceRepository) {
        $this->serviceRepository = $serviceRepository;
    }

    public function getServices() {
        return $this->serviceRepository->getServices();
    }

    public function addService($name, $description, $image) {
        return $this->serviceRepository->addService($name, $description, $image);
    }

    public function updateServiceWithoutImage($id, $name, $description) {
        return $this->serviceRepository->updateServiceWithoutImage($id, $name, $description);
    }

    public function updateServiceWithImage($id, $name, $description, $imageName) {
        return $this->serviceRepository->updateServiceWithImage($id, $name, $description, $imageName);
    }

    public function deleteService($id) {
        return $this->serviceRepository->deleteService($id);
    }

    public function getServiceById($id) {
        return $this->serviceRepository->getServiceById($id);
    }

    public function addImage($file) {
        return $this->serviceRepository->addImage($file);
    }
}
