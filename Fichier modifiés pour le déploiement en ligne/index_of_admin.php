<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

// Utilisation du fichier Database et MongoDB pour les base de données relationelle et non relationelle, ainsi que functions pour toutes les autres méthodes préparées

require '../Database.php';
require '../MongoDB.php';
require '../functions.php';

// Connexion à la base données

$db = new Database();
$conn = $db->connect();

// Si la connexion à la base de données ne passe pas alors le message apparaît

if (!$conn) {
    die("Erreur de connexion à la base de données");
}

// Utilisation d'un try/catch pour se connecter à la base de données MongoDB grâce à une nouvelle instance MongoDB

try {
    $mongoClient = new MongoDB();
} catch (Exception $erreur) {
    // Gestion silencieuse de l'erreur
}

// Instance Animal pour les méthodes préparées des animaux stockés dans la BDD MySQL

$animalMySQL = new Animal($conn);

// Instance Habitat pour les méthodes préparées des habitats

$habitat = new Habitat($conn);

// Récupère tous les animaux grâce à la méthode préparée "getAll"

$animals = $animalMySQL->getAll();

// Initialisation des totaux pour afficher 0, en cas de score nul et ensuite pour pouvoir faire le calcul de l'addition des clics et des likes qui s'ajoutent

$totalLikes = 0;
$totalClicks = 0;

// Calcul du total des likes pour tous les animaux en utilisant une boucle "foreach" qui incrémente le total du nombre de like par la variable $animal

foreach ($animals as $animal) {
    $totalLikes += $animal['likes'];
}

// Récupère tous les habitats pour le filtre, grâce à la méthode préparée "getToutHabitats"

$habitats = $habitat->getToutHabitats();

// Vérifie si un habitat est sélectionné dans le bouton filtrer, on utilise ici la méthode préparée "getAnimauxParHabitat"

if (isset($_POST['habitat_id']) && $_POST['habitat_id'] !== '') {
    $animals = $habitat->getAnimauxParHabitat($_POST['habitat_id']);
}

// Tri des likes par ordre décroissants grâce à la fonction "usort"

usort($animals, function($a, $b) {
    return $b['likes'] - $a['likes'];
});

// Calcul du total de clics par animal grâce la méthode "getClicks" en récupérant l'id de l'animal et en récupérant son index (son score)

foreach ($animals as $animal) {
    try {
        $clicks = $mongoClient->getClicks($animal['id']);
        $totalClicks += $clicks;
    } catch (Exception $erreur) {
    }
}

include '../templates/header.php';
include 'navbar_admin.php';
?>

<!-- Conteneur responsive pour afficher la dashboard -->

<div class="container">
    <h1 class="my-4">Dashboard Admin</h1>

    <!-- Formulaire (POST) de filtre par habitat -->

    <form method="POST" action="">
        <div class="form-group">
            <label for="habitat_id">Filtrer par habitat :</label>
            <select class="form-control" id="habitat_id" name="habitat_id">
                <option value="">Tous les habitats</option>
                <?php foreach ($habitats as $habitat): ?>
                    <option value="<?php echo $habitat['id']; ?>"><?php echo htmlspecialchars($habitat['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-success" style="margin-bottom: 10px;">Filtrer</button>
    </form>

    <!-- Tableau des likes et clics des animaux-->

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Animal</th>
                    <th>Likes</th>
                    <th>Clics</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($animal['name']); ?></td>
                        <td><?php echo $animal['likes']; ?></td>
                        <td>
                            <?php
                            try {  // Utilisation de la méthode "getClicks" par "id" pour afficher les clics stockés sur la collection MongoDB grâce au clic sur l'animal
                                $clicks = $mongoClient->getClicks($animal['id']);
                                echo $clicks;
                            } catch (Exception $erreur) {
                                echo "0"; // Affichage du INT 0 en cas d'erreur
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong><?php echo $totalLikes; ?></strong></td> <!-- Affichage des totaux en bas du tableau -->
                    <td><strong><?php echo $totalClicks; ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
