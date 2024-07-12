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

// Instance Habitat pour utiliser les méthodes en rapport avec les habitats

$habitat = new Habitat($db);

// Traitement et récupération des données du formulaire (POST) d'ajout d'habitat

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // Ajout d'une image pour l'habitat

    if ($image['error'] == UPLOAD_ERR_OK) {
        $imageName = time() . '_' . $image['name'];
        move_uploaded_file($image['tmp_name'], '../uploads/' . $imageName);
    } else {
        $imageName = null;
    }

    // Utilisation de la méthode préparée "addHabitat" pour finaliser le formulaire et valider les informations

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
<!-- Conteneur pour afficher le formulaire d'ajout d'unn habitat -->

<div class="container mt-4">
    <h1 class="my-4">Ajouter un Habitat</h1>
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
