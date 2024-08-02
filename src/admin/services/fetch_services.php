<?php
require_once '../../../config/Database.php';
require_once '../../models/ServiceModel.php';

$db = new Database();
$conn = $db->connect();

$services = new Service($conn);
$servicesList = $services->getServices();

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
                        <img src="../../../assets/uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Image du Service" style="width: 250px;">
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
