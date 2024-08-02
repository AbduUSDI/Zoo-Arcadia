<?php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../public/login.php');
    exit;
}

require_once '../../config/MongoDB.php';
require_once '../../config/Database.php';
require_once '../models/AnimalModel.php';
require_once '../models/HabitatModel.php';


$db = new Database();
$conn = $db->connect();

if (!$conn) {
    die("Erreur de connexion à la base de données");
}

try {
    $mongoClient = new MongoDB();
} catch (Exception $erreur) {
    echo "Erreur lors de la récupération des clics : " . htmlspecialchars($erreur->getMessage());
}

$animalMySQL = new Animal($conn);

$habitat = new Habitat($conn);

$animals = $animalMySQL->getAll();

$totalLikes = 0;
$totalClicks = 0;

foreach ($animals as $animal) {
    $totalLikes += $animal['likes'];
}

$habitats = $habitat->getToutHabitats();

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
        echo "Erreur lors de la récupération des clics : " . htmlspecialchars($erreur->getMessage());
    }
}

include_once '../../src/views/templates/header.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('../../assets/image/background.jpg');
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<nav class="navbar navbar-expand-lg navbar-light bg-light" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <a class="navbar-brand" href="../public/index.php"><img src="../../assets/favicon.ico" width="32px" height="32px"> Zoo Arcadia</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="users/manage_users.php">Gérer Utilisateurs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services/manage_services.php">Gérer Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="habitats/manage_habitats.php">Gérer Habitats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="animals/manage_animals.php">Gérer Animaux</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reports/manage_animal_reports.php">Gérer Rapports Vétérinaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="horaires/zoo_hours.php">Gérer Horaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
<br>
    <hr>
    <h1 class="my-4">Dashboard admin</h1>
    <hr>
    <br>
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
                            try {
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

<footer id="footerId" class="bg-light text-center text-lg-start mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-secondary" href="../public/contact.php"><img src="../../assets/image/lettre.png" width="32px" height="32px"></img>   Nous contacter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="../public/index.php#openhours"><img src="../../assets/image/ouvert.png" width="32px" height="32px"></img>   Nos horaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="../public/index.php#apropos"><img src="../../assets/image/a-propos-de-nous.png" width="32px" height="32px"></img>   A propos de nous</a>
            </li>
            </ul>
        <div class="container p-4">
            <p class="text-secondary"><img src="../../assets/image/favicon.jpg" width="32px" height="32px"></img>   &copy; 2024 Zoo Arcadia. Tous droits réservés.</p>
        </div>
    </footer>
    <!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Inclusion de Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <!-- Inclusion de Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Inclusion de AXIOS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="../js/scripts.js"></script>
</body>
</html>
