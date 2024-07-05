<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

include '../templates/header.php';
include 'navbar_vet.php';
?>

<!-- Simple conteneur pour préciser que c'est bien l'espace vétérinaire -->

<div class="container">
    <h1 class="my-4">Espace Vétérinaire</h1>
    <p>Bienvenue dans votre espace personnel. Utilisez le menu pour gérer les rapports des animaux et des habitats.</p>
</div>

<?php include '../templates/footer.php'; ?>
