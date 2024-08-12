<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Vérification de la session utilisateur et du rôle
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../../public/login.php');
    exit;
}

// Déconnexion automatique après inactivité
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: ../../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Génération du token CSRF s'il n'existe pas déjà
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

// Connexion à la base de données
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$habitatRepository = new HabitatRepository($conn);
$habitatService = new HabitatService($habitatRepository);
$habitatController = new HabitatController($habitatService);

// Gestion des commentaires soumis via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Validation et nettoyage des entrées
    $habitatId = filter_input(INPUT_POST, 'habitat_id', FILTER_VALIDATE_INT);
    $comment = trim($_POST['comment']);
    $vetId = $_SESSION['user']['id'];

    if ($habitatId && $comment) {
        try {
            $habitatController->submitHabitatComment($habitatId, $vetId, $comment);
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        } catch (Exception $e) {
            $error = "Erreur lors de la soumission du commentaire : " . $e->getMessage();
        }
    } else {
        $error = "Données invalides. Veuillez réessayer.";
    }
}

// Récupération des habitats
$habitats = $habitatController->getAllHabitats();

include '../../../../src/views/templates/header.php';
include '../navbar_vet.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}
body {
    background-image: url('../../../../assets/image/background.jpg');
}
.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Habitats</h1>
    <hr>
    <br>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Commentaires</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($habitats as $habitat): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($habitat['id']); ?></td>
                        <td><?php echo htmlspecialchars($habitat['name']); ?></td>
                        <td><?php echo htmlspecialchars($habitat['description']); ?></td>
                        <td><img src="../../../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>" width="250"></td>
                        <td>
                            <form action="" method="post">
                                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                <input type="hidden" name="habitat_id" value="<?php echo htmlspecialchars($habitat['id']); ?>">
                                <textarea name="comment" required></textarea>
                                <button type="submit" class="btn btn-success">Soumettre un commentaire</button>
                            </form>
                            <?php
                            $comments = $habitatController->getApprovedComments($habitat['id']);
                            foreach ($comments as $comment) {
                                echo "<div class=\"alert alert-success\">" . htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') . "</div>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
