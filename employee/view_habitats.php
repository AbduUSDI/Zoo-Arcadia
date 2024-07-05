<?php

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employé, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Instance Habitat pour afficher tout les habitats pour consultation et rien d'autre

$habitatManager = new Habitat($conn);

// Utilisation de la méthode getToutHabitats pour SELECT * FROM habitats

$habitats = $habitatManager->getToutHabitats();

include '../templates/header.php';
include 'navbar_employee.php';
?>

<!-- Utilisation d'un container pour afficher le tableau pour afficher les habitats -->
 
<div class="container">
    <h1 class="my-4">Habitats</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Image</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($habitats as $habitat): ?>
                    <tr>

                    <!-- Utilisation ici encore de htmlspecialchars pour sécuriser le code à caractère spéciaux -->

                        <td><?php echo htmlspecialchars($habitat['id']); ?></td>
                        <td><?php echo htmlspecialchars($habitat['name']); ?></td>
                        <td><?php echo htmlspecialchars($habitat['description']); ?></td>
                        <td><img src="../uploads/<?php echo htmlspecialchars($habitat['image']); ?>" alt="<?php echo htmlspecialchars($habitat['name']); ?>" width="250"></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../templates/footer.php'; ?>
