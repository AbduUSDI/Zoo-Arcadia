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

// Instance Service pour utiliser les méthodes en rapport avec les Services

$service = new Service($db);

// Vérification si un ID de service est fourni dans l'URL, sinon redirection vers page manage_services.php

if (!isset($_GET['id'])) {
    header('Location: manage_services.php');
    exit;
}

// Récupèration de l'ID du service depuis les paramètres de l'URL

$id = $_GET['id'];

// Traitement du formulaire (POST) en utilisant les méthode préparées de la classe Service

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    // Etablissement du dossier cible pour importer le fichier image ici c'est dans "uploads"

    if ($image['size'] > 0) {
        $imageName = basename($image['name']);
        $targetFile = '../uploads/' . $imageName;
        move_uploaded_file($image['tmp_name'], $targetFile);

        // Utilisation de la méthode "updateServiceAvecImage" pour une modification avec une image modifée

        $service->updateServiceAvecImage($id, $name, $description, $imageName);
    } else {

        // Utilisation de la méthode "updateServiceSansImage" pour une modification sans image

        $service->updateServiceSansImage($id, $name, $description);
    }

    header('Location: manage_services.php');
    exit;
}

// Utilisation de la méthode préparée "getServiceById" pour récupérer les infos du service par son id

$serviceData = $service->getServiceById($id);

// Si serviceDate est false alors, l'utilisateur est redirigé vers manage_services.php

if (!$serviceData) {
    header('Location: manage_services.php');
    exit;
}

include '../templates/header.php';
include 'navbar_admin.php';
?>

<!-- Conteneur pour afficher le formulaire (POST) de modification d'un service  -->

<div class="container">
    <h1 class="my-4">Modifier le Service</h1>
    <form action="edit_service.php?id=<?php echo $id; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($serviceData['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea class="form-control" id="description" name="description" rows="4" required><?php echo htmlspecialchars($serviceData['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="image">Image (laisser vide pour ne pas changer)</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <?php if (!empty($serviceData['image'])): ?>
            <div class="form-group">
                <img src="../uploads/<?php echo htmlspecialchars($serviceData['image']); ?>" alt="Image actuelle" style="width: 100px;">
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
