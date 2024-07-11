<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = (new Database())->connect();

// Instance User pour utiliser les méthodes en rapport au utilisateurs du zoo

$user = new User($db);

// Traitement et récupération des données du formulaire (POST) d'ajout d'utilisateur

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];

    // Hachage du mot de passe pour plus de sécurité

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role_id = $_POST['role_id'];
    $username = $_POST['username'];

    // Utilisation de la méthode préparée "addUser" afin d'ajouter le nouvel utilisateur après avoir récupéré toutes les informations fournies

    $user->addUser($email, $password, $role_id, $username);

    // Redirection vers la page de gestion des utilisateurs

    header('Location: manage_users.php');
    exit;
}

include '../templates/header.php';
include 'navbar_admin.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../image/background.jpg');
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<!-- Conteneur pour afficher le formulaire (POST) pour ajouter un utilisateur -->

<div class="container mt-4">
    <h1 class="my-4">Ajouter un Utilisateur</h1>
    <form action="add_user.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role_id">Rôle</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="1">Admin</option>
                <option value="2">Employé</option>
                <option value="3">Vétérinaire</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
