<?php
namespace Interfaces;

interface UserRepositoryInterface {
    public function getAllUsers();
    public function getUserByEmail($email);
    public function getUserById($id);
    public function addUser($email, $password, $role_id, $username);
    public function updateUser($id, $email, $role_id, $username, $password = null);
    public function deleteUser($id);
}
