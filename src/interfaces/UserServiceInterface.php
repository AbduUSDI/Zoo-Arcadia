<?php
namespace Interfaces;

interface UserServiceInterface {
    public function getAllUsers();
    public function getUserByEmail($email);
    public function getUserById($id);
    public function addUser($email, $password, $role_id, $username);
    public function updateUser($id, $email, $role_id, $username, $password = null);
    public function deleteUser($id);
    // Méthodes pour la réinitialisation de mot de passe
    public function initiatePasswordReset($email);
    public function verifyPasswordResetToken($token);
    public function resetPassword($token, $newPassword);
    public function createPasswordResetToken($userId, $token);
    
}
