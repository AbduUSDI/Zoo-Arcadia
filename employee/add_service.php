<?php

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employee, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = (new Database())->connect();

// Instance Service pour utiliser la méthode préparée en rapport avec les services 

$service = new Service($db);

// Traitement et récupération des données du formulaire (POST) d'ajout de service

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    try {
        $image = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {

            // Utilisation de la méthode préparée "ajouterImage" afin de pouvoir ajouter l'image du service

            $image = $service->ajouterImage($_FILES['image']);
        }

        // Utilisation de la méthode préparée "ajouterService" pour finaliser le formulaire et envoyer tout sur la BDD afin d'ajouter le service

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

<!-- Conteneur pour afficher le formulaire (POST) pour ajouter un service -->

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
