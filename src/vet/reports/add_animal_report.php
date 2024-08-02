<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../public/login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {

    session_unset();  
    session_destroy(); 
    header('Location: ../../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../config/Database.php';
require_once '../../models/AnimalModel.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../public/login.php');
    exit;
}

$db = new Database();
$conn = $db->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $vet_id = $_SESSION['user']['id'];
    $health_status = htmlspecialchars($_POST['health_status']);
    $food_given = htmlspecialchars($_POST['food_given']);
    $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
    $visit_date = htmlspecialchars($_POST['visit_date']);
    $details = htmlspecialchars($_POST['details']);

    $animal = new Animal($conn);

    $animal->ajouterRapports($animal_id, $vet_id, $health_status, $food_given, $food_quantity, $visit_date, $details);

    header('Location: manage_animal_reports.php');
    exit;
}

$animal = new Animal($conn); 
$animals = $animal->getAll();

include '../../../src/views/templates/header.php';
include '../navbar_vet.php';
?>
<style>
h1, h2, h3 {
    text-align: center;
}
body {
    background-image: url('../../../assets/image/background.jpg');
}
.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Ajouter un Rapport Animal</h1>
    <hr>
    <br>
    <form action="add_animal_report.php" method="POST">
        <div class="form-group">
            <label for="animal_id">Animal</label>
            <select class="form-control" id="animal_id" name="animal_id" required>
                <?php foreach ($animals as $animal): ?>
                    <option value="<?php echo $animal['id']; ?>"><?php echo htmlspecialchars($animal['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="health_status">État</label>
            <input type="text" class="form-control" id="health_status" name="health_status" required>
        </div>
        <div class="form-group">
            <label for="food_given">Nourriture</label>
            <input type="text" class="form-control" id="food_given" name="food_given" required>
        </div>
        <div class="form-group">
            <label for="food_quantity">Grammage (en grammes)</label>
            <input type="number" class="form-control" id="food_quantity" name="food_quantity" required>
        </div>
        <div class="form-group">
            <label for="visit_date">Date de Passage</label>
            <input type="date" class="form-control" id="visit_date" name="visit_date" required>
        </div>
        <div class="form-group">
            <label for="details">Détails (facultatif)</label>
            <textarea class="form-control" id="details" name="details" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
