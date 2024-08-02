<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';

if (!isset($_GET['id'])) {
    header('Location: manage_users.php');
    exit;
}

$user_id = $_GET['id'];

$database = new Database();
$db = $database->connect();

$userManager = new User($db);
$user = $userManager->getUtilisateurParId($user_id);

if (!$user) {
    header('Location: manage_users.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $role_id = $_POST['role_id'];

    // Utilisation de la fonction updateUser pour mettre à jour l'utilisateur avec les nouvelles informations
    $userManager->updateUser($user_id, $email, $role_id, $username, $password);

    header('Location: manage_users.php');
    exit;
}

include_once '../../../src/views/templates/header.php';
include_once '../navbar_admin.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/background.jpg');
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Modifier Utilisateur</h1>
    <hr>
    <br>
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
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        const eyeIcon = this.querySelector('i');
        
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
<?php include '../../../src/views/templates/footerconnected.php'; ?>
