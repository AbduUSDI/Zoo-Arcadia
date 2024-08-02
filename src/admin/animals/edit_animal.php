<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/AnimalModel.php';
require_once '../../models/HabitatModel.php';

$db = new Database();
$conn = $db->connect();

$animalManager = new Animal($conn);
$habitatsManager = new Habitat($conn);

$animal_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $species = $_POST['species'];
    $habitat_id = $_POST['habitat_id'];
    $image = $_FILES['image'];

    if ($image['name']) {
        $dossier = "../../../assets/uploads/";
        $imageName = time() . '_' . basename($image["name"]);
        $targetFile = $dossier . $imageName;
        $success = move_uploaded_file($image["tmp_name"], $targetFile);

        $animalManager->updateAvecImage([$name, $species, $habitat_id, $targetFile, $animal_id]);
    } else {

        $animalManager->updateSansImage([$name, $species, $habitat_id, $animal_id]);
    }

    header('Location: manage_animals.php');
    exit;
}

$animal = $animalManager->getDetailsAnimal($animal_id);
$habitat_id = $animal['habitat_id'];
$habitatsparid= $animalManager->getAnimalParHabitat($habitat_id);
$habitats= $habitatsManager->getToutHabitats();

include '../../../src/views/templates/header.php';
include '../navbar_admin.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../../assets/image/background.jpg');
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Modifier un Animal</h1>
    <hr>
    <br>
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
        <hr>
    </form>
</div>

</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
