<?php

session_start();

// Durée de vie de la session en secondes (30 minutes)
$sessionLifetime = 1800;

// Redirection si l'utilisateur n'est pas connecté ou n'a pas les droits d'accès admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role_id'] != 1) {
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

// Vérification de la durée de session
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $sessionLifetime)) {
    session_unset();  
    session_destroy(); 
    header('Location: /Zoo-Arcadia-New/login');
    exit;
}

$_SESSION['LAST_ACTIVITY'] = time();

require_once '../../../../vendor/autoload.php';

use Database\DatabaseConnection;
use Repositories\UserRepository;
use Services\UserService;
use Controllers\UserController;

// Connexion à la base de données
$dbConnection = new DatabaseConnection();
$conn = $dbConnection->connect();

$userRepository = new UserRepository($conn);
$userService = new UserService($userRepository);
$userController = new UserController($userService);

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Gestion des actions AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? null;
    if ($action) {
        if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            // Récupération et validation des données
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = $_POST['password'];
            $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $username = htmlspecialchars($_POST['username']);

            if ($email && $password && $role_id && $username) {
                $userController->addUser($email, $password, $role_id, $username);
                echo "Utilisateur ajouté avec succès.";
            } else {
                echo "Erreur dans les données saisies.";
            }
            exit;
        } elseif ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
            // Vérification du token CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            // Récupération et validation des données
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $role_id = filter_input(INPUT_POST, 'role_id', FILTER_VALIDATE_INT);
            $username = htmlspecialchars($_POST['username']);

            if ($id && $email && $role_id && $username) {
                $userController->updateUser($id, $email, $role_id, $username, $password);
                echo "Utilisateur modifié avec succès.";
            } else {
                echo "Erreur dans les données saisies.";
            }
            exit;
        } elseif ($action === 'get' && isset($_GET['id'])) {
            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $user = $userController->getUserById($id);
                echo json_encode($user);
            } else {
                echo json_encode(['error' => 'ID invalide']);
            }
            exit;
        } elseif ($action === 'delete' && isset($_GET['id'])) {
            // Vérification du token CSRF pour la suppression
            if (!isset($_GET['csrf_token']) || $_GET['csrf_token'] !== $_SESSION['csrf_token']) {
                die("Échec de la validation CSRF.");
            }

            $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $userController->deleteUser($id);
                echo "Utilisateur supprimé avec succès.";
            } else {
                echo "Erreur: ID invalide.";
            }
            exit;
        }
    }
}

$scriptRepository = new Repositories\ScriptRepository;
$script = $scriptRepository->manageUserScript();

$users = $userController->getAllUsers();

include_once '../../../../src/views/templates/header.php';
include_once '../navbar_admin.php';
?>

<div class="user-management-container mt-5 container">
    <h1 class="user-management-title">Gérer les utilisateurs</h1>
    <div class="user-management-table-wrapper table-responsive">
        <a href="javascript:void(0);" class="btn btn-success user-management-add-btn" data-toggle="modal" data-target="#addUserModal">Ajouter un utilisateur</a>
        <table class="user-management-table">
            <thead class="user-management-table-header">
                <tr>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['role_id'] == 1 ? 'Admin' : ($user['role_id'] == 2 ? 'Employé' : 'Vétérinaire')); ?></td>
                        <td>
                            <a href="javascript:void(0);" class="btn btn-warning btn-sm user-management-edit-btn" data-id="<?php echo $user['id']; ?>" data-toggle="modal" data-target="#editUserModal">Modifier</a>
                            <a href="javascript:void(0);" class="btn btn-danger btn-sm user-management-delete-btn" data-id="<?php echo $user['id']; ?>" data-csrf_token="<?php echo $_SESSION['csrf_token']; ?>">Supprimer</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modals pour Ajouter et Modifier -->
<div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Ajouter un utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="addUserForm">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="addEmail">Email</label>
                        <input type="email" class="form-control user-management-input" id="addEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="addPassword">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control user-management-input" id="addPassword" name="password" required>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="addUsername">Nom d'utilisateur</label>
                        <input type="text" class="form-control user-management-input" id="addUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="addRole">Rôle</label>
                        <select class="form-control user-management-input" id="addRole" name="role_id" required>
                            <option value="1">Admin</option>
                            <option value="2">Employé</option>
                            <option value="3">Vétérinaire</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success user-management-add-confirm-btn">Ajouter</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Modifier un utilisateur</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editUserForm">
                    <input type="hidden" id="editUserId" name="id">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="form-group">
                        <label for="editEmail">Email</label>
                        <input type="email" class="form-control user-management-input" id="editEmail" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="editPassword">Mot de passe</label>
                        <div class="input-group">
                            <input type="password" class="form-control user-management-input" id="editPassword" name="password">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary toggle-password" type="button">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="editUsername">Nom d'utilisateur</label>
                        <input type="text" class="form-control user-management-input" id="editUsername" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="editRole">Rôle</label>
                        <select class="form-control user-management-input" id="editRole" name="role_id" required>
                            <option value="1">Admin</option>
                            <option value="2">Employé</option>
                            <option value="3">Vétérinaire</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-warning user-management-edit-confirm-btn">Modifier</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
echo $script;
?>

<?php include '../../../../src/views/templates/footerconnected.php'; ?>
