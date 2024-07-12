<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

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
