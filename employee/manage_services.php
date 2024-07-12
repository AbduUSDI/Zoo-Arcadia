<?php

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employee, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';


// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Création d'une instance de la classe Service pour toutes les méthodes concernant les services

$services = new Service($conn);

// Affichage de tous les services sur le tableau en utilisant la méthode "getServices"

$services = $services->getServices();

include '../templates/header.php';
include 'navbar_employee.php';
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
<!-- Conteneur pour afficher la table des services existants -->

<div class="container mt-4">

    <!-- Table pour afficher les services existants -->

    <div class="table-responsive">
    <h1 class="my-4">Gérer les services</h1>
    <a href="add_service.php" class="btn btn-success mb-4">Ajouter un service</a>
    <table class="table table-bordered table-striped table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Nom</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            
            <!-- Boucle "foreach" qui affiche tous les services existants -->

            <?php foreach ($services as $service): ?>
                <tr>
                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                    <td><?php echo htmlspecialchars($service['description']); ?></td>
                    <td>
                        <?php if (!empty($service['image'])): ?>
                            <img src="../uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Image du Service" style="width: 100px;">
                        <?php endif; ?>
                    </td>
                    <td>

                        <!-- Bouton pour modifier le service -->

                        <a href="edit_service.php?id=<?php echo $service['id']; ?>" class="btn btn-warning">Modifier</a>
                        
                        <!-- Bouton pour supprimer le service -->
                        
                        <a href="delete_service.php?id=<?php echo $service['id']; ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sr de vouloir supprimer ce service ?');">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
    </div>
</div>

<?php include '../templates/footerconnected.php'; ?>
