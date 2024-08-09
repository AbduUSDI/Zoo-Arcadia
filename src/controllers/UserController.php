<?php
namespace Controllers;

use Interfaces\UserServiceInterface;

class UserController {
    private $userService;

    public function __construct(UserServiceInterface $userService) {
        $this->userService = $userService;
    }

    public function getAllUsers() {
        return $this->userService->getAllUsers();
    }

    public function getUserByEmail($email) {
        return $this->userService->getUserByEmail($email);
    }

    public function getUserById($id) {
        return $this->userService->getUserById($id);
    }

    public function addUser($email, $password, $role_id, $username) {
        return $this->userService->addUser($email, $password, $role_id, $username);
    }

    public function updateUser($id, $email, $role_id, $username, $password = null) {
        return $this->userService->updateUser($id, $email, $role_id, $username, $password);
    }

    public function deleteUser($id) {
        return $this->userService->deleteUser($id);
    }
}
