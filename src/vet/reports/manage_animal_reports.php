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

$db = new Database();
$conn = $db->connect();

$reportManager = new Animal($conn);
$reports = $reportManager->getReports();

include '../../../src/views/templates/header.php';
include '../navbar_vet.php';
?>
<style>
h1,h2,h3 {
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
    <h1 class="my-4">Gérer les rapports animaux</h1>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Animal</th>
                    <th>Date de Passage</th>
                    <th>État</th>
                    <th>Nourriture</th>
                    <th>Grammage (en grammes)</th>
                    <th>Détails</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['animal_name']); ?></td> 
                        <td><?php echo htmlspecialchars($report['visit_date']); ?></td>
                        <td><?php echo htmlspecialchars($report['health_status']); ?></td>
                        <td><?php echo htmlspecialchars($report['food_given']); ?></td>
                        <td><?php echo htmlspecialchars($report['food_quantity']); ?></td>
                        <td><?php echo $report['details']; ?></td>
                        <td>
                            <a href="delete_vet_report.php?id=<?php echo $report['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../src/views/templates/footerconnected.php'; ?>
