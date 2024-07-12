<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 3) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Connexion à la base de données
$db = new Database();
$conn = $db->connect();

// Instance Animal pour afficher les animaux dans le tableau
$animalManager = new Animal($conn);

include '../templates/header.php';
include 'navbar_vet.php';
?>
<style>

body {
    background-image: url('../image/background.jpg');
}
/* Utilisation d'une hauteur maximum de l'accordéon pour garder l'effet responsif au cas ou il y aurait beaucoup de commentaires */
.accordion {
    max-height: 200px;
    overflow-y: auto;
}
h1, .mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <h1 class="my-4">Gestion des Animaux</h1>
    <div class="table-responsive">
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
                <?php
                // Utilisation de la méthode getAll pour les animaux
                $animals = $animalManager->getAll();
                foreach ($animals as $animal) {
                    // Utilisation ici de "echo" pour afficher le tableau des animaux ainsi que le bouton pour rediriger vers Ajouter rapport
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($animal['id']) . "</td>";  // Prévention contre XSS grâce à htmlspecialchars
                    echo "<td>" . htmlspecialchars($animal['name']) . "</td>";
                    echo "<td>" . htmlspecialchars($animal['species']) . "</td>";
                    echo "<td><img src=\"../uploads/" . htmlspecialchars($animal['image']) . "\" alt=\"" . htmlspecialchars($animal['name']) . "\" width=\"100\"></td>";
                    echo "<td>
                    <div class=\"accordion\" id=\"accordionExampleFood-" . htmlspecialchars($animal['id']) . "\">
                        <div class=\"accordion-item\">
                            <h2 class=\"accordion-header\" id=\"headingFood-" . htmlspecialchars($animal['id']) . "\">
                                <button class=\"btn btn-outline-success\" type=\"button\" data-toggle=\"collapse\" data-target=\"#collapseFood-" . htmlspecialchars($animal['id']) . "\" aria-expanded=\"false\" aria-controls=\"collapseFood\">
                                    Voir les nourritures
                                </button>
                            </h2>
                            <div id=\"collapseFood-" . htmlspecialchars($animal['id']) . "\" class=\"collapse\" aria-labelledby=\"headingFood-" . htmlspecialchars($animal['id']) . "\" data-parent=\"#accordionExampleFood-" . htmlspecialchars($animal['id']) . "\">
                                <div class=\"accordion-body\">
                                    <ul class=\"list-group\">";
                    // Utilisation de la méthode getNourritureAnimaux par id d'animal afin d'afficher les bonnes informations par animal
                    $foods = $animalManager->getNourritureAnimaux($animal['id']);
                    foreach ($foods as $food) {
                        echo "<li class=\"list-group-item\">
                            <strong>Nourriture:</strong> " . htmlspecialchars($food['food_given']) . "<br>
                            <strong>Quantité:</strong> " . htmlspecialchars($food['food_quantity']) . "g<br>
                            <strong>Date:</strong> " . htmlspecialchars($food['date_given']) . "
                        </li>";
                    }
                    echo "</ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href=\"add_animal_report.php?id=" . htmlspecialchars($animal['id']) . "\" class=\"btn btn-success mt-2\">Ajouter un rapport</a>
                    </td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footerconnected.php'; ?>
