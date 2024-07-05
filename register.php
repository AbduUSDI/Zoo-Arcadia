<?php
require_once 'functions.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['nom_utilisateur'];
    $email = $_POST['email'];
    $password = $_POST['mot_de_passe'];
    $role = $_POST['role_id'];

    if ($user->getUtilisateurParEmail($email)) {
        $error = "L'email est déjà utilisé.";
    } else {
        $result = $user->addUser($email, $password, $role, $username);
        if ($result) {
            $success = "Inscription réussie. Vous pouvez maintenant vous connecter.";
            header("Location: login.php?success=" . urlencode($success));
            exit();
        } else {
            $error = "Erreur lors de l'inscription. Veuillez réessayer.";
            header("Location: login.php?error=" . urlencode($error));
            exit();
        }
    }
}