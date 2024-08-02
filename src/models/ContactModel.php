<?php
class Contact {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }
    public function saveMessage($name, $email, $message) {
        $stmt = $this->conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (:name, :email, :message)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':message', $message, PDO::PARAM_STR);
        $stmt->execute();
    }
}