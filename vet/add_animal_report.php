<?php
session_start();

require '../Database.php';
require '../functions.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

// Connexion à la base de données

$db = new Database();
$conn = $db->connect();

// Récupération des informations du formulaire de rapport vétérinaire

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Récupération par label (for) de chaque données

    $animal_id = filter_input(INPUT_POST, 'animal_id', FILTER_VALIDATE_INT);
    $vet_id = $_SESSION['user']['id'];
    $health_status = htmlspecialchars($_POST['health_status']);
    $food_given = htmlspecialchars($_POST['food_given']);
    $food_quantity = filter_input(INPUT_POST, 'food_quantity', FILTER_VALIDATE_INT);
    $visit_date = htmlspecialchars($_POST['visit_date']);
    $details = htmlspecialchars($_POST['details']);

    // Utilisation d'une instance Animal ici dans le if pour qu'elle soit utilisé seulement en cas d'action du formulaire (POST)

    $animal = new Animal($conn);

    // Méthode ajouterRapports pour faire le lien avec la BDD et récupérer les infos grâce au traitement des données plus haut et redirection vers la page Gérer rapports

    $animal->ajouterRapports($animal_id, $vet_id, $health_status, $food_given, $food_quantity, $visit_date, $details);

    header('Location: manage_animal_reports.php');
    exit;
}

// Utilisation de l'instance Animal une seconde fois pour pouvoir utiliser la méthode getAll qui affichera dans le label Animal les animaux disponible à la sélection

$animal = new Animal($conn); 
$animals = $animal->getAll();

include '../templates/header.php';
include 'navbar_vet.php';
?>
<style>
body {
    background-image: url('../image/background.jpg');
}

h1, .mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<!-- Conteneur du formulaire POST pour ajouter un rapport vétérinaire -->

<div class="container mt-5">
    <h1 class="my-4">Ajouter un Rapport Animal</h1>
    <form action="add_animal_report.php" method="POST">
        <div class="form-group">
            <label for="animal_id">Animal</label>
            <select class="form-control" id="animal_id" name="animal_id" required>
                <?php foreach ($animals as $animal): ?>
                    <option value="<?php echo $animal['id']; ?>"><?php echo htmlspecialchars($animal['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="health_status">État</label>
            <input type="text" class="form-control" id="health_status" name="health_status" required>
        </div>
        <div class="form-group">
            <label for="food_given">Nourriture</label>
            <input type="text" class="form-control" id="food_given" name="food_given" required>
        </div>
        <div class="form-group">
            <label for="food_quantity">Grammage (en grammes)</label>
            <input type="number" class="form-control" id="food_quantity" name="food_quantity" required>
        </div>
        <div class="form-group">
            <label for="visit_date">Date de Passage</label>
            <input type="date" class="form-control" id="visit_date" name="visit_date" required>
        </div>
        <div class="form-group">
            <label for="details">Détails (facultatif)</label>
            <textarea class="form-control" id="details" name="details" rows="4"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../templates/footerconnected.php'; ?>
