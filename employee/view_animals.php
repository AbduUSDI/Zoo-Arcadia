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

// Instance Animal pour afficher tout les animaux pour consultation

$animalManager = new Animal($conn);

// Utilisation de la méthode getAll pour récupérer tout les animaux existants et les afficher dans le tableau en bas

$animals = $animalManager->getAll();

include '../templates/header.php';
include 'navbar_employee.php';
?>

<style>

    /* Utilisation d'une hauteur maximum de l'accordéon pour garder l'effet responsif au cas ou il y aurait beaucoup de commentaires */

    .accordion {
        max-height: 200px;
        overflow-y: auto;
    }
</style>

<!-- Utilisation d'un conteneur pour afficher dans un tableau les animaux existants et y ajouter aussi deux boutons : le premier pour afficher les commentaires sur l'animal et le deuxième pour afficher les repas consommés par l'animal -->

<div class="container">
    <h1 class="my-4">Animaux</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Espèce</th>
                    <th>Habitat</th>
                    <th>Image</th>
                    <th>Commentaires</th>
                    <th>Nourriture Donnée</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($animals as $animal): ?>
                <tr> 
                    
                <!-- Utilisation ici encore de htmlspecialchars pour sécuriser le code à caractère spéciaux -->

                    <td><?php echo htmlspecialchars($animal['id']); ?></td>
                    <td><?php echo htmlspecialchars($animal['name']); ?></td>
                    <td><?php echo htmlspecialchars($animal['species']); ?></td>
                    <td><?php echo htmlspecialchars($animal['habitat_name']); ?></td>
                    <td><img src="<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>" style="width: 100px;"></td>
                    <td>
                        <!-- Accordéon pour les commentaires -->
                        <div class="accordion" id="accordionExample-<?php echo $animal['id']; ?>">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne-<?php echo $animal['id']; ?>">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapseComments-<?php echo $animal['id']; ?>" aria-expanded="false" aria-controls="collapseComments">
                                        Voir les commentaires
                                    </button>
                                </h2>
                                <div id="collapseComments-<?php echo $animal['id']; ?>" class="collapse" aria-labelledby="headingOne-<?php echo $animal['id']; ?>" data-parent="#accordionExample-<?php echo $animal['id']; ?>">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <?php

                                            // Utilisation de la méthode getAvisAnimaux par id d'animal afin d'afficher les bons commentaires

                                            $comments = $animalManager->getAvisAnimaux($animal['id']);
                                            foreach ($comments as $comment): ?>
                                                <li class="list-group-item">
                                                    <strong><?php echo htmlspecialchars($comment['visitor_name']); ?>:</strong> <?php echo htmlspecialchars($comment['review_text']); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <!-- Accordéon pour les nourritures -->
                        <div class="accordion" id="accordionExampleFood-<?php echo $animal['id']; ?>">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFood-<?php echo $animal['id']; ?>">
                                    <button class="btn btn-outline-secondary" type="button" data-toggle="collapse" data-target="#collapseFood-<?php echo $animal['id']; ?>" aria-expanded="false" aria-controls="collapseFood">
                                        Voir les nourritures
                                    </button>
                                </h2>
                                <div id="collapseFood-<?php echo $animal['id']; ?>" class="collapse" aria-labelledby="headingFood-<?php echo $animal['id']; ?>" data-parent="#accordionExampleFood-<?php echo $animal['id']; ?>">
                                    <div class="accordion-body">
                                        <ul class="list-group">
                                            <?php

                                            // Utilisation de la méthode getNourritureAnimaux par id d'animal afin d'afficher les bonnes informations par animal

                                            $foods = $animalManager->getNourritureAnimaux($animal['id']);
                                            foreach ($foods as $food): ?>
                                                <li class="list-group-item">
                                                    <strong>Nourriture:</strong> <?php echo htmlspecialchars($food['food_given']); ?><br>
                                                    <strong>Quantité:</strong> <?php echo htmlspecialchars($food['food_quantity']); ?>g<br>
                                                    <strong>Date:</strong> <?php echo htmlspecialchars($food['date_given']); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
