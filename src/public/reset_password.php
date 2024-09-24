<?php
session_start();

require '../../vendor/autoload.php';

// Vérifier si le token est présent dans l'URL
if (!isset($_GET['token'])) {
    die("Token manquant.");
}

$token = $_GET['token'];

// Connexion à la base de données
$db = (new \Database\DatabaseConnection())->connect();

// Initialisation des repositories
$userRepository = new \Repositories\UserRepository($db);

// Initialisation des services
$userService = new \Services\UserService($userRepository);

// Initialisation des contrôleurs
$userController = new \Controllers\UserController($userService);

// Vérifier si le token est valide avant de continuer
$userId = $userService->verifyPasswordResetToken($token);
if ($userId === false) { // Vérifiez explicitement que $userId est faux
    die("Token invalide ou expiré.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("Échec de la validation CSRF.");
    }

    // Récupération et validation du nouveau mot de passe
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword && $newPassword === $confirmPassword) {
        $result = $userController->resetPassword($token, $newPassword);

        if ($result && is_array($result) && $result['success']) {  // Vérifiez que $result est un tableau et que 'success' est présent
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "success";
            header('Location: /Zoo-Arcadia-New/login');
            exit;
        } else {
            $error = $result['message'] ?? 'Une erreur est survenue lors de la réinitialisation du mot de passe.';
        }
    } else {
        $error = "Les mots de passe ne correspondent pas.";
    }
}

// Générer un token CSRF unique s'il n'existe pas
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

include '../../src/views/templates/header.php';
include '../../src/views/templates/navbar_visitor.php';
?>

<div class="container mt-5" style="background: linear-gradient(to right, #ffffff, #ccedb6);">
    <br>
    <hr>
    <h1 class="my-4">Réinitialiser le mot de passe</h1>
    <hr>
    <br>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form action="/Zoo-Arcadia-New/reset_password?token=<?php echo urlencode($token); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
        <div class="form-group">
            <label for="new_password">Nouveau mot de passe</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirmer le mot de passe</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Réinitialiser le mot de passe</button>
    </form>
</div>

<?php include_once '../views/templates/footer.php';
