<?php
session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();
    session_destroy();
    header('Location: /Zoo-Arcadia-New/login');
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
$styleRepository = new \Repositories\StyleRepository();
$scriptRepository = new \Repositories\ScriptRepository();

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
                header('Location: /Zoo-Arcadia-New/admin');
            } elseif ($userData['role_id'] == 2) {
                header('Location: /Zoo-Arcadia-New/employee');
            } elseif ($userData['role_id'] == 3) {
                header('Location: /Zoo-Arcadia-New/vet');
            } else {
                header('Location: /Zoo-Arcadia-New/home');
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

    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

$style = $styleRepository->loginStyle();
$script = $scriptRepository->loginScript();

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>
<div class="container custom" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <!-- Formulaire de connexion -->
    <form action="/Zoo-Arcadia-New/login" method="POST">
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
        <button type="submit" class="btn btn-success" name="login">Se connecter</button>
    </form>

    <!-- Lien pour mot de passe oublié -->
    <button class="btn btn-outline-danger mt-3" data-toggle="modal" data-target="#forgotPasswordModal">Mot de passe oublié ?</button>
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
                <form id="forgotPasswordForm" method="post" action="/Zoo-Arcadia-New/login">
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

<?php 
echo $script;
?>

<footer id="footerId" class="bg-light text-center text-lg-start mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <!-- Section Contact et Navigation -->
    <div class="containerr p-4">
        <div class="row">
            <div class="col-md-4">
                <h5>Nous contacter</h5>
                <ul class="navbar-nav">
                <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/contact">
                            <i class="fas fa-envelope mr-2"></i> Nous contacter
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/home#openhours">
                            <i class="fas fa-clock mr-2"></i> Nos horaires
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/aproposdenous">
                            <i class="fas fa-info-circle mr-2"></i> A propos de nous
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/mentions-legales">
                            <i class="fas fa-file-alt mr-2"></i> Mentions légales
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/politique-de-confidentialite">
                            <i class="fas fa-user-shield mr-2"></i> Politique de confidentialité
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Section Plan Google Maps -->
            <div class="col-md-4">
                <h5>Adresse</h5>
                <p>Forêt de Brocéliande, 35380 Paimpont</p>
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2665.098412817444!2d-2.2466591491221856!3d48.00743897921212!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x480ed92e0dbf4477%3A0x9e59e8de9302db5a!2s35380%20Paimpont%2C%20France!5e0!3m2!1sen!2sfr!4v1695648726871!5m2!1sen!2sfr" 
                    width="100%" 
                    height="200" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <!-- Section Réseaux sociaux -->
            <div class="col-md-4">
                <h5>Suivez-nous</h5>
                <div class="d-flex justify-content-center">
                    <a href="https://twitter.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-x-twitter fa-2x"></i>
                    </a>
                    <a href="https://facebook.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-facebook-f fa-2x"></i>
                    </a>
                    <a href="https://snapchat.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-snapchat-ghost fa-2x"></i>
                    </a>
                    <a href="https://instagram.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-instagram fa-2x"></i>
                    </a>
                    <a href="https://github.com" class="text-secondary mx-2" target="_blank" rel="noopener noreferrer">
                        <i class="fab fa-github fa-2x"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Copyright -->
    <div class="containerr p-4">
        <p class="text-secondary">
            <img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px" alt="Zoo Arcadia Favicon"> &copy; 2024 Zoo Arcadia. Tous droits réservés.
        </p>
    </div>
</footer>

<!-- Inclusion de FontAwesome (si non inclus déjà) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
<!-- Inclusion de jQuery (version complète, pas la version 'slim' qui ne supporte pas AJAX) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<!-- Inclusion de Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>

<!-- Inclusion de Bootstrap JS -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Inclusion de AXIOS -->
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<!-- Inclusion des scripts personnalisés -->
<script src="/Zoo-Arcadia-New/assets/js/scripts.js"></script>
</body>
</html>

