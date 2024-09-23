<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Redirection si l'utilisateur n'est pas connecté ou n'a pas les droits d'accès admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

// Vérification de la durée de session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Database\MongoDBConnection;
use Repositories\AnimalRepository;
use Repositories\ClickRepository;
use Services\AnimalService;
use Services\ClickService;
use Controllers\AnimalController;
use Repositories\HabitatRepository;
use Services\HabitatService;
use Controllers\HabitatController;

// Connexion à la base de données MySQL et MongoDB
$db = (new DatabaseConnection())->connect();
$mongoCollection = (new MongoDBConnection())->getCollection('clicks');

// Initialisation des repositories
$animalRepository = new AnimalRepository($db);
$clickRepository = new ClickRepository($mongoCollection);
$habitatRepository = new HabitatRepository($db);

// Initialisation des services
$animalService = new AnimalService($animalRepository, $clickRepository);
$clickService = new ClickService($clickRepository);
$habitatService = new HabitatService($habitatRepository);

// Initialisation des contrôleurs
$animalController = new AnimalController($animalService, $clickService);
$habitatController = new HabitatController($habitatService);

$animals = $animalController->getAllAnimals();
$habitats = $habitatController->getAllHabitats();

$totalLikes = $animalController->getTotalLikes($animals);
$totalClicks = $animalController->getTotalClicks($animals);

// Gestion du filtrage par habitat
if (isset($_POST['habitat_id']) && $_POST['habitat_id'] !== '') {
    $habitat_id = filter_input(INPUT_POST, 'habitat_id', FILTER_VALIDATE_INT);
    if ($habitat_id) {
        $animals = $animalController->getAnimalsByHabitat($habitat_id);
    }
}

// Tri des animaux par nombre de likes
usort($animals, function($a, $b) {
    return $b['likes'] - $a['likes'];
});

include_once '../../../src/views/templates/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <a class="navbar-brand" href="/Zoo-Arcadia-New/home"><img src="/Zoo-Arcadia-New/assets/favicon.ico" width="32px" height="32px"> Zoo Arcadia</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin/users">Gérer Utilisateurs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin/services">Gérer Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin/habitats">Gérer Habitats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin/animals">Gérer Animaux</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin/reports">Gérer Rapports Vétérinaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/admin/horaires">Gérer Horaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/Zoo-Arcadia-New/logout">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Dashboard Admin</h1>
    <hr>
    <br>
    <form method="POST" action="">
        <div class="form-group">
            <label for="habitat_id">Filtrer par habitat :</label>
            <select class="form-control" id="habitat_id" name="habitat_id">
                <option value="">Tous les habitats</option>
                <?php foreach ($habitats as $habitat): ?>
                    <option value="<?php echo htmlspecialchars($habitat['id']); ?>"><?php echo htmlspecialchars($habitat['name']); ?></option>
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
                        <td><?php echo htmlspecialchars($animal['likes']); ?></td>
                        <td><?php echo htmlspecialchars($animal['clicks']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><strong>Total</strong></td>
                    <td><strong><?php echo htmlspecialchars($totalLikes); ?></strong></td>
                    <td><strong><?php echo htmlspecialchars($totalClicks); ?></strong></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<footer id="footerId" class="bg-light text-center text-lg-start mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/contact"><img src="/Zoo-Arcadia-New/assets/image/lettre.png" width="32px" height="32px"></img>   Nous contacter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/home#openhours"><img src="/Zoo-Arcadia-New/assets/image/ouvert.png" width="32px" height="32px"></img>   Nos horaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/aproposdenous"><img src="/Zoo-Arcadia-New/assets/image/a-propos-de-nous.png" width="32px" height="32px"></img>   A propos de nous</a>
            </li>
            </ul>
        <div class="containerr p-4">
            <p class="text-secondary"><img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px"></img>   &copy; 2024 Zoo Arcadia. Tous droits réservés.</p>
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

<script src="/Zoo-Arcadia-New/assets/js/scripts.js"></script>
</body>
</html>
