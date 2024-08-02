<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../../public/login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {

    session_unset();  
    session_destroy(); 
    header('Location: ../../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

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