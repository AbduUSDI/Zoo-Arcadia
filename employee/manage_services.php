<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

$db = new Database();
$conn = $db->connect();

$services = new Service($conn);
$services = $services->getServices();

include '../templates/header.php';
include 'navbar_employee.php';
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

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <div class="table-responsive">
    <br>
    <hr>
    <h1 class="my-4">Gérer les services</h1>
    <hr>
    <br>
    <a href="add_service.php" class="btn btn-success mb-4">Ajouter un service</a>
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                    <td>
                        <?php if (!empty($service['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Image du Service" style="width: 100px;">
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_service.php?id=<?php echo $service['id']; ?>" class="btn btn-warning">Modifier</a>                        
                        <a href="delete_service.php?id=<?php echo $service['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sr de vouloir supprimer ce service ?');">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
    </div>
</div>

<?php include '../templates/footerconnected.php'; ?>
