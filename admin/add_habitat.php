<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

$db = (new Database())->connect();

$habitat = new Habitat($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    if ($image['error'] == UPLOAD_ERR_OK) {
        $imageName = time() . '_' . $image['name'];
        move_uploaded_file($image['tmp_name'], '../uploads/' . $imageName);
    } else {
        $imageName = null;
    }
    if ($habitat->addHabitat($name, $description, $imageName)) {
        header('Location: manage_habitats.php');
        exit;
    } else {
        $error = "Erreur lors de l'ajout de l'habitat.";
    }
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

<div class="container mt-4">
    <br>
    <hr>
    <h1 class="my-4">Ajouter un Habitat</h1>
    <hr>
    <br>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control-file" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../templates/footerconnected.php'; ?>
