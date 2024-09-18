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

// Protection CSRF : Génération d'un token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }
    
    foreach ($_POST['hours'] as $id => $times) {
        // Validation des données entrées par l'utilisateur
        $openTime = filter_var($times['open'], FILTER_SANITIZE_STRING);
        $closeTime = filter_var($times['close'], FILTER_SANITIZE_STRING);
        $closed = isset($times['closed']) ? 1 : 0;
        
        // Met à jour les heures du zoo
        $zooHoursController->updateHours($openTime, $closeTime, $closed, (int)$id);
    }
    header("Location: zoo_hours.php");
    exit;
}

$scriptRepository = new Repositories\ScriptRepository;
$script = $scriptRepository->zooHoursScript();

include '../../../../src/views/templates/header.php';
include '../navbar_admin.php';
?>

<div class="zoo-hours-container mt-5 container">
    <h2 class="zoo-hours-title">Modifier les horaires d'ouverture du Zoo</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class="zoo-hours-table-wrapper">
            <table class="zoo-hours-table">
                <thead class="zoo-hours-table-header">
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
                                <input type="time" name="hours[<?php echo $hour['id']; ?>][open]" value="<?php echo substr($hour['open_time'], 0, 5); ?>" class="zoo-hours-input-time">
                                -
                                <input type="time" name="hours[<?php echo $hour['id']; ?>][close]" value="<?php echo substr($hour['close_time'], 0, 5); ?>" class="zoo-hours-input-time">
                            <?php endif; ?>
                            <input type="checkbox" name="hours[<?php echo $hour['id']; ?>][closed]" value="1" <?php echo $hour['closed'] ? 'checked' : ''; ?> class="zoo-hours-checkbox">
                            <label for="hours[<?php echo $hour['id']; ?>][closed]" class="zoo-hours-label">Fermé</label>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" class="zoo-hours-update-button">Mettre à jour les horaires</button>
    </form>
</div>

<?php
echo $script;
?>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
