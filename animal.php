<?php
session_start();
require 'functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Nous établissons une définition pour que animal_id récupère l'id de l'animal de la base de donnée et l'affiche sur la page

$animal_id = $_GET['id'];

// Instance Animal pour utiliser les méthode afin d'afficher les details d'un animal et ses rapports vétérinaire

$animalDef = new Animal($conn);
$animal = $animalDef->getDetailsAnimal($animal_id);
$reports = $animalDef->getRapportsAnimal($animal_id);

// Méthode pour utiliser la méthode ajouterLike et récupérer les informations du formulaire (POST) like  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['like'])) {
    $animalDef->ajouterLike($animal_id);
    $animal['likes']++;
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
    body {
        padding-top: 48px;  /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
    }
</style>

<!-- Conteneur principal de la page affichant les caractéristiques de l'animal ainsi que son image, le bouton like aussi y est intégré -->

<div class="container">
    <h1 class="my-4"><?php echo htmlspecialchars($animal['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($animal['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($animal['name']); ?>">
    <p>Race: <?php echo htmlspecialchars($animal['species']); ?></p>
    <p>Habitat: <?php echo htmlspecialchars($animal['habitat_id']); ?></p>
    <p>Likes: <?php echo $animal['likes']; ?></p>

    <form action="animal.php?id=<?php echo $animal_id; ?>" method="POST">
        <button type="submit" name="like" class="btn btn-success">❤️ Like</button>
    </form>

<!-- Tableau pour afficher les rapports vétérinaire de l'animal en question -->

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
