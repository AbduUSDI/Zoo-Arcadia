<?php
use Database\DatabaseConnection;
use Repositories\UserRepository;
use Services\UserService;
use Controllers\UserController;

require '../../vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'forgotEmail', FILTER_VALIDATE_EMAIL);

    if ($email) {
        // Connexion à la base de données
        $db = (new DatabaseConnection())->connect();
        
        // Initialisation des repositories, services et contrôleurs
        $userRepository = new UserRepository($db);
        $userService = new UserService($userRepository);
        $userController = new UserController($userService);

        // Début de la réinitialisation du mot de passe
        if ($userController->initiatePasswordReset($email)) {
            echo 'Un email de réinitialisation de mot de passe a été envoyé.';
        } else {
            echo "Aucun utilisateur trouvé avec cet email.";
        }
    } else {
        echo "Adresse email invalide.";
    }
}
