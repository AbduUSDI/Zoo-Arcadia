<?php
session_start();
require '../../vendor/autoload.php';

use Controllers\ContactController;
use Services\ContactService;

// Protection CSRF : Génération d'un token CSRF si nécessaire
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$contactService = new ContactService();
$contactController = new ContactController($contactService);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Validation et nettoyage des entrées
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $subject = filter_input(INPUT_POST, 'subject', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    if ($name && $email && $subject && $message) {
        try {
            $result = $contactController->handleContactForm($name, $email, $subject, $message);

            if ($result['success']) {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = $result['message'];
                $_SESSION['message_type'] = "danger";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Une erreur s'est produite : " . htmlspecialchars($e->getMessage());
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

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="text-center">Nous contacter</h1>
    <hr>
    <br>
    <?php
    if (isset($_SESSION['message'])) {
        echo '<div class="alert alert-' . htmlspecialchars($_SESSION['message_type']) . ' alert-dismissible fade show" role="alert">
                ' . htmlspecialchars($_SESSION['message']) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>

    <form method="POST" class="mt-5">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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
        <button type="submit" class="btn btn-success">Envoyer</button>
        <hr>
    </form>
</div>

<?php include '../views/templates/footer.php'; ?>
