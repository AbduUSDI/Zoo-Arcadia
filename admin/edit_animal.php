<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Instance de la classe Animal pour utiliser les méthodes en rapport avec les animaux
$animalManager = new Animal($conn);

// Instance de la classe Habitat afin d'afficher les habitats dans le label comme sélection

$habitatsManager = new Habitat($conn);

// Vérification si l'id est affiché sur l'URL

$animal_id = $_GET['id'];

// Récupération des données du formulaire POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $habitat_id = $_POST['habitat_id'];
    $image = $_FILES['image'];

// Si une image est chargée alors l'image va dans le dossier /uploads en utilisant les méthodes "updateAvecImage" et "updateSansImage"

    if ($image['name']) {
        $dossier = "../uploads/";
        $imageName = time() . '_' . basename($image["name"]);
        $targetFile = $dossier . $imageName;
        $success = move_uploaded_file($image["tmp_name"], $targetFile);  // Utilisation de la méthode intégré de VSCode pour php afin de déplacer un fichier chargé vers un dossier existant (attention permissions d'écriture dans le dossier)
        // Méthode updateAvecImage de la classe Animal
        $animalManager->updateAvecImage([$name, $species, $habitat_id, $targetFile, $animal_id]);
    } else {
        // Méthode updateSansImage de la classe Animal
        $animalManager->updateSansImage([$name, $species, $habitat_id, $animal_id]);
    }

    header('Location: manage_animals.php');
    exit;
}

// Utilisez la méthode getDetailsAnimal de la classe Animal pour récupérer les détails de l'animal spécifique grâce à son id
$animal = $animalManager->getDetailsAnimal($animal_id);

// Définition de l'ID de l'habitat à l'animal actuel
$habitat_id = $animal['habitat_id'];

// Utilisation de la méthode "getParHabitats" de la classe Animal pour récupérer la liste des habitats dans le label Select

$habitatsparid= $animalManager->getParHabitat($habitat_id);

// Utilisation de la méthode "getToutHabitats" de la classe Habitat pour récupérer la liste des habitats dans le label Select

$habitats= $habitatsManager->getToutHabitats();

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
<!-- Conteneur pour afficher le formulaire (POST) -->

<div class="container mt-4">
    <h1 class="my-4">Modifier un Animal</h1>
    <form action="edit_animal.php?id=<?php echo $animal['id']; ?>" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($animal['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="species">Espèce</label>
            <input type="text" class="form-control" id="species" name="species" value="<?php echo htmlspecialchars($animal['species']); ?>" required>
        </div>
        <div class="form-group">
            <label for="habitat_id">Habitat</label>
            <select class="form-control" id="habitat_id" name="habitat_id" required>
                <?php foreach ($habitats as $habitat): ?>
                    <option value="<?php echo $habitat['id']; ?>" <?php if ($habitat['id'] == $animal['habitat_id']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($habitat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image">Image (laisser vide pour ne pas changer)</label>
            <input type="file" class="form-control" id="image" name="image">
        </div>
        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
    </form>
</div>

</div>

<?php include '../templates/footerconnected.php'; ?>
