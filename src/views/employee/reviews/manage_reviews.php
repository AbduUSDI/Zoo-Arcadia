<?php
session_start();

$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: Zoo-Arcadia-New/login');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Générer un token CSRF unique
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

require '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ReviewRepository;
use Repositories\HabitatRepository;
use Services\ReviewService;
use Services\HabitatService;
use Controllers\ReviewController;
use Controllers\HabitatController;

$db = (new DatabaseConnection())->connect();

$reviewRepository = new ReviewRepository($db);
$habitatRepository = new HabitatRepository($db);

$reviewService = new ReviewService($reviewRepository);
$habitatService = new HabitatService($habitatRepository);

$reviewController = new ReviewController($reviewService);
$habitatController = new HabitatController($habitatService);

$reviews = $reviewController->getAllReviews();
$vetComments = $habitatController->getAllHabitatComments();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    if (isset($_POST['delete']) && isset($_POST['review_id'])) {
        $reviewController->deleteReview($_POST['review_id']);
    } elseif (isset($_POST['approve']) && isset($_POST['review_id'])) {
        $reviewController->approveReview($_POST['review_id']);
    } elseif (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
        $habitatController->deleteHabitatComment($_POST['comment_id']);
    } elseif (isset($_POST['approve_comment']) && isset($_POST['comment_id'])) {
        $habitatController->approveHabitatComment($_POST['comment_id']);
    } elseif (isset($_POST['edit']) && isset($_POST['review_id'])) {
        $reviewId = $_POST['review_id'];
        $pseudo = trim($_POST['pseudo']);
        $subject = trim($_POST['subject']);
        $review_text = trim($_POST['review_text']);

        if ($pseudo && $subject && $review_text) {
            try {
                $reviewController->updateReview($reviewId, $pseudo, $subject, $review_text);
                $success = "Avis modifié avec succès.";
            } catch (Exception $e) {
                $error = "Erreur lors de la modification de l'avis : " . $e->getMessage();
            }
        } else {
            $error = "Données invalides. Veuillez vérifier vos entrées.";
        }
    }
    header('Location: reviews');
    exit;
}

include '../../../../src/views/templates/header.php';
include '../navbar_employee.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <h1 class="text-center my-4">Gérer les avis des visiteurs</h1>
    
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <!-- Section pour les avis des visiteurs -->
    <div class="review-cards">
        <?php foreach ($reviews as $review): ?>
            <div class="card review-card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($review['visitor_name'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($review['subject'], ENT_QUOTES, 'UTF-8') ?></h6>
                    <p class="card-text"><?= htmlspecialchars($review['review_text'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Approuvé: </strong><?= $review['approved'] ? 'Oui' : 'Non' ?></p>
                    <div class="d-flex justify-content-between">
                        <?php if (!$review['approved']): ?>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="review_id" value="<?= htmlspecialchars($review['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" name="approve" class="btn btn-success">Approuver</button>
                            </form>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="review_id" value="<?= htmlspecialchars($review['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                        </form>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#editReviewModal-<?= htmlspecialchars($review['id'], ENT_QUOTES, 'UTF-8') ?>">Modifier</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Modals pour modifier les avis -->
    <?php foreach ($reviews as $review): ?>
        <div class="modal fade" id="editReviewModal-<?= htmlspecialchars($review['id'], ENT_QUOTES, 'UTF-8') ?>" tabindex="-1" role="dialog" aria-labelledby="editReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editReviewModalLabel">Modifier l'avis</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="review_id" value="<?= htmlspecialchars($review['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <div class="mb-3">
                                <label for="pseudo" class="form-label">Pseudo:</label>
                                <input type="text" class="form-control" name="pseudo" value="<?= htmlspecialchars(trim($review['visitor_name']), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="subject" class="form-label">Objet:</label>
                                <input type="text" class="form-control" name="subject" value="<?= htmlspecialchars(trim($review['subject']), ENT_QUOTES, 'UTF-8') ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="review_text" class="form-label">Texte de l'avis:</label>
                                <textarea class="form-control" name="review_text" rows="3" required><?= htmlspecialchars(trim($review['review_text']), ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>
                            <button type="submit" name="edit" class="btn btn-success">Enregistrer les modifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

    <!-- Section pour les commentaires des vétérinaires -->
    <h2 class="text-center my-4">Commentaires des vétérinaires</h2>
    <div class="review-cards">
        <?php foreach ($vetComments as $comment): ?>
            <div class="card comment-card mb-3">
                <div class="card-body">
                    <h5 class="card-title">ID Habitat: <?= htmlspecialchars(trim($comment['habitat_id']), ENT_QUOTES, 'UTF-8') ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">Vétérinaire: <?= htmlspecialchars(trim($comment['vet_username']), ENT_QUOTES, 'UTF-8') ?></h6>
                    <p class="card-text"><?= htmlspecialchars(trim($comment['comment']), ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="card-text"><strong>Approuvé: </strong><?= $comment['approved'] ? 'Oui' : 'Non' ?></p>
                    <div class="d-flex justify-content-between">
                        <?php if (!$comment['approved']): ?>
                            <form method="post">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                                <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" name="approve_comment" class="btn btn-success">Approuver</button>
                            </form>
                        <?php endif; ?>
                        <form method="post">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="comment_id" value="<?= htmlspecialchars($comment['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <button type="submit" name="delete_comment" class="btn btn-danger">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
