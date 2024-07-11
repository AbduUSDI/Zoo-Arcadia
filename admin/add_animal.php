<?php

// Vérification de l'identification de l'utilisateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Instance Animal pour utiliser les méthodes en rapport avec les animaux

$animalManager = new Animal($conn);

// Traitement et récupération des données du formulaire (POST) d'ajout d'un animal

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $habitat_id = $_POST['habitat_id'];
    $image = $_FILES['image'];

    // Vérification si un fichier a été téléchargé

    if ($image['error'] == UPLOAD_ERR_OK) {
        $targetDir = "../uploads/";
        $imageName = time() . '_' . basename($image["name"]);
        $targetFile = $targetDir . $imageName;

        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Utilisation d'un allowedTypes pour forcer l'utilisateur à utiliser uniquement des fichiers image ou gif

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes) && $image['size'] < 5000000) {
            if (move_uploaded_file($image["tmp_name"], $targetFile)) {
            } else {
                $error = "Erreur lors du téléchargement de l'image.";
            }
        } else {
            $error = "Le fichier doit être une image (jpg, jpeg, png, gif) et ne doit pas dépasser 5MB.";
        }
    } else {
        $error = "Erreur de téléchargement de l'image.";
    }

    if (!isset($error)) {

        // Utilisation de la méthode "add" de la classe Animal pour ajouter un nouvel animal

        $animalManager->add([$name, $species, $habitat_id, $targetFile]);
        header('Location: manage_animals.php');
        exit;
    }
}

// Récupération de la liste des habitats pour le formulaire

$habitats = $animalManager->getAllHabitats();

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
<!-- Conteneur pour afficher le formulaire (POST) pour ajouter un nouvel animal -->

<div class="container mt-4">
    <h1 class="my-4">Ajouter un Animal</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="add_animal.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="species">Espèce</label>
            <input type="text" class="form-control" id="species" name="species" required>
        </div>
        <div class="form-group">
            <label for="habitat_id">Habitat</label>
            <select class="form-control" id="habitat_id" name="habitat_id" required>
                <?php foreach ($habitats as $habitat): ?>
                    <option value="<?php echo $habitat['id']; ?>"><?php echo htmlspecialchars($habitat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Image</label>
            <input type="file" class="form-control" id="image" name="image" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
