<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../../config/Database.php';
require_once '../../models/ZooHoursModel.php';

$db = new Database();
$conn = $db->connect();

$zooHours = new ZooHours($conn);
$hours = $zooHours->getAllHours();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['hours'] as $id => $times) {
        $zooHours->updateHours($times['open'], $times['close'], $id);
    }
    header("Location: zoo_hours.php");
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
    <h2>Modifier les horaires d'ouverture du Zoo</h2>
    <hr>
    <br>
    <form method="POST">
    <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Jour</th>
                        <th>Heures d'ouverture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hours as $hour): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hour['day']); ?></td>
                        <td>
                            <input type="time" name="hours[<?php echo $hour['id']; ?>][open]" value="<?php echo substr($hour['open_time'], 0, 5); ?>">
                            -
                            <input type="time" name="hours[<?php echo $hour['id']; ?>][close]" value="<?php echo substr($hour['close_time'], 0, 5); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour les horaires</button>
    </form>
</div>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
