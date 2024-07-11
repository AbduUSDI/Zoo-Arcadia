<?php
session_start();

require 'functions.php';
require 'MongoDB.php';

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();

// Instance pour classe Animal
$animalPage = new Animal($conn);

// Utilisation de la méthode getAll pour afficher tous les animaux
$animals = $animalPage->getAll();

// Connexion à la base de donnée MongoDB
try {
    $mongoClient = new MongoDB();
} catch (Exception $erreur) {
    die('Connexion à la base de données MongoDB échouée : ' . $erreur->getMessage());
}

// Récupération des informations envoyées par le bouton like
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['like'])) {
        $animalId = $_POST['animal_id'];
        
        // Utilisation de la méthode d'ajout de Like
        $animalPage->ajouterLike($animalId);
    }
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}

h1, .mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
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
                        <img src="uploads/<?php echo htmlspecialchars($animal['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($animal['name']); ?>"
                             onclick="registerClick(<?php echo $animal['id']; ?>)"
                             style="cursor: pointer;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                            <p class="card-text">Race: <?php echo htmlspecialchars($animal['species']); ?></p>
                            <p class="card-text">Habitat: <?php echo htmlspecialchars($animal['habitat_id']); ?></p>
                            <p class="card-text">Likes: <?php echo $animal['likes']; ?></p>
                            <form action="animals.php" method="POST">
                                <input type="hidden" name="animal_id" value="<?php echo $animal['id']; ?>">
                                <button type="submit" name="like" class="btn btn-success">👍 Like</button>
                            </form>
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

<script>
function registerClick(animalId) {
    console.log("Tentative d'enregistrement du clic pour l'animal ID:", animalId);
    fetch('record_click.php?animal_id=' + animalId)
        .then(response => {
            console.log("Réponse reçue:", response);
            return response.text();
        })
        .then(data => {
            console.log("Données reçues:", data);
            window.location.href = 'animal.php?id=' + animalId;
        })
        .catch(error => {
            console.error("Erreur lors de l'enregistrement du clic:", error);
        });
}
</script>

<?php include 'templates/footer.php'; ?>