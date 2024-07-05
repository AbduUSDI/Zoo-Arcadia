<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base données

$db = new Database();
$conn = $db->connect();

// Instance Habitat pour récupérer tout les habitats existants et aussi mettre un formulaire pour ajouter un commentaire à l'habitat

$habitatManager = new Habitat($conn);

// Utilisation de la méthode getToutHabitats dans la classe Habitat

$habitats = $habitatManager->getToutHabitats();

include '../templates/header.php';
include 'navbar_vet.php';
?>
<!-- Conteneur regroupant tout les habitats existants -->
<div class="container">
    <h1 class="my-4">Habitats</h1>
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
                        <td><img src="../uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>" width="250"></td>
                        <td>
                            <form action="submit_comment.php" method="post">
                                <input type="hidden" name="habitat_id" value="<?php echo $habitat['id']; ?>">
                                <textarea name="comment" required></textarea>
                                <button type="submit" class="btn btn-success">Soumettre un commentaire</button>
                            </form>
                            <?php

                            // Utilisation de la méthode getCommentApprouvés par id d'habitat pour récupérer les commentaires de l'habitat en question et l'afficher juste en dessous

                            $comments = $habitatManager->getCommentsApprouvés($habitat['id']);
                            foreach ($comments as $comment) {
                                echo "<div class=\"alert alert-success\">" . htmlspecialchars($comment['comment']) . "</div>";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
