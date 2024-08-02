<?php
class ZooHours {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getAllHours() {
        $stmt = $this->db->prepare("SELECT * FROM zoo_hours ORDER BY id ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function updateHours($open, $close, $id) {
        $stmt = $this->db->prepare("UPDATE zoo_hours SET open_time = ?, close_time = ? WHERE id = ?");
        $stmt->execute([$open, $close, $id]);
        return $stmt->rowCount();
    }
}