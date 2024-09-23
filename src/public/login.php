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

<footer id="footerId" class="bg-light text-center text-lg-start mt-5 fixed-bottom" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/contact"><img src="/Zoo-Arcadia-New/assets/image/lettre.png" width="32px" height="32px" alt="Nous contacter"> Nous contacter</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/home#openhours"><img src="/Zoo-Arcadia-New/assets/image/ouvert.png" width="32px" height="32px" alt="Nos horaires"> Nos horaires</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-secondary" href="/Zoo-Arcadia-New/aproposdenous"><img src="/Zoo-Arcadia-New/assets/image/a-propos-de-nous.png" width="32px" height="32px" alt="A propos de nous"> A propos de nous</a>
        </li>
    </ul>
    <div class="container p-4">
        <p class="text-secondary"><img src="/Zoo-Arcadia-New/assets/image/favicon.jpg" width="32px" height="32px" alt="Zoo Arcadia Favicon"> &copy; 2024 Zoo Arcadia. Tous droits réservés.</p>
    </div>
</footer>

<!-- Inclusion de jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="/Zoo-Arcadia-New/assets/js/scripts.js"></script>
</body>
</html>
