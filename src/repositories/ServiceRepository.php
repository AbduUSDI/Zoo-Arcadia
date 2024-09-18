<?php
namespace Repositories;

use PDO;
use Interfaces\ServiceRepositoryInterface;
use Exception;

class ServiceRepository implements ServiceRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getServices() {
        $stmt = $this->db->prepare("SELECT * FROM services");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addService($name, $description, $image) {
        $stmt = $this->db->prepare("INSERT INTO services (name, description, image) VALUES (:name, :description, :image)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        return $stmt->execute();
    }

    public function updateServiceWithoutImage($id, $name, $description) {
        $stmt = $this->db->prepare("UPDATE services SET name = :name, description = :description WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateServiceWithImage($id, $name, $description, $imageName) {
        $stmt = $this->db->prepare("UPDATE services SET name = :name, description = :description, image = :image WHERE id = :id");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':image', $imageName, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteService($id) {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function getServiceById($id) {
        $stmt = $this->db->prepare("SELECT * FROM services WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addImage($file) {
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'gif', 'png', 'jpeg', 'webp'];
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDirection = '../../../../assets/uploads/';
            $dest_path = "{$uploadFileDirection}{$fileName}";

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                return $fileName;
            } else {
                throw new Exception('Une erreur est survenue au moment de l\'enregistrement de l\'image. Assurez-vous que le dossier upload existe bien dans votre répertoire.');
            }
        } else {
            throw new Exception('Type de fichier incorrect, veuillez insérer le bon type de fichier : ' . implode(',', $allowedfileExtensions));
        }
    }
}
