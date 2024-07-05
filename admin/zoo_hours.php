<?php

// Vérification de l'identification de l'utiliateur, il doit être role 1 donc admin, sinon page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données
 
$db = new Database();
$conn = $db->connect();

// Instance ZooHours pour utiliser les méthode préparée pour les horaires du zoo

$zooHours = new ZooHours($conn);

// Récupération des horaires existants grâce à la méthode "getAllHours"

$hours = $zooHours->getAllHours();

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['hours'] as $id => $times) {

        // Utilisation de la méthode préparée "updateHours" pour pouvoir modifier les horaires du zoo en cliquant sur les horaires un par un
         
        $zooHours->updateHours($times['open'], $times['close'], $id);
    }
    header("Location: zoo_hours.php");
    exit;
}

include '../templates/header.php';
include 'navbar_admin.php';
?>

<!-- Conteneur pour afficher le formulaire tableau (POST) affichant les horaires du zoo modifiable -->

<div class="container">
    <h2>Modifier les horaires d'ouverture du Zoo</h2>
    <form method="POST">
    <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>Jour</th>
                        <th>Heures d'ouverture</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hours as $hour): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($hour['day']); ?></td>
                        <td>
                            <input type="time" name="hours[<?php echo $hour['id']; ?>][open]" value="<?php echo substr($hour['open_time'], 0, 5); ?>">
                            -
                            <input type="time" name="hours[<?php echo $hour['id']; ?>][close]" value="<?php echo substr($hour['close_time'], 0, 5); ?>">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-primary">Mettre à jour les horaires</button>
    </form>
</div>
</div>

<?php include '../templates/footer.php'; ?>
