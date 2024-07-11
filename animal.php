<?php
session_start();

require 'functions.php';

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();

// Vérifier si l'id de l'animal est passé via GET
$animal_id = isset($_GET['id']) ? $_GET['id'] : null;

// Vérifier si animal_id est défini
if (!$animal_id) {
    // Gérer le cas où l'id de l'animal n'est pas défini, par exemple rediriger vers une page d'erreur
    header("Location: error.php");
    exit;
}

// Instance de la classe Animal pour récupérer les détails de l'animal et ses rapports vétérinaires
$animalDef = new Animal($conn);
$animal = $animalDef->getDetailsAnimal($animal_id);

// Vérifier si l'animal existe
if (!$animal) {
    // Gérer le cas où l'animal n'est pas trouvé
    header("Location: error.php");
    exit;
}

// Récupérer les rapports vétérinaires de l'animal
$reports = $animalDef->getRapportsAnimal($animal_id);

// Instance de la classe Habitat pour récupérer les détails de l'habitat de l'animal
$habitatDef = new Habitat($conn);
$habitat = $habitatDef->getParId($animal['habitat_id']);

// Vérifier si l'habitat existe
if (!$habitat) {
    // Gérer le cas où l'habitat n'est pas trouvé
    $habitat['name'] = 'Habitat indisponible';
}

// Traitement du formulaire de Like
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $animalDef->ajouterLike($animal_id);
    // Mettre à jour le compteur de likes dans la variable $animal
    $animal['likes']++;
}

// Traitement du formulaire d'ajout d'avis
$avis_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $visitorName = $_POST['visitor_name'];
    $reviewText = $_POST['review_text'];

    // Appel à la méthode pour ajouter un avis dans la classe Animal
    $animalDef->ajouterAvis($visitorName, $reviewText, $animal_id);

    // Marquer le succès pour afficher un message de confirmation
    $avis_success = true;
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
body {
    background-image: url('image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}

h1, .mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<!-- Conteneur principal de la page pour afficher les détails de l'animal -->
<div class="container mt-5">
    <h1 class="my-4"><?php echo htmlspecialchars($animal['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($animal['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($animal['name']); ?>">
    <p>Race: <?php echo htmlspecialchars($animal['species']); ?></p>
    <p>Habitat: <?php echo htmlspecialchars($habitat['name']); ?></p>
    <p>Likes: <?php echo $animal['likes']; ?></p>

    <!-- Formulaire de Like -->
    <form action="animal.php?id=<?php echo $animal_id; ?>" method="POST">
        <button type="submit" name="like" class="btn btn-success">👍 Like</button>
    </form>
<hr>
    <!-- Formulaire d'ajout d'avis -->
    <div class="my-4">
        <h2>Ajouter un avis</h2>
        <form action="animal.php?id=<?php echo $animal_id; ?>" method="POST">
            <div class="mb-3">
                <label for="visitor_name" class="form-label">Nom</label>
                <input type="text" class="form-control" name="visitor_name" required>
            </div>
            <div class="mb-3">
                <label for="review_text" class="form-label">Commentaire</label>
                <textarea class="form-control" name="review_text" rows="3" required></textarea>
            </div>
            <button type="submit" name="comment" class="btn btn-success">Poster l'avis</button>
        </form>
        <?php if ($avis_success): ?>
            <div class="alert alert-success mt-3" role="alert">
                Votre avis a bien été envoyé, il sera soumit à la validation par nos employés !
            </div>
        <?php endif; ?>
    </div>

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
                        // Utilisation de la méthode getAvisAnimaux pour récupérer les avis par animal
                        $comments = $animalDef->getAvisAnimaux($animal['id']);
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
<hr>
<hr>
    <!-- Tableau pour afficher les rapports vétérinaires de l'animal -->
    <h2>Rapports du Vétérinaire</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Date de Visite</th>
                    <th>État de Santé</th>
                    <th>Nourriture Donnée</th>
                    <th>Quantité de Nourriture (en grammes)</th>
                    <th>Détails</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reports as $report): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['visit_date']); ?></td>
                        <td><?php echo htmlspecialchars($report['health_status']); ?></td>
                        <td><?php echo htmlspecialchars($report['food_given']); ?></td>
                        <td><?php echo htmlspecialchars($report['food_quantity']); ?></td>
                        <td><?php echo htmlspecialchars($report['details']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
