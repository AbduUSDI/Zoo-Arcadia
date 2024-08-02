<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {

    session_unset();  
    session_destroy(); 
    header('Location: login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../config/Database.php';
require_once '../../config/MongoDB.php';
require_once '../models/AnimalModel.php';
require_once '../models/HabitatModel.php';

$db = new Database();
$conn = $db->connect();

$animalPage = new Animal($conn);
$animals = $animalPage->getAll();

try {
    $mongoClient = new MongoDB();
} catch (Exception $erreur) {
    die('Connexion à la base de données MongoDB échouée : ' . $erreur->getMessage());
}

$habitatDef = new Habitat($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['like'])) {
        $animalId = $_POST['animal_id'];
        
        $animalPage->ajouterLike($animalId);
    }
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 68px;
}

.mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mb-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Tous les Animaux</h1>
    <hr>
    <br>
    <div class="row">
        <?php if (count($animals) > 0): ?>
            <?php foreach ($animals as $animal): ?>
                <?php
                // Récupérer le nom de l'habitat pour chaque animal
                $habitatName = $animal['habitat_id'] ? htmlspecialchars($habitatDef->getParId($animal['habitat_id'])['name']) : 'Habitat indisponible'; ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <img src="../../assets/uploads/<?php echo htmlspecialchars($animal['image']); ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($animal['name']); ?>"
                             onclick="registerClick(<?php echo $animal['id']; ?>)"
                             style="cursor: pointer;">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>
                            <p class="card-text">Race: <?php echo htmlspecialchars($animal['species']); ?></p>
                            <p class="card-text">Habitat: <?php echo $habitatName; ?></p>
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
            <p>Aucun animal trouvé.</p>
        <?php endif; ?>
    </div>
</div>

<script>

// Fonction JS pour utiliser le fichier "record_click.php" et en utilisant l'id de l'animal pour incrémenter le click stocké sur le bon animal 

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

<?php include '../../src/views/templates/footer.php'; ?>