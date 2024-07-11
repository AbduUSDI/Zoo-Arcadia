<?php
session_start();

// Utilisation du fichier Database et MongoDB pour les base de données relationelle et non relationelle, ainsi que functions pour toutes les autres méthodes préparées

require 'functions.php';
require 'MongoDB.php';

// Connexion à la base de données
$db = (new Database())->connect();

// Si la connexion à la base de données ne passe pas alors le message apparaît

if (!$db) {
    die("Erreur de connexion à la base de données");
}

// Connexion à la base de donnée MongoDB

try {
    $mongoClient = new MongoDB();
} catch (Exception $erreur) {
    die('Connexion à la base de données MongoDB échouée : ' . $erreur->getMessage());
}

// Récupération de l'id de l'habitat selectionné

if ($db && $mongoClient) {
    $habitatId = $_GET['id'];

    // Instance Habitat pour les méthodes préparées MySQL
    
    $habitatModel = new Habitat($db);

    // Récupération de l'habitat sélectionné par son id grâce à la méthode préparée "getParId"

    $habitat = $habitatModel->getParId($habitatId);

    // Récupération de l'animal sélectionné par son id d'habitat grâce à la méthode préparée "getAnimauxParHabitat"

    $animals = $habitatModel->getAnimauxParHabitat($habitatId);

    // Récupération des commentaires vétérinaires d'habitat grâce à la méthode préparée "getCommentApprouvés"

    $vetComments = $habitatModel->getCommentsApprouvés($habitatId);
} else {

    // Message d'erreur au cas ou si l'id n'est pas trouvé

    die('Connexion à la base de données échouée.');
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
h1, h2 {
    text-align: center; /* Centrage des titre h1 et h2 */
}

body {
    padding-top: 48px;  /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
</style>

<!-- Conteneur pour afficher la card de l'habitat, c'est-à-dire : la photo, le commentaire habitat, les animaux de l'habitat -->

    <!-- Affichage de la photo de l'habitat -->
    
<div class="container">
    <h1 class="my-4"><?php echo htmlspecialchars($habitat['name']); ?></h1>
    <img src="uploads/<?php echo htmlspecialchars($habitat['image']); ?>" class="img-fluid mb-4" alt="<?php echo htmlspecialchars($habitat['name']); ?>">
    <p><?php echo htmlspecialchars($habitat['description']); ?></p>

    <!-- Bloc pour afficher les commentaire vétérinaires -->

    <h2>Commentaires sur l'habitat</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Nom du vétérinaire</th>
                    <th>Date du commentaire</th>
                    <th>Commentaire</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vetComments as $comment): ?>
                    <tr>
                        <td scope="col" class="col-3"><?php echo htmlspecialchars($comment['username']); ?></td>
                        <td scope="col" class="col-3"><?php echo htmlspecialchars($comment['created_at']); ?></td>
                        <td scope="col" class="col-6"><?php echo htmlspecialchars($comment['comment']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($vetComments)): ?>
                    <tr>
                        <td colspan="3">Aucun commentaire disponible pour cet habitat.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Section for displaying animals -->
    <h2>Animaux</h2>
    <div class="row">
        <?php foreach ($animals as $animal): ?>
            <div class="col-md-4">
                <div class="card mb-4">
                    <img class="card-img-top" src="uploads/<?php echo htmlspecialchars($animal['image']); ?>" alt="<?php echo htmlspecialchars($animal['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($animal['name']); ?></h5>

                        <!-- Onclick event to register click using AJAX -->
                        <button onclick="registerClick(<?php echo $animal['id']; ?>)" class="btn btn-success">Plus de détails</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>

function registerClick(animalId) {
    console.log("Tentative d'enregistrement du clic pour l'animal ID:", animalId);
    fetch('record_click.php?animal_id=' + animalId)
        .then(response => {
            console.log("Réponse reçue:", response);
            return response.text();
        })
        .then(data => {
            console.log("Données reçues:", data);
            window.location.href = 'animal.php?id=' + animalId;
        })
        .catch(error => {
            console.error("Erreur lors de l'enregistrement du clic:", error);
        });
}

</script>

<?php include 'templates/footer.php'; ?>
