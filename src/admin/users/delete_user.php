<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_users.php');
    exit();
}

$userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($userId === false) {
    header('Location: manage_users.php');
    exit();
}

$db = (new Database())->connect();

$user = new User($db);

try {
    $user->deleteUser($userId);

    $_SESSION['message'] = "Utilisateur supprimé avec succès.";
    header('Location: manage_users.php');
    exit();
} catch (PDOException $erreur) {
    $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur : " . $erreur->getMessage();
    header('Location: manage_users.php');
    exit();
}