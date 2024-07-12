<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Instance Habitat pour utiliser les méthodes en rapport avec un habitat

$habitatObj = new Habitat($conn);

// Vérification si un ID d'habitat est fourni dans l'URL, sinon redirige vers manage_habitats.php

if (!isset($_GET['id'])) {
    header('Location: manage_habitats.php');
    exit;
}

// Récupèration de l'ID de l'habitat depuis les paramètres de l'URL

$id = $_GET['id'];

// Utilisation de la méthode "getParId" pour récupérer les informations de l'habitat par son id

$habitat = $habitatObj->getParId($id);

// Vérifie si l'habitat existe, sinon redirection vers manage_habitats.php

if (!$habitat) {
    header('Location: manage_habitats.php');
    exit;
}

// Traitement du formulaire (POST) en utilisant les méthode préparées de la classe Habitat

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    if ($image['size'] > 0) {

        // Utilisation de la méthode préparée "uploadImage" pour pouvoir ajouter une image si il le faut sinon garder l'image existante

        $imagee = $habitatObj->uploadImage($image);
    } else {
        $image = $habitat['image'];
    }

        // Utilisation de la méthode préparée "updateHabitat" avec ses 4 paramètres

    $habitatObj->updateHabitat($id, $name, $description, $image);

    header('Location: manage_habitats.php');
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
<!-- Conteneur pour afficher me formulaire (POST) de modification d'Habitat -->

<div class="container mt-4">
    <h1 class="my-4">Modifier un Habitat</h1>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($habitat['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($habitat['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control-file" id="image" name="image">
            <?php if ($habitat['image']): ?>
                <img src="../uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>" width="100">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>

<?php include '../templates/footerconnected.php'; ?>
