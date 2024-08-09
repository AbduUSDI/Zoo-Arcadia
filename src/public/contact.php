<?php
session_start();
require '../../vendor/autoload.php';

use Controllers\ContactController;
use Services\ContactService;

$contactService = new ContactService();
$contactController = new ContactController($contactService);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $subject && $message) {
        $result = $contactController->handleContactForm($name, $email, $subject, $message);

        if ($result['success']) {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Tous les champs sont obligatoires.";
        $_SESSION['message_type'] = "danger";
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

include '../views/templates/header.php';
include '../views/templates/navbar_visitor.php';
?>

<style>
h1, h2, h3 {
    text-align: center;
}
body {
    background-image: url('../../assets/image/background.jpg');
    padding-top: 48px;
}
h1, .mt-5 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>
<div class="container mt-5">
    <br>
    <hr>
    <h1 class="text-center">Nous contacter</h1>
    <hr>
    <br>
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . $_SESSION['message_type'] . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="name" class="form-label">Nom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
            <label for="subject" class="form-label">Sujet</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
        </div>
        <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-info">Envoyer</button>
        <hr>
    </form>
</div>

<?php include '../views/templates/footer.php'; ?>
