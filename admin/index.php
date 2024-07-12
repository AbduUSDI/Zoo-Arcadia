<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../MongoDB.php';
require '../functions.php';

$db = new Database();
$conn = $db->connect();

try {
    $mongoClient = new MongoDB();
} catch (Exception $erreur) {

}

$animalMySQL = new Animal($conn);

$habitat = new Habitat($conn);
$animals = $animalMySQL->getAll();

$totalLikes = 0;
$totalClicks = 0;

// Calcul du total des likes pour tous les animaux en utilisant une boucle "foreach" qui incrémente le total du nombre de like par la variable $animal

foreach ($animals as $animal) {
    $totalLikes += $animal['likes'];
}

$habitats = $habitat->getToutHabitats();

// Vérifie si un habitat est sélectionné dans le bouton filtrer, on utilise ici la méthode préparée "getAnimauxParHabitat"

if (isset($_POST['habitat_id']) && $_POST['habitat_id'] !== '') {
    $animals = $habitat->getAnimauxParHabitat($_POST['habitat_id']);
}

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
    <h1 class="my-4">Dashboard Admin</h1>

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
                            try {  // Utilisation de la méthode "getClicks" par "id" pour afficher les clics stockés dans la collection MongoDB grâce au clic sur l'animal
                                $clicks = $mongoClient->getClicks($animal['id']);
                                echo $clicks;
                            } catch (Exception $erreur) {
                                echo "0";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong><?php echo $totalLikes; ?></strong></td>
                    <td><strong><?php echo $totalClicks; ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footerconnected.php'; ?>
