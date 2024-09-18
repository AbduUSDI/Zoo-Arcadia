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

<div class="habitat-container mt-5 container">
    <h1 class="habitat-title my-4">Habitats</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="habitat-cards">
        <?php foreach ($habitats as $habitat): ?>
            <div class="habitat-card">
                <div class="habitat-card-image">
                    <img src="../../../../assets/uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
                </div>
                <div class="habitat-card-body">
                    <h5 class="habitat-card-title"><?php echo htmlspecialchars($habitat['name']); ?></h5>
                    <p class="habitat-card-description"><?php echo htmlspecialchars_decode($habitat['description']); ?></p>
                    <form action="" method="post" class="habitat-comment-form">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="habitat_id" value="<?php echo htmlspecialchars($habitat['id']); ?>">
                        <textarea name="comment" class="habitat-comment-input" placeholder="Écrire un commentaire..." required></textarea>
                        <button type="submit" class="btn btn-success habitat-comment-button">Soumettre un commentaire</button>
                    </form>
                    <div class="habitat-comments">
                        <?php
                        $comments = $habitatController->getApprovedComments($habitat['id']);
                        foreach ($comments as $comment) {
                            echo "<div class=\"habitat-comment\">" . htmlspecialchars($comment['comment'], ENT_QUOTES, 'UTF-8') . "</div>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
