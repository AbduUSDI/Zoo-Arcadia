<?php
session_start();

require_once 'functions.php';

$database = new Database();
$db = $database->connect();
$user = new User($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $userData = $user->getUtilisateurParEmail($email);

    // Ici c'est pour controler la vérification et rediriger vers la bonne page en fonction du role_id de la base de données

    if ($userData && password_verify($password, $userData['password'])) {
        echo "Mot de passe vérifié.<br>";
        $_SESSION['user'] = $userData;
        if ($userData['role_id'] == 1) {
            header('Location: admin/index.php');
        } elseif ($userData['role_id'] == 2) {
            header('Location: employee/index.php');
        } elseif ($userData['role_id'] == 3) {
            header('Location: vet/index.php');
        } else {

            header('Location: index.php');
        }
        exit;

    // Message d'erreur en cas d'erreur, de mot de passe incorrect ou email incorrect

    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}

include_once 'templates/header.php';
include_once 'templates/navbar_visitor.php';
?>

<style>

h1,h2,h3 {
    text-align: center;
}

body {
    background-image: url('image/background.jpg');
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4">
    <h1 class="my-4">Connexion</h1>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" autocomplete="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" autocomplete="current-password" required>
                <div class="input-group-append">
                    <button class="btn btn-outline-primary" type="button" id="togglePassword"><i class="fas fa-eye"></i></button>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary" name="login">Se connecter</button>
    </form>
    <hr>
    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#forgotPasswordModal">Mot de passe oublié ?</button>   
</div>

<div class="modal fade" id="forgotPasswordModal" tabindex="-1" role="dialog" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="forgotPasswordModalLabel">Mot de passe oublié ?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="forgotPasswordForm" method="post" action="forgot_password.php">
                    <div class="form-group">
                        <label for="forgotEmail">Email</label>
                        <input type="email" class="form-control" id="forgotEmail" name="forgotEmail" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Envoyer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>

   // Exécute le script une fois que le DOM est entièrement chargé

document.addEventListener('DOMContentLoaded', function() {

    // Obtenir les éléments par leur ID
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    // Ajouter un écouteur d'événements pour le clic sur l'icône

    togglePassword.addEventListener('click', function() {
        // Modifie le type de l'input entre 'password' et 'text' quand on clic sur l'icône
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        // Chargement de l'icône FontAwesome à l'intérieur de l'élément cliqué grâce à une balise nommée "i"
        const eyeIcon = this.querySelector('i');
        
        // Modifications des classes FontAwesome pour l'icône de l'œil (barré/non barré)
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        } else {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        }
    });
});

</script>
    
<footer id="footerId" class="bg-light text-center text-lg-start mt-5 fixed-bottom" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" style="color: black;" href="contact.php">Nous contacter</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="color: black;" href="index.php#openhours">Nos horaires</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" style="color: black;" href="index.php#apropos">A propos de nous</a>
            </li>
            </ul>
        <div class="container p-4">
            <p>&copy; 2024 Zoo Arcadia. Tous droits réservés.</p>
        </div>
    </footer>
    <!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Inclusion de Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

    <!-- Inclusion de Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Inclusion de AXIOS -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script src="../js/scripts.js"></script>
</body>
</html>