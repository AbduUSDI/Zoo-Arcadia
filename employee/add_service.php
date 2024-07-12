<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

$db = (new Database())->connect();

$service = new Service($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    try {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $service->ajouterImage($_FILES['image']);
        }
        $service->ajouterService($name, $description, $image);
        header('Location: manage_services.php');
        exit;

    } catch (Exception $erreur) {
        $error = $erreur->getMessage();
    }
}

include '../templates/header.php';
include 'navbar_employee.php';
?>

<div class="container">
    <h1 class="my-4">Ajouter un Service</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="add_service.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nom du Service</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image du Service</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../templates/footerconnected.php'; ?>
