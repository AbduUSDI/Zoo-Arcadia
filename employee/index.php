<?php

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employé, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

include '../templates/header.php';
include 'navbar_employee.php';
?>

<!-- Conteneur pour afficher tout simplement que c'est bien l'espace Employé -->

<div class="container">
    <h1 class="my-4">Espace Employé</h1>
    <p>Bienvenue dans votre espace personnel. Utilisez le menu pour gérer les avis et les services.</p>
</div>

<?php include '../templates/footer.php'; ?>
