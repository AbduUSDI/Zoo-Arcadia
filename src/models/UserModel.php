<?php
class User {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }
    public function getAllUtilisateurs() {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUtilisateurParEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUtilisateurParId($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function addUser($email, $password, $role_id, $username) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        $stmt = $this->db->prepare("INSERT INTO users (email, password, role_id, username) VALUES (:email, :password, :role_id, :username)");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role_id', $role_id, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        return $this->db->lastInsertId();
    }
    public function updateUser($id, $email, $role_id, $username, $password = null) {
        $sql = "UPDATE users SET email = :email, role_id = :role_id, username = :username";
    
        $params = [
            ':id' => $id,
            ':email' => $email,
            ':role_id' => $role_id,
            ':username' => $username
        ];
    
        if (!empty($password)) {
            $sql .= ", password = :password";
            $params[':password'] = password_hash($password, PASSWORD_DEFAULT);
        }
    
        $sql .= " WHERE id = :id";
    
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
    
        try {
            $stmt->execute();
            return true;
        } catch (PDOException $erreur) {
            error_log("Erreur lors de la mise à jour de l'utilisateur : " . $erreur->getMessage());
            return false;
        }
    }
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
    }
}