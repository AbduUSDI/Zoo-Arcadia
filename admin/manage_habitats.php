<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

$db = new Database();
$conn = $db->connect();

$habitat = new Habitat($conn);
$habitats = $habitat->getToutHabitats();

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

<div class="container mt-4">
    <br>
    <hr>
    <h1 class="my-4">Gestion des Habitats</h1>
    <hr>
    <br>
    <a href="add_habitat.php" class="btn btn-success mb-4">Ajouter un habitat</a>
<div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Image</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($habitats as $habitat) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($habitat['id']); ?></td>
                    <td><?php echo htmlspecialchars($habitat['name']); ?></td>
                    <td><?php if (!empty($habitat['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="Image de l'habitat" style="width: 100px;">
                        <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars($habitat['description']); ?></td>
                    <td>
                        <a href="edit_habitat.php?id=<?php echo $habitat['id']; ?>" class="btn btn-warning">Modifier</a>
                        <a href="delete_habitat.php?id=<?php echo $habitat['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet habitat ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</div>
<?php include '../templates/footerconnected.php'; ?>
