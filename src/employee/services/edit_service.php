<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = (new Database())->connect();

$service = new Service($db);

if (!isset($_GET['id'])) {
    header('Location: manage_services.php');
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image = $_FILES['image'];

    if ($image['size'] > 0) {
        $imageName = basename($image['name']);
        $targetFile = '../../../assets/uploads/' . $imageName;
        move_uploaded_file($image['tmp_name'], $targetFile);

        $service->updateServiceAvecImage($id, $name, $description, $imageName);
    } else {
        $service->updateServiceSansImage($id, $name, $description);
    }

    header('Location: manage_services.php');
    exit;
}

$serviceData = $service->getServiceById($id);

if (!$serviceData) {
    header('Location: manage_services.php');
    exit;
}

include '../../../src/views/templates/header.php';
include '../navbar_employee.php';
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
    <h1 class="my-4">Modifier le Service</h1>
    <hr>
    <br>
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
                <img src="../../../assets/uploads/<?php echo htmlspecialchars($serviceData['image']); ?>" alt="Image actuelle" style="width: 100px;">
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-success">Mettre à jour</button>
    </form>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
