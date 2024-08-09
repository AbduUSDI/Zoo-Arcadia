<?php
namespace Repositories;

use PDO;
use Interfaces\ZooHoursRepositoryInterface;

class ZooHoursRepository implements ZooHoursRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllHours() {
        $stmt = $this->db->prepare("SELECT * FROM zoo_hours ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateHours($open, $close, $closed, $id) {
        // Si le jour est marqué comme fermé, on stocke 00:00 pour les heures d'ouverture et de fermeture
        if ($closed) {
            $open = '00:00';
            $close = '00:00';
        }
    
        $stmt = $this->db->prepare("UPDATE zoo_hours SET open_time = ?, close_time = ?, closed = ? WHERE id = ?");
        $stmt->execute([$open, $close, $closed, $id]);
        return $stmt->rowCount();
    }
}
