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
    <!-- Section Contact et Navigation -->
    <div class="containerr p-4">
        <div class="row">
            <div class="col-md-4">
                <h5>Nous contacter</h5>
                <ul class="navbar-nav">
                <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/contact">
                            <i class="fas fa-envelope mr-2"></i> Nous contacter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/home#openhours">
                            <i class="fas fa-clock mr-2"></i> Nos horaires
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/aproposdenous">
                            <i class="fas fa-info-circle mr-2"></i> A propos de nous
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/mentions-legales">
                            <i class="fas fa-file-alt mr-2"></i> Mentions légales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/politique-de-confidentialite">
                            <i class="fas fa-user-shield mr-2"></i> Politique de confidentialité
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section Plan Google Maps -->
            <div class="col-md-4">
                <h5>Adresse</h5>
                <p>Forêt de Brocéliande, 35380 Paimpont</p>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2665.098412817444!2d-2.2466591491221856!3d48.00743897921212!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480ed92e0dbf4477%3A0x9e59e8de9302db5a!2s35380%20Paimpont%2C%20France!5e0!3m2!1sen!2sfr!4v1695648726871!5m2!1sen!2sfr" 
                    width="100%" 
                    height="200" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Section Réseaux sociaux -->
            <div class="col-md-4">
                <h5>Suivez-nous</h5>
                <div class="d-flex justify-content-center">
                    <a href="https://twitter.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-x-twitter fa-2x"></i>
                    </a>
                    <a href="https://facebook.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f fa-2x"></i>
                    </a>
                    <a href="https://snapchat.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-snapchat-ghost fa-2x"></i>
                    </a>
                    <a href="https://instagram.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                    <a href="https://github.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-github fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright -->
    <div class="containerr p-4">
        <p class="text-secondary">
            <img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px" alt="Zoo Arcadia Favicon"> &copy; 2024 Zoo Arcadia. Tous droits réservés.
        </p>
    </div>
</footer>

<!-- Inclusion de FontAwesome (si non inclus déjà) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Inclusion de Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Inclusion de Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Inclusion de AXIOS -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Inclusion des scripts personnalisés -->
<script src="/Zoo-Arcadia-New/assets/js/scripts.js"></script>
</body>
</html>
