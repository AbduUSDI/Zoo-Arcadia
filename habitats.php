<?php
session_start();
require_once 'functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Instance Habitat pour afficher tout les habitat sur la page

$habitat = new Habitat($conn);
$habitats = $habitat->getToutHabitats();

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>
<style> 
h1, h2 {
    text-align: center;
}

body {
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
</style>

<!-- Utilisation des card pour afficher les habitats avec leurs images et derscriptions ainsi que le bouton détails -->

<div class="container">
    <h1 class="my-4">Tous les Habitats</h1>
    <div class="row">
        <?php foreach ($habitats as $habitat): ?>
            <div class="col-md-4">
                <div class="card mb-4  text-dark" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
                    <img class="card-img-top" src="uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title" style="text-align: center";><?php echo htmlspecialchars($habitat['name']); ?></h5>
                        <a href="habitat.php?id=<?php echo $habitat['id']; ?>" class="btn btn-success">Voir les détails</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
