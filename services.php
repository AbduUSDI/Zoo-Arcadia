<?php
session_start();

require 'functions.php';

// Connexion à la base de données
$db = (new Database())->connect();

// Création d'une instance $service pour utiliser la méthode pour afficher les services
$service = new Service($db);
$services = $service->getServices();

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
</style>
<div class="container">
    <h1 class="my-4" style="text-align: center;">Nos Services</h1>
    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="col-md-4">
                <div class="card mb-4 text-black bg-light border-success">
                    <?php if (!empty($service['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($service['image']); ?>" class="card-img-top" alt="Image du Service">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
