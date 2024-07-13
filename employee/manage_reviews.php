<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

$db = new Database();
$conn = $db->connect();

$reviewHandler = new Review($conn);
$reviews = $reviewHandler->getAvisTout();

$habitatHandler = new Habitat($conn);
$vetComments = $habitatHandler->getToutHabitatsComments();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && isset($_POST['review_id'])) {
        $review_id = $_POST['review_id'];
        $reviewHandler->deleteAvis($review_id);

    } elseif (isset($_POST['approve']) && isset($_POST['review_id'])) {
        $review_id = $_POST['review_id'];
        $reviewHandler->approve($review_id);

    } elseif (isset($_POST['delete_comment']) && isset($_POST['comment_id'])) {
        $comment_id = $_POST['comment_id'];
        $habitatHandler->deleteHabitatComment($comment_id);
        
    } elseif (isset($_POST['approve_comment']) && isset($_POST['comment_id'])) {
        $comment_id = $_POST['comment_id'];
        $habitatHandler->approveHabitatComment($comment_id);
    }
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
    <h1 class="my-4">Gérer les avis des visiteurs</h1>
    <hr>
    <br>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Pseudo</th>
                    <th>Objet</th>
                    <th>Avis</th>
                    <th>Approuvé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($review['visitor_name']); ?></td>
                        <td><?php echo htmlspecialchars($review['subject']); ?></td>
                        <td><?php echo htmlspecialchars($review['review_text']); ?></td>
                        <td><?php echo $review['approved'] ? 'Oui' : 'Non'; ?></td>
                        <td>
                            <?php if (!$review['approved']): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <button type="submit" name="approve" class="btn btn-success">Approuver</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                <button type="submit" name="delete" class="btn btn-danger">Supprimer</button>
                            </form>
                            <a href="edit_review.php?id=<?php echo $review['id']; ?>" class="btn btn-warning">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <h2 class="my-4">Commentaires des vétérinaires</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID Habitat</th>
                    <th>Nom du Vétérinaire</th>
                    <th>Commentaire</th>
                    <th>Approuvé</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vetComments as $comment): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($comment['habitat_id']); ?></td>
                        <td><?php echo htmlspecialchars($comment['vet_id']); ?></td>
                        <td><?php echo htmlspecialchars($comment['comment']); ?></td>
                        <td><?php echo $comment['approved'] ? 'Oui' : 'Non'; ?></td>
                        <td>
                            <?php if (!$comment['approved']): ?>
                                <form method="post" style="display: inline;">
                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                    <button type="submit" name="approve_comment" class="btn btn-success">Approuver</button>
                                </form>
                            <?php endif; ?>
                            <form method="post" style="display: inline;">
                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                <button type="submit" name="delete_comment" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footerconnected.php'; ?>