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

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ZooHoursRepository;
use Services\ZooHoursService;
use Controllers\ZooHoursController;

// Connexion à la base de données
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

// Initialisation du repository, service et contrôleur
$zooHoursRepository = new ZooHoursRepository($conn);
$zooHoursService = new ZooHoursService($zooHoursRepository);
$zooHoursController = new ZooHoursController($zooHoursService);

// Récupération des horaires
$hours = $zooHoursController->getAllHours();

// Gestion de la mise à jour des horaires via le formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['hours'] as $id => $times) {
        // Si le checkbox "Fermé" est coché, on passe la valeur 1, sinon 0
        $closed = isset($times['closed']) ? 1 : 0;
        $zooHoursController->updateHours($times['open'], $times['close'], $closed, $id);
    }
    header("Location: zoo_hours.php");
    exit;
}

include '../../../../src/views/templates/header.php';
include '../navbar_admin.php';
?>
<style>
h1, h2, h3 {
    text-align: center;
}
body {
    background-image: url('../../../../assets/image/background.jpg');
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
                        <th>Fermé</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($hours as $hour): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hour['day']); ?></td>
                        <td>
                            <?php if ($hour['closed']): ?>
                                Fermé
                                <input type="hidden" name="hours[<?php echo $hour['id']; ?>][open]" value="00:00">
                                <input type="hidden" name="hours[<?php echo $hour['id']; ?>][close]" value="00:00">
                            <?php else: ?>
                                <input type="time" name="hours[<?php echo $hour['id']; ?>][open]" value="<?php echo substr($hour['open_time'], 0, 5); ?>">
                                -
                                <input type="time" name="hours[<?php echo $hour['id']; ?>][close]" value="<?php echo substr($hour['close_time'], 0, 5); ?>">
                            <?php endif; ?>
                            <input type="checkbox" name="hours[<?php echo $hour['id']; ?>][closed]" value="1" <?php echo $hour['closed'] ? 'checked' : ''; ?>>
                            <label for="hours[<?php echo $hour['id']; ?>][closed]">Fermé</label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-success">Mettre à jour les horaires</button>
    </form>
</div>

<script>
function toggleClosed(checkbox, id) {
    const openInput = document.querySelector(`input[name="hours[${id}][open]"]`);
    const closeInput = document.querySelector(`input[name="hours[${id}][close]"]`);
    
    if (checkbox.checked) {
        openInput.disabled = true;
        closeInput.disabled = true;
    } else {
        openInput.disabled = false;
        closeInput.disabled = false;
    }
}
</script>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
