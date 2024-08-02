<?php
session_start();

require_once '../../config/Database.php';
require_once '../models/ContactModel.php';

$database = new Database();
$db = $database->connect();

$contact = new Contact($db);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);

    $to = 'zooarcadiausdi@gmail.com';
    $subject = "Nouveau message de contact de $name"; // Ajout du sujet
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $body = "Vous avez reçu un nouveau message de contact de la part de $name.\n\n";
    $body .= "Message:\n$message\n";

    if ($contact->saveMessage($name, $email, $message)) {
        if (mail($to, $subject, $body, $headers)) {
            echo "<div class='alert alert-success text-center'><p>Merci pour votre message, $name. Nous vous répondrons sous peu.</p></div>";
        } else {
            echo "<div class='alert alert-danger text-center'><p>Désolé, il y a eu un problème lors de l'envoi de votre message. Veuillez réessayer plus tard.</p></div>";
        }
    }
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<style>
    h1, h2, h3 {
        text-align: center;
    }

    body {
        background-image: url('../../assets/image/background.jpg');
        padding-top: 78px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
    }

.mt-5, .mb-4 {
        background: whitesmoke;
        border-radius: 15px;
    }
</style>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <div class="card p-4" style="background: linear-gradient(to right, #ffffff, #ccedb6); border-radius: 10px;">
        <h1 class="card-title text-center">Contact</h1>
        <form action="contact.php" method="post" class="col-md-6 offset-md-3">
            <div class="mb-3">
                <label for="name" class="form-label">Nom :</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email :</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="message" class="form-label">Message :</label>
                <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
            </div>
            <div class="mb-3 text-center">
                <button type="submit" name="submit" class="btn btn-success">Envoyer</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../src/views/templates/footer.php'; ?>
