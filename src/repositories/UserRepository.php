<?php
namespace Repositories;

use PDO;
use PDOException;
use Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllUsers() {
        $stmt = $this->db->prepare("SELECT * FROM users");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserById($id) {
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
    // Méthodes pour la réinitialisation de mot de passe
    public function createPasswordResetToken($userId, $token) {
        $stmt = $this->db->prepare("INSERT INTO password_resets (user_id, token, created_at) VALUES (:user_id, :token, NOW())");
        $stmt->execute([
            'user_id' => $userId,
            'token' => $token
        ]);
    }

    public function getUserIdByPasswordResetToken($token) {
        $stmt = $this->db->prepare("SELECT user_id FROM password_resets WHERE token = :token AND created_at >= NOW() - INTERVAL 1 HOUR");
        $stmt->execute(['token' => $token]);
        return $stmt->fetchColumn();
    }

    public function updatePassword($id, $newPassword) {
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->execute([
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
            'id' => $id
        ]);
    }

    public function deletePasswordResetToken($token) {
        $stmt = $this->db->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
    }
}
