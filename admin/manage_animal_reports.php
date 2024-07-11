<?php

// Vérification de l'identification de l'utilisateur, il doit être role 1 donc admin, sinon redirection vers la page login.php

session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: ../login.php');
    exit;
}

require '../functions.php';

// Instance pour utiliser la connexion à la base de données

$db = new Database();
$conn = $db->connect();

include '../templates/header.php';
include 'navbar_admin.php';
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
<!-- Conteneur pour filtrer les rapports vétérinaires avec -->

<div class="container mt-4">
    <h1 class="my-4">Gérer les Rapports Vétérinaires</h1>
    <form id="filterForm">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="filterDate">Date de Visite:</label>
                <select class="form-control" id="filterDate" name="visit_date">
                    <option value="">Toutes les dates</option>

                    <!-- Les filtres par date sera chargées par JavaScript AJAX/AXIOS -->

                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="filterAnimal">Animal:</label>
                <select class="form-control" id="filterAnimal" name="animal_id">
                    <option value="">Tous les animaux</option>

                    <!-- Les filtres par animal sera chargées par JavaScript AJAX/AXIOS -->

                </select>
            </div>
        </div>
        <button type="button" class="btn btn-success" id="filterButton">Filtrer</button> <!-- Bouton pour valider la demande de filtre et demande la requête à JavaScript -->
    </form>
    <br>

    <div class="table-responsive" id="reportsTable">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
                <tr>
                    <th>Animal</th>
                    <th>État de Santé</th>
                    <th>Nourriture Donnée</th>
                    <th>Grammage</th>
                    <th>Date de Passage</th>
                    <th>Détails</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="reportsBody">

                <!-- Affichage des rapports vétérinaires chargés par JavaScript AJAX/AXIOS -->

            </tbody>
        </table>
    </div>
</div>

<!-- Inclusion de la bibliothèque Axios pour les requêtes HTTP -->

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>

    // Récupération des id pour établir les fonctions

    document.addEventListener('DOMContentLoaded', function() {
        const filterButton = document.getElementById('filterButton');
        const filterDate = document.getElementById('filterDate');
        const filterAnimal = document.getElementById('filterAnimal');
        const reportsBody = document.getElementById('reportsBody');

        // Fonction "loadOptions" pour charger les options des filtres depuis le serveur

        function loadOptions() {
            axios.get('get_options.php') // Chargement du fichier get_options.php pour récupérer les informations des rapports et animaux
                .then(response => {
                    const dates = response.data.dates;
                    const animals = response.data.animals;

                    // Ajouter les filtres des dates au sélecteur

                    dates.forEach(date => {
                        const option = document.createElement('option');
                        option.value = date.visit_date;
                        option.textContent = date.visit_date;
                        filterDate.appendChild(option);
                    });

                    // Ajouter les filtres des animaux au sélecteur

                    animals.forEach(animal => {
                        const option = document.createElement('option');
                        option.value = animal.id;
                        option.textContent = animal.name;
                        filterAnimal.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des options:', error);
                });
        }

        // Fonction pour charger les rapports depuis le serveur avec les filtres choisis

        function loadReports() {
            const visitDate = filterDate.value;
            const animalId = filterAnimal.value;

            axios.get('get_reports.php', {  // Chargement du fichier get_reports.php
                params: {
                    visit_date: visitDate,
                    animal_id: animalId
                }
            })
            .then(response => {
                // Vider le contenu actuel du tableau des rapports
                reportsBody.innerHTML = '';

                // Ajouter les nouveaux rapports au tableau
                response.data.forEach(report => {
                    const row = `<tr>
                        <td>${report.animal_name}</td>
                        <td>${report.health_status}</td>
                        <td>${report.food_given}</td>
                        <td>${report.food_quantity}</td>
                        <td>${report.visit_date}</td>
                        <td>${report.details}</td>
                        <td>
                            <a href="delete_vet_report.php?id=${report.id}" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce rapport ?');">Supprimer</a>
                        </td>
                    </tr>`;
                    reportsBody.innerHTML += row;
                });
            })
            .catch(error => {
                console.error('Erreur lors du chargement des rapports:', error);
            });
        }

        // Chargement des rapports vétérinaire lorsque l'utilisateur clique sur le bouton "Filtrer"

        filterButton.addEventListener('click', loadReports);
        
        // Charger les options des filtres et les rapports au démarrage de la page

        loadOptions(); //Chargement de toutes les options au démarrage
        loadReports(); // Chargement de tout les rapports au démarrage

        // Actualisation de la liste des rapports toutes les 30 secondes

        setInterval(loadReports, 30000);
    });
</script>

<?php include '../templates/footer.php'; ?>
