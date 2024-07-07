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
body {
    padding-top: 58px;
}
</style>

<div class="container">
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

<?php include_once 'templates/footer.php'; ?>