<?php
namespace Services;

use Interfaces\UserRepositoryInterface;
use Interfaces\UserServiceInterface;

class UserService implements UserServiceInterface {
    private $userRepository;

    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function getAllUsers() {
        return $this->userRepository->getAllUsers();
    }

    public function getUserByEmail($email) {
        return $this->userRepository->getUserByEmail($email);
    }

    public function getUserById($id) {
        return $this->userRepository->getUserById($id);
    }

    public function addUser($email, $password, $role_id, $username) {
        return $this->userRepository->addUser($email, $password, $role_id, $username);
    }

    public function updateUser($id, $email, $role_id, $username, $password = null) {
        return $this->userRepository->updateUser($id, $email, $role_id, $username, $password);
    }

    public function deleteUser($id) {
        return $this->userRepository->deleteUser($id);
    }
    // Méthodes pour la réinitialisation de mot de passe
    public function initiatePasswordReset($email) {
        $user = $this->userRepository->getUserByEmail($email);
        if ($user) {
            $token = bin2hex(random_bytes(16));
            $this->userRepository->createPasswordResetToken($user['id'], $token);
        }
    }

    public function verifyPasswordResetToken($token) {
        $userId = $this->userRepository->getUserIdByPasswordResetToken($token);
        return $userId ? $userId : false; // Renvoie l'ID de l'utilisateur ou false
    }
    

    public function resetPassword($token, $newPassword) {
        $userId = $this->userRepository->getUserIdByPasswordResetToken($token);
        if ($userId) {
            $this->userRepository->updatePassword($userId, $newPassword);
            $this->userRepository->deletePasswordResetToken($token);
            return true;
        }
        return false;
    }
    public function createPasswordResetToken($userId, $token) {
        $userId = $this->userRepository->createPasswordResetToken($userId, $token);
    }
}
