<?php
require '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\ServiceRepository;
use Services\ServiceService;
use Controllers\ServiceController;

// Connexion à la base de données
$db = (new DatabaseConnection())->connect();

// Initialisation des repositories
$serviceRepository = new ServiceRepository($db);

// Initialisation des services
$serviceService = new ServiceService($serviceRepository);

// Initialisation des contrôleurs
$serviceController = new ServiceController($serviceService);

// Récupérer tous les services
$servicesList = $serviceController->getServices();
?>

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
        <?php foreach ($servicesList as $service): ?>
            <tr>
                <td><?php echo htmlspecialchars($service['name']); ?></td>
                <td><?php echo htmlspecialchars($service['description']); ?></td>
                <td>
                    <?php if (!empty($service['image'])): ?>
                        <img src="../../../../assets/uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Image du Service" style="width: 250px;">
                    <?php endif; ?>
                </td>
                <td>
                    <a href="#" class="btn btn-warning btn-edit" data-id="<?php echo $service['id']; ?>">Modifier</a>
                    <a href="delete_service.php?id=<?php echo $service['id']; ?>" class="btn btn-danger btn-delete">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
