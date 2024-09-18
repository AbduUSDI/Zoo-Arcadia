<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../public/login.php');
    exit;
}

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {

    session_unset();  
    session_destroy(); 
    header('Location: ../public/login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

include '../../../src/views/templates/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <a class="navbar-brand" href="../../public/index.php"><img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px"></img>   Zoo Arcadia</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php">Accueil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="reviews/manage_reviews.php">Gérer Avis</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="services/manage_services.php">Gérer Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="food_record/manage_food.php">Gérer Nourriture</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view/view_animals.php">Animaux</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="view/view_habitats.php">Habitats</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Déconnexion</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Espace Employé</h1>
    <hr>
    <br>
    <p>Bienvenue dans votre espace personnel. Utilisez le menu pour gérer les avis et les services.</p>
</div>

<footer id="footerId" class="bg-light text-center text-lg-start mt-5 fixed-bottom" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link text-secondary" href="../../public/contact.php"><img src="/Zoo-Arcadia-New/assets/image/lettre.png" width="32px" height="32px"></img>   Nous contacter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="../../public/index.php#openhours"><img src="/Zoo-Arcadia-New/assets/image/ouvert.png" width="32px" height="32px"></img>   Nos horaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-secondary" href="../../public/index.php#apropos"><img src="/Zoo-Arcadia-New/assets/image/a-propos-de-nous.png" width="32px" height="32px"></img>   A propos de nous</a>
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
