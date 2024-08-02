<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
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
require_once '../../models/HabitatModel.php';

$db = new Database();
$conn = $db->connect();

$habitatObj = new Habitat($conn);

if (!isset($_GET['id'])) {
    header('Location: manage_habitats.php');
    exit;
}

$id = $_GET['id'];

$habitat = $habitatObj->getParId($id);

if (!$habitat) {
    header('Location: manage_habitats.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    if ($image['size'] > 0) {
        $imagee = $habitatObj->uploadImage($image);
    } else {
        $image = $habitat['image'];
    }

    $habitatObj->updateHabitat($id, $name, $description, $image);

    header('Location: manage_habitats.php');
    exit;
}

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
    <h1 class="my-4">Modifier un Habitat</h1>
    <hr>
    <br>
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
                <img src="../../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>" width="500" class="img-fluid">
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
        <hr>
    </form>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
