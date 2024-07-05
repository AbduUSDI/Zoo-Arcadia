<?php
session_start();
require 'functions.php';

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();

// Instance pour classe Animal
$animalPage = new Animal($conn);

// Utilisation de la méthode getAll pour afficher tout les animaux

$animals = $animalPage->getAll();

// Récupération des informations envoyées par le bouton like et le champ commentaire

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['like'])) {
        $animalId = $_POST['animal_id'];

        // Utilisation de la méthode d'ajout de Like

        $animalPage->ajouterLike($animalId);  
    } elseif (isset($_POST['comment'])) {
        $animalId = $_POST['animal_id'];
        $visitorName = $_POST['visitor_name'];
        $subject = $_POST['subject'];
        $reviewText = $_POST['review_text'];

        // Utilisation de la requête préparée d'ajout d'un Avis pour visiteur

        $animalPage->ajouterAvis($visitorName, $subject, $reviewText, $animalId);  
    }
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
body {
    padding-top: 48px;  /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
.card-img-top {
    height: 300px;
    object-fit: cover;
}
</style>

<!-- Conteneur pour afficher les animaux ainsi que leurs informations de like et commentaires existants -->

<div class="container">
    <h1 class="my-4">Tous les Animaux</h1>
    <div class="row">
        <?php if (count($animals) > 0): ?>
            <?php foreach ($animals as $animal): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <img src="uploads/<?php echo htmlspecialchars($animal['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($animal['name']); ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                            <p class="card-text">Race: <?php echo htmlspecialchars($animal['species']); ?></p>
                            <p class="card-text">Habitat: <?php echo htmlspecialchars($animal['habitat_id']); ?></p>
                            <p class="card-text">Likes: <?php echo $animal['likes']; ?></p>
                            <form action="animals.php" method="POST">
                                <input type="hidden" name="animal_id" value="<?php echo $animal['id']; ?>">
                                <button type="submit" name="like" class="btn btn-success">❤️ Like</button>
                            </form>
                            <hr>

                            <!-- Bloc pour ajouter un commentaire, il utilise la méthode POST -->

                            <h6>Ajouter un commentaire</h6>
                            <form action="animals.php" method="POST">
                                <input type="hidden" name="animal_id" value="<?php echo $animal['id']; ?>">
                                <div class="mb-3">
                                    <label for="visitor_name" class="form-label">Nom</label>
                                    <input type="text" class="form-control" name="visitor_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Objet</label>
                                    <input type="text" class="form-control" name="subject" required>
                                </div>
                                <div class="mb-3">
                                    <label for="review_text" class="form-label">Commentaire</label>
                                    <textarea class="form-control" name="review_text" rows="3" required></textarea>
                                </div>
                                <button type="submit" name="comment" class="btn btn-success">Poster</button>
                            </form>
                            <hr>
                            <!-- Accordéon pour afficher les commentaires existants (approuvés) -->
                            <div class="accordion" id="accordionExample-<?php echo $animal['id']; ?>">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="headingOne-<?php echo $animal['id']; ?>">
                                        <button class="btn btn-outline-success" type="button" data-toggle="collapse" data-target="#collapseExample-<?php echo $animal['id']; ?>" aria-expanded="false" aria-controls="collapseExample">
                                            Voir les commentaires
                                        </button>
                                    </h2>
                                    <div id="collapseExample-<?php echo $animal['id']; ?>" class="collapse" aria-labelledby="headingOne-<?php echo $animal['id']; ?>" data-parent="#accordionExample-<?php echo $animal['id']; ?>">
                                        <div class="accordion-body">
                                            <ul class="list-group">
                                                <?php

                                                // Utilisation ici de la méthode getAvisAnimaux par son id afin de sélectionner les élements nécessaires par animal

                                                $comments = $animalPage->getAvisAnimaux($animal['id']);
                                                foreach ($comments as $comment): ?>
                                                    <li class="list-group-item">
                                                        <strong><?php echo htmlspecialchars($comment['visitor_name']); ?>:</strong> <?php echo htmlspecialchars($comment['review_text']); ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>

            <!-- Ce petit <p> s'affiche uniquement s'il n'y a pas d'animaux dans l'habitat -->

            <p>Aucun animal trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
