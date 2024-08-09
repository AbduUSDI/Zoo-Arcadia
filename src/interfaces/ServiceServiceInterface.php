<?php
namespace Interfaces;

interface ServiceServiceInterface {
    public function getServices();
    public function addService($name, $description, $image);
    public function updateServiceWithoutImage($id, $name, $description);
    public function updateServiceWithImage($id, $name, $description, $imageName);
    public function deleteService($id);
    public function getServiceById($id);
    public function addImage($file);
}
