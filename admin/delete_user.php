<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Vérifier si l'ID de l'utilisateur à supprimer est présent dans la requête

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: manage_users.php');
    exit();
}

// Vérifie si l'ID de l'utilisateur à supprimer est présent dans la requête et valide

$userId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if ($userId === false) {
    header('Location: manage_users.php');
    exit();
}

// Connexion à la base de données
$db = (new Database())->connect();

// Instance User ici pour utiliser toutes le méthodes en rapport avec les utilisateurs

$user = new User($db);

try {
    // Supprimer l'utilisateur de la base de données
    $user->deleteUser($userId);

    // Rediriger vers la page des utilisateurs avec un message de succès
    $_SESSION['message'] = "Utilisateur supprimé avec succès.";
    header('Location: manage_users.php');
    exit();
} catch (PDOException $erreur) {
    // Rediriger vers la page des utilisateurs avec un message d'erreur
    $_SESSION['error'] = "Erreur lors de la suppression de l'utilisateur : " . $erreur->getMessage();
    header('Location: manage_users.php');
    exit();
}