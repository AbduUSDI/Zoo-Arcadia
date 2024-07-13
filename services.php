<?php
session_start();

require 'functions.php';

$db = (new Database())->connect();

$service = new Service($db);
$services = $service->getServices();

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>
<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('image/background.jpg');
    padding-top: 48px;
}
h1, .mt-5, .mb-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <br>
    <hr>
    <h1 class="my-4" style="text-align: center;">Nos Services</h1>
    <hr>
    <br>
    <div class="row">
        <?php foreach ($services as $service): ?>
            <div class="col-md-4">
                <div class="card mb-4 text-black bg-light border-success">
                    <?php if (!empty($service['image'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($service['image']); ?>" class="card-img-top" alt="Image du Service">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($service['description']); ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php include 'templates/footer.php'; ?>
