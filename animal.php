<?php
session_start();

require 'functions.php';

$db = new Database();
$conn = $db->connect();

$animal_id = $_GET['id'] ?? null;

if (!$animal_id) {
    header("Location: animals.php");
    exit;
}

$animalDef = new Animal($conn);
$animal = $animalDef->getDetailsAnimal($animal_id);
$reports = $animalDef->getRapportsAnimalParId($animal_id);

if (!$animal) {
    header("Location: animals.php");
    exit;
}

$habitatDef = new Habitat($conn);
$habitat = $habitatDef->getParId($animal['habitat_id']);

if (!$habitat) {
    $habitat['name'] = 'Habitat indisponible';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $animalDef->ajouterLike($animal_id);
    $animal['likes']++;
}

$avis_success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $visitorName = $_POST['visitor_name'];
    $reviewText = $_POST['review_text'];

    $animalDef->ajouterAvis($visitorName, $reviewText, $animal_id);

    $avis_success = true;
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
body {
    background-image: url('image/background.jpg');
    padding-top: 48px;
}

.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <h1 class="my-4"><?php echo htmlspecialchars($animal['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($animal['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($animal['name']); ?>">
    <p>Race: <?php echo htmlspecialchars($animal['species']); ?></p>
    <p>Habitat: <?php echo htmlspecialchars($habitat['name']); ?></p>
    <p>Likes: <?php echo $animal['likes']; ?></p>
    <form action="animal.php?id=<?php echo $animal_id; ?>" method="POST">
        <button type="submit" name="like" class="btn btn-success">👍 Like</button>
    </form>
<hr>
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
