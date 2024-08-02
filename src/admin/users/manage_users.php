<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/UserModel.php';

$db = new Database();
$conn = $db->connect();

$user = new User($conn);
$users = $user->getAllUtilisateurs();

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
    <h1 class="my-4">Gérer les utilisateurs</h1>
    <hr>
    <br>
    <div class="table-responsive">
        <a href="add_user.php" class="btn btn-success mb-4">Ajouter un utilisateur</a>
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_id'] == 1 ? 'Admin' : ($user['role_id'] == 2 ? 'Employé' : 'Vétérinaire')); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
