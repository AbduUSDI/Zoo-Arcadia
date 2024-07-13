<?php

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employé, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base données

$db = new Database();
$conn = $db->connect();

// Instance de la classe Review pour toutes les méthodes en rapport avec les avis

$reviewManager = new Review($conn);

// Vérification si l'ID de l'avis est fourni

if (!isset($_GET['id'])) {
    echo "Aucun avis spécifié.";
    exit;
}

$reviewId = $_GET['id'];

// Récupérer les données de l'avis
$review = $reviewManager->getAvisById($reviewId);

if (!$review) {
    echo "Avis non trouvé.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = $_POST['pseudo'] ?? $review['visitor_name'];
    $subject = $_POST['subject'] ?? $review['subject'];
    $review_text = $_POST['review_text'] ?? $review['review_text'];
    $reviewManager->updateAvis($reviewId, $pseudo, $subject, $review_text);

    header('Location: manage_reviews.php');
    exit;
}

include '../templates/header.php';
include 'navbar_employee.php';
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
    <br>
    <hr>
    <h1>Modifier l'avis</h1>
    <hr>
    <br>
    <form action="edit_review.php?id=<?php echo htmlspecialchars($reviewId); ?>" method="POST">
        <div class="mb-3">
            <label for="pseudo" class="form-label">Pseudo:</label>
            <input type="text" class="form-control" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($review['visitor_name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Objet:</label>
            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($review['subject']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="review_text" class="form-label">Texte de l'avis:</label>
            <textarea class="form-control" id="review_text" name="review_text" rows="3" required><?php echo htmlspecialchars($review['review_text']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-success">Enregistrer les modifications</button>
    </form>
</div>

<?php include '../templates/footerconnected.php'; ?>
