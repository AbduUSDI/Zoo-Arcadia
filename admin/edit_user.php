<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Vérification si l'identifiant de l'utilisateur à modifier est spécifié

if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

// Récupération de l'identifiant dans la variable $user_id de la page manage_users.php
$user_id = $_GET['id'];

// Connexion à la base de données
$database = new Database();
$db = $database->connect();

// Instance User pour pouvoir utiliser les méthodes nécessaire à modifier un utilisateur

$userManager = new User($db);

// Utilisation de la méthode "getUtilisateurParId" pour récupérer les infos user par son id

$user = $userManager->getUtilisateurParId($user_id);

// Ici si user est false alors l'utilisateur est redirigé vers manage_users.php

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

// Vérification si le formulaire de modification (POST) a été soumis, si oui, il récupère les infos modifier pour les modifer grâce à la méthode préparée "updateUser"

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];

    // Utilisation de la fonction updateUser pour mettre à jour l'utilisateur avec les nouvelles informations
    $userManager->updateUser($user_id, $email, $role_id, $username, $password);

    // Rediriger vers la page de gestion des utilisateurs après la modification
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
<!-- Conteneur pour afficher le formulaire de modification (POST) -->

<div class="container mt-4">
    <h1 class="my-4">Modifier Utilisateur</h1>
    <form action="edit_user.php?id=<?php echo $user['id']; ?>" method="POST">
        <div class="form-group">
            <label for="username">Nom d'utilisateur</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
    <label for="password">Mot de passe (laisser vide pour ne pas changer)</label>
    <div class="input-group">
        <input type="password" class="form-control" id="password" name="password">
        <div class="input-group-append">

<!-- Utilisation ici de togglePassword en tant que afficheur/désafficheur et en mettant une icone d'oeil FontAwesome (qui est inclut dans le header) -->

            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="fa fa-eye" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
        <div class="form-group">
            <label for="role_id">Rôle</label>
            <select class="form-control" id="role_id" name="role_id" required>
                <option value="1" <?php if ($user['role_id'] == 1) echo 'selected'; ?>>Administrateur</option>
                <option value="2" <?php if ($user['role_id'] == 2) echo 'selected'; ?>>Employé</option>
                <option value="3" <?php if ($user['role_id'] == 3) echo 'selected'; ?>>Vétérinaire</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>
<script>

   // Exécute le script une fois que le DOM est entièrement chargé

document.addEventListener('DOMContentLoaded', function() {

    // Obtenir les éléments par leur ID
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Ajouter un écouteur d'événements pour le clic sur l'icône

    togglePassword.addEventListener('click', function() {
        // Modifie le type de l'input entre 'password' et 'text' quand on clic sur l'icône
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Chargement de l'icône FontAwesome à l'intérieur de l'élément cliqué grâce à une balise nommée "i"
        const eyeIcon = this.querySelector('i');
        
        // Modifications des classes FontAwesome pour l'icône de l'œil (barré/non barré)
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

</script>
<?php include '../templates/footerconnected.php'; ?>
