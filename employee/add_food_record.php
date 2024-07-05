<?php
session_start();

// Vérification de l'identification de l'utiliateur, il doit être role 2 donc employé, sinon page login.php

if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 2) {
    // Rediriger si l'utilisateur n'est pas un employé
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base données

$db = new Database();
$conn = $db->connect();

// Instance Animal pour aller chercher la méthode "donnerNourriture" 

$foodManager = new Animal($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $animal_id = $_POST['animal_id'];
    $food_given = $_POST['food_given'];
    $food_quantity = $_POST['food_quantity'];
    $date_given = $_POST['date_given'];

    // Utilisation de la méthode "donnerNourriture" ici pour récupérer les données du formulaire POST

    if ($foodManager->donnerNourriture($animal_id, $food_given, $food_quantity, $date_given)) {
        header('Location: manage_food.php');
        exit;
    } else {
        $error = "Une erreur s'est produite lors de l'ajout de la nourriture.";
    }
}

// Utilisation de la méthode "getAnimaux" pour afficher les animaux disponible dans le toggle Select

$animals = $foodManager->getAnimaux();

include '../templates/header.php';
include 'navbar_employee.php';
?>

<!-- Conteneur pour afficher le formulaire (POST) pour donner de la nourriture à un animal -->

<div class="container">
    <h1 class="my-4">Donner de la Nourriture</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="add_food.php" method="POST">
        <div class="form-group">
            <label for="animal_id">Animal</label>
            <select class="form-control" id="animal_id" name="animal_id" required>
                <?php foreach ($animals as $animal): ?>
                    <option value="<?php echo htmlspecialchars($animal['id']); ?>"><?php echo htmlspecialchars($animal['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="food_given">Nourriture Donnée</label>
            <input type="text" class="form-control" id="food_given" name="food_given" required>
        </div>
        <div class="form-group">
            <label for="food_quantity">Quantité (grammes)</label>
            <input type="number" class="form-control" id="food_quantity" name="food_quantity" required>
        </div>
        <div class="form-group">
            <label for="date_given">Date Donnée</label>
            <input type="date" class="form-control" id="date_given" name="date_given" required>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>
