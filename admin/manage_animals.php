<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données

$db = (new Database())->connect();

// Instance Animal pour utiliser les méthodes préparées en rapport avec les animaux

$animal = new Animal($db);

// Utilisation de la méthode "getAll" pour récupérer les infos de tout les animaux

$animals = $animal->getAll();

include '../templates/header.php';
include 'navbar_admin.php';
?>

<!-- Conteneur pour afficher le tableau de gestion des animaux -->

<div class="container">
    <h1 class="my-4">Gestion des Animaux</h1>
    <div class="table-responsive">

    <!-- Bouton pour ajouter un animal -->

        <a href="add_animal.php" class="btn btn-success mb-4">Ajouter un Animal</a>
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Prénom</th>
                    <th>Race</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($animal['id']); ?></td>
                        <td><?php echo htmlspecialchars($animal['name']); ?></td>
                        <td><?php echo htmlspecialchars($animal['species']); ?></td>
                        <td><img src="../uploads/<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" width="100"></td>
                        <td>

                            <!-- Boutons pour modifier ou supprimer (Message de confirmation si il faut supprimer ou pas) un animal -->

                            <a href="edit_animal.php?id=<?php echo htmlspecialchars($animal['id']); ?>" class="btn btn-warning">Modifier</a>
                            <a href="delete_animal.php?id=<?php echo htmlspecialchars($animal['id']); ?>" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet animal ?');">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
