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
}
