<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

$db = new Database();
$conn = $db->connect();

$habitatManager = new Habitat($conn);
$habitats = $habitatManager->getToutHabitats();

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

<div class="container mt-4">
    <br>
    <hr>
    <h1 class="my-4">Habitats</h1>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($habitats as $habitat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($habitat['id']); ?></td>
                        <td><?php echo htmlspecialchars($habitat['name']); ?></td>
                        <td><?php echo htmlspecialchars($habitat['description']); ?></td>
                        <td><img src="../uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>" width="250"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../templates/footerconnected.php'; ?>
