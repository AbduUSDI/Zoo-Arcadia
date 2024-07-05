<?php
session_start();
require 'functions.php';

// Connexion à la base données

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
            echo "<div class='alert alert-success' style='text-align: center;'><p>Merci pour votre message, $name. Nous vous répondrons sous peu.</p></div>";
        } else {
            echo "<div class='alert alert-danger' style='text-align: center;'><p>Désolé, il y a eu un problème lors de l'envoi de votre message. Veuillez réessayer plus tard.</p></div>";
        }
    }
}

include 'templates/header.php';
include 'templates/navbar_visitor.php';
?>

<style>
body {
  padding-top: 78px;
}
h1 {
  text-align: center;
  font-weight: 600;
  color: black;
  margin-bottom: 20px;
}
input[type="submit"] {
  color: white;
  border: none;
  padding: 10px 20px;
  border-radius: 5px;
  cursor: pointer;
}
input {
  border-radius: 5px;
  border: 1px solid;
  padding: 10px 20px;
  margin-bottom: 10px;
  width: 100%;
  font-size: 16px;
  font-weight: 600;
  outline: none;
  transition: all 0.3s ease;
}
#message {
  border-radius: 5px;
  border: 1px solid;
  padding: 10px 20px;
  margin-bottom: 10px;
  width: 100%;
  font-weight: 600;
  outline: none;
  transition: all 0.3s ease;
}
</style>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6); padding: 20px; border-radius: 10px; border: 5px solid #e5e5e5; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);">
  <h1>Contact</h1>
  <form action="contact.php" method="post" class="col-md-6 offset-md-3">
    <div class="mb-2">
      <label for="name" class="form-label">Nom:</label>
      <input type="text" id="name" name="name" required>
    </div>
    <div class="mb-2">
      <label for="email" class="form-label">Email:</label>
      <input type="email" id="email" name="email" required>
    </div>
    <div class="mb-2">
      <label for="message" class="form-label">Message:</label>
      <textarea id="message" name="message" class="form-control" required></textarea>
    </div>
    <div class="mb-2">
      <input type="submit" name="submit" value="Envoyer" class="btn btn-success">
    </div>
  </form>
</div>

<?php include 'templates/footer.php'; ?>
