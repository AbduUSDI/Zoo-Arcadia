<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

// Générer un token CSRF unique s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Inclure les fichiers nécessaires
require '../../vendor/autoload.php';

// Connexion à la base de données
$db = (new \Database\DatabaseConnection())->connect();

// Initialisation des repositories
$userRepository = new \Repositories\UserRepository($db);

// Initialisation des services
$userService = new \Services\UserService($userRepository);

// Initialisation des contrôleurs
$userController = new \Controllers\UserController($userService);

// Gestion du formulaire de connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Validation et filtrage des entrées
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if ($email && $password) {
        $userData = $userController->getUserByEmail($email);

        if ($userData && password_verify($password, $userData['password'])) {
            $_SESSION['user'] = $userData;
            if ($userData['role_id'] == 1) {
                header('Location: ../views/admin/index.php');
            } elseif ($userData['role_id'] == 2) {
                header('Location: ../views/employee/index.php');
            } elseif ($userData['role_id'] == 3) {
                header('Location: ../views/vet/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    } else {
        $error = "Veuillez entrer une adresse email valide et un mot de passe.";
    }
}

// Gestion du formulaire de mot de passe oublié
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['forgotEmail'])) {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Validation et filtrage de l'email
    $email = filter_input(INPUT_POST, 'forgotEmail', FILTER_VALIDATE_EMAIL);

    if ($email) {
        $result = $userController->handleForgotPasswordRequest($email);

        if ($result['success']) {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = "Veuillez entrer une adresse email valide.";
        $_SESSION['message_type'] = "danger";
    }

    header('Location: login.php');
    exit;
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
    padding-top: 48px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
}
.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}
</style>

<div class="container mt-4" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Connexion</h1>
    <hr>
    <br>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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
    <hr>
</div>

<!-- Modale pour la réinitialisation du mot de passe -->
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
                <form id="forgotPasswordForm" method="post" action="login.php">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
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
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);

        const eyeIcon = this.querySelector('i');
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

<footer id="footerId" class="bg-light text-center text-lg-start mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="contact.php"><img src="../../assets/image/lettre.png" width="32px" height="32px"></img> Nous contacter</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="index.php#openhours"><img src="../../assets/image/ouvert.png" width="32px" height="32px"></img> Nos horaires</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="index.php#apropos"><img src="../../assets/image/a-propos-de-nous.png" width="32px" height="32px"></img> A propos de nous</a>
        </li>
    </ul>
    <div class="container p-4">
        <p class="text-secondary"><img src="../../assets/image/favicon.jpg" width="32px" height="32px"></img> &copy; 2024 Zoo Arcadia. Tous droits réservés.</p>
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

<script src="../../assets/js/scripts.js"></script>
</body>
</html>
